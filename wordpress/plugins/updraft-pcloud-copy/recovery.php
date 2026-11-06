<?php
namespace Updraft_PCloud_Copy;

defined('ABSPATH') || exit;

function cloud_folder(array $connection, int $id, bool $recursive = false, array $allow = []): array {
    $params = ['folderid' => $id];
    if ($recursive) {
        $params['recursive'] = 1;
    }
    $result = api($connection['region'], 'listfolder', $params, token_from($connection), $allow);
    if (!empty($result['result'])) {
        return [];
    }
    $folder = $result['metadata'] ?? [];
    if (empty($folder['isfolder']) || !isset($folder['contents']) || !is_array($folder['contents'])) {
        throw new \RuntimeException('pCloud gaf geen bruikbare mapinhoud terug.');
    }
    return $folder;
}

function cloud_children(array $folder): array {
    return array_column($folder['contents'], null, 'name');
}

function remote_catalog(array $connection): array {
    $root = cloud_children(cloud_folder($connection, 0));
    $parent = $root[backup_parent_name()] ?? [];
    $parent_id = (int) ($parent['folderid'] ?? 0);
    $backups = [];
    if ($parent_id > 0 && !empty($parent['isfolder'])) {
        $tree = cloud_folder($connection, $parent_id, true);
        $parent['path'] = $tree['path'] ?? $parent['path'] ?? '';
        foreach (cloud_children($tree) as $name => $child) {
            if (empty($child['isfolder']) || (int) ($child['parentfolderid'] ?? 0) !== $parent_id ||
                !preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_UTC_([a-f0-9]{12})$/D', (string) $name, $parts)) {
                continue;
            }
            $marker = cloud_children(['contents' => $child['contents'] ?? []])['VOLTOOID.json'] ?? [];
            $id = (int) ($child['folderid'] ?? 0);
            if ($id > 0 && (int) ($marker['fileid'] ?? 0) > 0) {
                $backups[$id] = ['name' => $name, 'nonce' => $parts[1], 'id' => $id];
            }
        }
    }
    uasort($backups, static fn ($left, $right) => strcmp($right['name'], $left['name']));
    return [
        'account' => $connection['account'], 'path' => $parent['path'] ?? '', 'found' => $parent_id > 0,
        'backups' => $backups,
    ];
}

function get_download(array $connection, int $file_id, int $limit, string $destination = '') {
    $link = api($connection['region'], 'getfilelink', ['fileid' => $file_id], token_from($connection));
    $path = $link['path'] ?? '';
    $host = $link['hosts'][0] ?? '';
    if (!is_string($host) || !preg_match('/^[a-z0-9-]+(?:\.[a-z0-9-]+)*\.pcloud\.com$/Di', $host) ||
        !is_string($path) || !str_starts_with($path, '/') || str_starts_with($path, '//') || preg_match('/[\x00-\x20]/', $path)) {
        throw new \RuntimeException('pCloud gaf geen geldige downloadlocatie terug.');
    }
    $args = ['timeout' => 90, 'redirection' => 0, 'limit_response_size' => $limit];
    if ($destination !== '') {
        $args['stream'] = true;
        $args['filename'] = $destination;
    }
    $response = wp_safe_remote_get('https://' . $host . $path, $args);
    if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
        throw new \RuntimeException('Het bestand kon niet veilig van pCloud worden opgehaald.');
    }
    return $destination === '' ? wp_remote_retrieve_body($response) : null;
}

function recovery_job(array $connection, int $folder_id, bool $force_download = false): array {
    $catalog = remote_catalog($connection);
    $set = $catalog['backups'][$folder_id] ?? null;
    if (!$set) {
        throw new \RuntimeException('Dit herstelpunt staat niet in de pCloud-map van deze website.');
    }
    $children = cloud_children(cloud_folder($connection, $folder_id));
    $marker = $children['VOLTOOID.json'] ?? [];
    $marker_id = (int) ($marker['fileid'] ?? 0);
    $marker_size = (int) ($marker['size'] ?? 0);
    if ($marker_id < 1 || $marker_size < 1 || $marker_size > 128 * 1024) {
        throw new \RuntimeException('Het herstelpunt heeft geen bruikbare voltooiingslijst.');
    }
    $body = get_download($connection, $marker_id, $marker_size + 1);
    $manifest = json_decode($body, true);
    $hashes = $manifest['files_sha1'] ?? null;
    if (strlen($body) !== $marker_size || !is_array($hashes) || !$hashes || count($hashes) > 1000) {
        throw new \RuntimeException('De bestandenlijst van het herstelpunt is ongeldig.');
    }
    $files = [];
    foreach ($hashes as $name => $sha1) {
        $file = $children[$name] ?? [];
        $size = (int) ($file['size'] ?? 0);
        $id = (int) ($file['fileid'] ?? 0);
        if (!valid_archive_name($name, $set['nonce']) || !is_string($sha1) || !preg_match('/^[a-f0-9]{40}$/D', $sha1) ||
            $id < 1 || $size < 1 || $size === PHP_INT_MAX || (int) ($file['parentfolderid'] ?? 0) !== $folder_id) {
            throw new \RuntimeException('Een archiefdeel ontbreekt of staat niet goed in de bestandenlijst.');
        }
        $files[$name] = ['fileid' => $id, 'size' => $size, 'sha1' => $sha1];
    }
    $stage = '.upc-recovery-' . wp_generate_password(24, false, false);
    $path = backup_directory() . '/' . $stage;
    if (!mkdir($path, 0700)) {
        throw new \RuntimeException('WordPress kon geen tijdelijke herstelmap maken.');
    }
    if (false === file_put_contents($path . '/.htaccess', "Deny from all\n") ||
        false === file_put_contents($path . '/index.html', '')) {
        throw new \RuntimeException('WordPress kon de tijdelijke herstelmap niet afschermen.');
    }
    return $set + [
        'account' => $connection['account'], 'files' => $files, 'stage' => $stage,
        'confirmed' => [], 'attempts' => 0, 'next' => 0, 'error' => '',
        'force_download' => $force_download,
    ];
}

function recovery_stage(array $job): string {
    $dir = backup_directory();
    $path = $dir . '/' . $job['stage'];
    if (is_link($path) || realpath($path) !== $path || !is_dir($path)) {
        throw new \RuntimeException('De tijdelijke herstelmap is niet meer beschikbaar.');
    }
    return $path;
}

function recovery_next(array &$job, array $connection): bool {
    $stage = recovery_stage($job);
    $remaining = array_diff_key($job['files'], $job['confirmed']);
    if ($remaining) {
        $name = array_key_first($remaining);
        $file = $remaining[$name];
        $target = $stage . '/' . $name;
        $local = backup_directory() . '/' . $name;
        if (file_exists($local) || is_link($local)) {
            verify_local_archive($local, $file);
            if (empty($job['force_download'])) {
                $job['confirmed'][$name] = 'local';
                return false;
            }
        }
        $temporary = $target . '.part';
        if (is_link($temporary)) {
            throw new \RuntimeException('Het tijdelijke downloadpad is ongeldig.');
        }
        try {
            get_download($connection, $file['fileid'], $file['size'] + 1, $temporary);
            verify_local_archive($temporary, $file);
            if (!rename($temporary, $target)) {
                throw new \RuntimeException('WordPress kon het geverifieerde archiefdeel niet opslaan.');
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
        $job['confirmed'][$name] = 'downloaded';
        return false;
    }

    if (!class_exists('UpdraftPlus_Backup_History')) {
        throw new \RuntimeException('Activeer UpdraftPlus voordat je het herstelpunt toevoegt.');
    }
    $dir = backup_directory();
    foreach ($job['files'] as $name => $file) {
        $destination = $dir . '/' . $name;
        $source = $stage . '/' . $name;
        if (file_exists($destination) || is_link($destination)) {
            verify_local_archive($destination, $file);
        } else {
            verify_local_archive($source, $file);
        }
    }
    foreach ($job['files'] as $name => $file) {
        $destination = $dir . '/' . $name;
        if (!file_exists($destination) && !is_link($destination) && !link($stage . '/' . $name, $destination)) {
            throw new \RuntimeException('WordPress kon een archiefdeel niet aan de UpdraftPlus-map toevoegen.');
        }
        verify_local_archive($destination, $file);
    }
    \UpdraftPlus_Backup_History::rebuild(false);
    return true;
}

function verify_local_archive(string $path, array $file): void {
    clearstatcache(true, $path);
    if (is_link($path) || !is_file($path) || filesize($path) !== $file['size'] ||
        !hash_equals($file['sha1'], (string) hash_file('sha1', $path))) {
        throw new \RuntimeException('Een lokaal archiefdeel wijkt af van de gecontroleerde pCloud-kopie.');
    }
}

function clean_recovery_stage(array $job): void {
    $stage = recovery_stage($job);
    foreach (array_keys($job['files']) as $name) {
        foreach ([$stage . '/' . $name, $stage . '/' . $name . '.part'] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
    unlink($stage . '/.htaccess');
    unlink($stage . '/index.html');
    rmdir($stage);
}

function managed_backup_type(array $entry): ?string {
    $type = $entry['type'] ?? null;
    if (in_array($type, ['database', 'full', 'files'], true)) {
        return $type;
    }
    // Existing uploads recorded their archive names before storing the type.
    $nonce = substr($entry['name'], -12);
    $archives = array_diff($entry['files'], ['VOLTOOID.json']);
    $has_database = false;
    $has_files = false;
    foreach ($archives as $name) {
        if (!valid_archive_name($name, $nonce)) {
            return null;
        }
        if (preg_match('/-db\.gz(?:\.crypt)?$/Di', $name)) {
            $has_database = true;
        } else {
            $has_files = true;
        }
    }
    return $has_database ? ($has_files ? 'full' : 'database') : ($has_files ? 'files' : null);
}

function retention_expiry(\DateTimeImmutable $uploaded, array $rule): \DateTimeImmutable {
    $amount = $rule['amount'];
    $unit = $rule['unit'];
    if ('days' === $unit || 'weeks' === $unit) {
        return $uploaded->modify("+$amount $unit");
    }

    $months = 'years' === $unit ? $amount * 12 : $amount;
    $month = $uploaded->modify('first day of this month')->modify("+$months months");
    $day = min((int) $uploaded->format('j'), (int) $month->format('t'));
    return $month->setDate((int) $month->format('Y'), (int) $month->format('n'), $day);
}

function retention_targets(array $managed, int $keep, array $rules, \DateTimeImmutable $now): array {
    if (!$rules && ($keep < 1 || count($managed) <= $keep)) {
        return [];
    }
    uasort($managed, static fn ($left, $right) =>
        ($right['uploaded_at'] <=> $left['uploaded_at']) ?: strcmp($right['name'], $left['name']));
    if (!$rules) {
        return array_fill_keys(array_keys(array_slice($managed, $keep, null, true)), null);
    }

    $latest = [];
    $targets = [];
    foreach ($managed as $id => $entry) {
        $type = managed_backup_type($entry);
        if ($type === null || !isset($rules[$type]) || $entry['uploaded_at'] < 1) {
            continue;
        }
        if (!isset($latest[$type])) {
            $latest[$type] = $id;
        } elseif (retention_expiry($now->setTimestamp($entry['uploaded_at']), $rules[$type]) <= $now) {
            $targets[$id] = $latest[$type];
        }
    }
    return array_reverse($targets, true);
}

function verify_managed_folder(array $connection, int $id, array $entry): bool {
    $folder = cloud_folder($connection, $id, false, [2005]);
    if (!$folder) {
        return false;
    }
    $actual = cloud_children($folder);
    $expected = $entry['files'];
    $names = array_keys($actual);
    sort($expected);
    sort($names);
    if (($folder['name'] ?? '') !== $entry['name'] ||
        (int) ($folder['parentfolderid'] ?? 0) !== $entry['parent_id'] ||
        empty($folder['ismine']) || !empty($folder['isshared']) || $expected !== $names) {
        throw new \RuntimeException('Opruimen gestopt. Een pCloud-map wijkt af van de verwachte inhoud.');
    }
    foreach ($actual as $child) {
        if (!empty($child['isfolder']) || !empty($child['isshared']) || empty($child['ismine'])) {
            throw new \RuntimeException('Opruimen gestopt. Een pCloud-map bevat gedeelde of extra inhoud.');
        }
    }
    $marker = api($connection['region'], 'checksumfile', ['fileid' => (int) $actual['VOLTOOID.json']['fileid']], token_from($connection));
    if (!hash_equals($entry['manifest'], (string) ($marker['sha1'] ?? ''))) {
        throw new \RuntimeException('Opruimen gestopt. De voltooiingslijst van een pCloud-map is gewijzigd.');
    }
    return true;
}

function prune_managed_backups(array $connection, array &$managed, array $targets): void {
    foreach ($targets as $id => $protected) {
        if (!verify_managed_folder($connection, (int) $id, $managed[$id])) {
            unset($managed[$id]);
            continue;
        }
        if ($protected !== null && !verify_managed_folder($connection, (int) $protected, $managed[$protected])) {
            throw new \RuntimeException('Opruimen gestopt. Het nieuwste herstelpunt van deze soort ontbreekt in pCloud.');
        }
        api($connection['region'], 'deletefolderrecursive', ['folderid' => (int) $id], token_from($connection), [2005]);
        unset($managed[$id]);
        return;
    }
}

function handle_recovery(string $action, array $state, array $connection): string {
    token_from($connection);
    $job = $state['recovery'] ?? null;
    if ('scan' === $action) {
        $state['catalog'] = remote_catalog($connection);
        $notice = $state['catalog']['found'] ? 'pCloud-map gecontroleerd.' : 'De doelmap bestaat nog niet in pCloud.';
    } elseif ('recover' === $action) {
        if ($job) {
            throw new \RuntimeException('Er staat al een terughaalopdracht in de wachtrij.');
        }
        $id = filter_var(field('remote_backup'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) {
            throw new \RuntimeException('Kies een herstelpunt uit pCloud.');
        }
        $state['recovery'] = recovery_job($connection, $id, '1' === field('force_download'));
        unset($state['recovered']);
        $notice = 'Herstelpunt staat klaar om naar de lokale UpdraftPlus-map terug te halen.';
    } elseif (!$job || $job['account'] !== $connection['account']) {
        throw new \RuntimeException('Er is geen passende terughaalopdracht.');
    } elseif ('recover_cancel' === $action) {
        clean_recovery_stage($job);
        unset($state['recovery']);
        $notice = 'Terughalen afgebroken. Alleen de tijdelijke downloadmap is opgeruimd.';
    } else {
        $state['recovery']['attempts'] = 0;
        $state['recovery']['next'] = 0;
        $state['recovery']['error'] = '';
        $notice = 'Terughalen wordt opnieuw geprobeerd.';
    }
    update_option(STATE, $state, false);
    return $notice;
}

function process_recovery(array $state, array $job, array $connection): void {
    ++$job['attempts'];
    $job['next'] = time() + 5 * MINUTE_IN_SECONDS;
    $job['error'] = 'De vorige download is gestart. Na een onderbreking volgt een nieuwe poging.';
    $state['recovery'] = $job;
    update_option(STATE, $state, false);
    try {
        if ($job['account'] !== $connection['account']) {
            throw new \RuntimeException('De terughaalopdracht hoort bij een ander pCloud-account.');
        }
        if (recovery_next($job, $connection)) {
            $state['recovered'] = ['name' => $job['name'], 'time' => time()];
            if (!in_array(true, $job['confirmed'], true)) {
                $counts = array_count_values($job['confirmed']);
                $state['recovered']['downloaded'] = $counts['downloaded'] ?? 0;
                $state['recovered']['local'] = $counts['local'] ?? 0;
            }
            unset($state['recovery']);
            update_option(STATE, $state, false);
            clean_recovery_stage($job);
            return;
        }
        $job['attempts'] = 0;
        $job['next'] = 0;
        $job['error'] = '';
    } catch (\RuntimeException $error) {
        if (!isset($state['recovery'])) {
            return;
        }
        $job['error'] = $error->getMessage();
    }
    $state['recovery'] = $job;
    update_option(STATE, $state, false);
}

function apply_retention(array $state, array $connection, array $config, \DateTimeImmutable $now): void {
    $account = $connection['account'];
    $today = $now->format('Y-m-d');
    $pending = ($state['retention_pending'] ?? '') === $account;
    $due = array_filter($config['retention']) && ($state['retention_date'][$account] ?? '') !== $today;
    if ((!$pending && !$due) || !empty($state['recovery'])) {
        return;
    }
    $managed = $state['managed'][$account] ?? [];
    $previous_count = count($managed);
    $targets = retention_targets($managed, (int) $config['keep'], $config['retention'], $now);
    unset($state['retention_pending']);
    try {
        prune_managed_backups($connection, $managed, $targets);
        unset($state['retention_error']);
        if (array_intersect_key($targets, $managed)) {
            $state['retention_pending'] = $account;
        }
    } catch (\RuntimeException $error) {
        $state['retention_error'] = $error->getMessage();
    }
    $state['retention_date'][$account] = $today;
    $state['managed'][$account] = $managed;
    if ($pending || count($managed) !== $previous_count) {
        unset($state['catalog']);
    }
    update_option(STATE, $state, false);
}
