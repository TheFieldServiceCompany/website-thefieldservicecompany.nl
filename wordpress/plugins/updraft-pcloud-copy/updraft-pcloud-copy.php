<?php
/**
 * Plugin Name: TFSC Updraft to PCloud
 * Description: Kopieert lokale UpdraftPlus-back-ups op een ingesteld tijdstip naar pCloud.
 * Version: 0.2.3
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * License: GPL-2.0-or-later
 */
namespace Updraft_PCloud_Copy;

defined('ABSPATH') || exit;

const SETTINGS = 'upc_settings';
const CONNECTION = 'upc_connection';
const CONNECTION_STATUS = 'upc_connection_status';
const STATE = 'upc_state';
const SOURCE_HISTORY = 'upc_source_history';
const TICK = 'upc_upload_tick';
const INTERVAL = 'upc_every_minute';
const WEEKDAYS = ['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'];

require_once __DIR__ . '/recovery.php';

add_filter('cron_schedules', static function ($schedules) {
    $schedules[INTERVAL] = ['interval' => MINUTE_IN_SECONDS, 'display' => 'TFSC Updraft to PCloud'];
    return $schedules;
});

register_activation_hook(__FILE__, static function ($network_wide) {
    if (is_multisite() || $network_wide || !extension_loaded('curl') || !extension_loaded('openssl')) {
        wp_die('Deze versie vereist een losse WordPress-installatie met PHP cURL en OpenSSL.');
    }
    if (!wp_next_scheduled(TICK)) {
        $result = wp_schedule_event(time() + MINUTE_IN_SECONDS, INTERVAL, TICK, [], true);
        if (is_wp_error($result) || !$result) {
            wp_die('WordPress kon de geplande uploadcontrole niet registreren.');
        }
    }
});

register_deactivation_hook(__FILE__, static function () {
    wp_clear_scheduled_hook(TICK);
});

add_action(TICK, __NAMESPACE__ . '\\tick');
add_action('admin_menu', static function () {
    add_management_page('TFSC Updraft to PCloud', 'TFSC Updraft to PCloud', 'manage_options', 'updraft-pcloud-copy', __NAMESPACE__ . '\\render');
});
add_action('admin_enqueue_scripts', static function ($hook) {
    if ('tools_page_updraft-pcloud-copy' === $hook) {
        wp_enqueue_style('upc-admin', plugins_url('admin.css', __FILE__), [], '0.2.3');
    }
});
add_filter('updraftplus_save_last_backup', __NAMESPACE__ . '\\record_successful_backup');
add_action('admin_post_upc_change', __NAMESPACE__ . '\\handle_admin');

function settings(): array {
    return wp_parse_args(get_option(SETTINGS, []), [
        'enabled' => false, 'day' => '7', 'time' => '04:00', 'time_second' => '', 'client' => '',
        'source' => 'any', 'keep' => 0,
    ]);
}

function api_host(string $region): string {
    return 'US' === $region ? 'api.pcloud.com' : 'eapi.pcloud.com';
}

function secret_key(): string {
    return hash('sha256', wp_salt('auth') . "\0" . wp_salt('secure_auth') . "\0upc-pcloud-token", true);
}

function token_from(array $connection): string {
    $bytes = base64_decode($connection['sealed'] ?? '', true);
    if (!$bytes || strlen($bytes) <= 28) {
        throw new \RuntimeException('Koppel eerst je pCloud-account.');
    }
    $token = openssl_decrypt(substr($bytes, 28), 'aes-256-gcm', secret_key(), OPENSSL_RAW_DATA, substr($bytes, 0, 12), substr($bytes, 12, 16));
    if (!$token) {
        throw new \RuntimeException('De pCloud-koppeling kan niet worden gelezen. Koppel je account opnieuw.');
    }
    return $token;
}

function api(string $region, string $method, array $body, string $token = '', array $allow = []): array {
    $response = wp_remote_post('https://' . api_host($region) . '/' . $method, [
        'timeout' => 12, 'redirection' => 0, 'body' => $body,
        'headers' => $token ? ['Authorization' => 'Bearer ' . $token] : [],
    ]);
    if (is_wp_error($response)) {
        throw new \RuntimeException('WordPress could not reach the pCloud API.');
    }
    return api_result(wp_remote_retrieve_response_code($response), wp_remote_retrieve_body($response), $method, $allow);
}

function api_result(int $http, string $body, string $method, array $allow = []): array {
    $data = json_decode($body, true);
    if ($http < 200 || $http >= 300 || !is_array($data) || !isset($data['result'])) {
        throw new \RuntimeException(sprintf('Invalid response from pCloud for %s (HTTP %d).', $method, $http));
    }
    $result = (int) $data['result'];
    if ($result && !in_array($result, $allow, true)) {
        throw new \RuntimeException(sprintf('pCloud rejected %s (code %d).', $method, $result));
    }
    return $data;
}

function connect(array $config, array $state): void {
    $region = field('region');
    $code = field('code');
    $secret = field('secret');
    if (!in_array($region, ['EU', 'US'], true) || !$config['client'] || !$code || !$secret) {
        throw new \RuntimeException('Choose the storage region and enter the Client ID, Client Secret and Access Code.');
    }
    $data = api($region, 'oauth2_token', [
        'client_id' => $config['client'], 'client_secret' => $secret, 'code' => $code,
    ]);
    $token = $data['access_token'] ?? '';
    if (!is_string($token) || !preg_match('/^[A-Za-z0-9._~+\/=:-]+$/D', $token)) {
        throw new \RuntimeException('The pCloud response did not contain a valid access token.');
    }
    // The pCloud reference uses uid, while its SDK example uses userid.
    $id_options = ['options' => ['min_range' => 1]];
    $user_id = filter_var($data['uid'] ?? null, FILTER_VALIDATE_INT, $id_options)
        ?: filter_var($data['userid'] ?? null, FILTER_VALIDATE_INT, $id_options);
    if (false === $user_id) {
        $profile = api($region, 'userinfo', [], $token);
        $user_id = filter_var($profile['userid'] ?? null, FILTER_VALIDATE_INT, $id_options);
        if (false === $user_id) {
            throw new \RuntimeException('pCloud issued an access token, but the account ID could not be verified.');
        }
    }
    $account = $region . ':' . $user_id;
    if ((!empty($state['job']) && $state['job']['account'] !== $account) ||
        (!empty($state['recovery']) && $state['recovery']['account'] !== $account)) {
        throw new \RuntimeException('A transfer is still queued for a different pCloud account.');
    }
    $iv = random_bytes(12);
    $ciphertext = openssl_encrypt($token, 'aes-256-gcm', secret_key(), OPENSSL_RAW_DATA, $iv, $tag);
    if (false === $ciphertext) {
        throw new \RuntimeException('The pCloud access token could not be encrypted.');
    }
    if (!update_option(CONNECTION, [
        'account' => $account, 'region' => $region, 'sealed' => base64_encode($iv . $tag . $ciphertext),
    ], false)) {
        throw new \RuntimeException('WordPress could not save the pCloud connection.');
    }
}

function valid_backup_id($id): bool {
    return is_string($id) && 1 === preg_match('/^[a-f0-9]{12}$/D', $id);
}

function valid_archive_name($name, string $id): bool {
    return is_string($name) && valid_backup_id($id)
        && !str_contains($name, '\\')
        && 1 === preg_match('~^backup_[^/\x00-\x1f]+_' . $id . '-[a-z0-9-]+\.(?:zip|gz(?:\.crypt)?)$~Di', $name);
}

function record_successful_backup(array $last): array {
    if (!empty($last['success']) && valid_backup_id($last['backup_nonce'] ?? '')) {
        $history = get_option(SOURCE_HISTORY, []);
        unset($history[$last['backup_nonce']]);
        $history[$last['backup_nonce']] = array_intersect_key($last, array_flip(['success', 'backup_nonce', 'backup_time', 'backup_array']));
        update_option(SOURCE_HISTORY, array_slice($history, -30, null, true), false);
    }
    return $last;
}

function available_backups(): array {
    global $updraftplus;
    if (!is_object($updraftplus) || !class_exists('UpdraftPlus_Options') || !method_exists($updraftplus, 'get_backupable_file_entities')) {
        throw new \RuntimeException('Activeer eerst UpdraftPlus.');
    }
    $history = get_option(SOURCE_HISTORY, []);
    $last = \UpdraftPlus_Options::get_updraft_option('updraft_last_backup', []);
    if (!empty($last['success']) && valid_backup_id($last['backup_nonce'] ?? '')) {
        $history[$last['backup_nonce']] = $last;
    }
    $entities = array_keys($updraftplus->get_backupable_file_entities());
    $sets = [];
    foreach ($history as $entry) {
        try {
            $backup = local_backup($entry, $entities);
            $sets[$backup['id']] = $backup;
        } catch (\RuntimeException $error) {
            // UpdraftPlus can prune local archives independently.
        }
    }
    uasort($sets, static fn ($left, $right) => $right['timestamp'] <=> $left['timestamp']);
    return $sets;
}

function latest_backup(string $type = 'any', string $id = ''): array {
    foreach (available_backups() as $backup) {
        if (($id === '' || $backup['id'] === $id) && ($type === 'any' || $backup['type'] === $type)) {
            return $backup;
        }
    }
    throw new \RuntimeException('Geen geschikte geslaagde UpdraftPlus-back-up met alle archiefdelen op de server gevonden.');
}

function local_backup(array $entry, array $entities): array {
    $id = $entry['backup_nonce'];
    $files = [];
    $has_database = false;
    $has_files = false;
    foreach (array_merge($entities, ['db']) as $entity) {
        foreach ((array) ($entry['backup_array'][$entity] ?? []) as $name) {
            if (!valid_archive_name($name, $id) || isset($files[$name])) {
                throw new \RuntimeException('UpdraftPlus gaf een onverwachte archiefnaam terug.');
            }
            $size = filesize(local_file($name));
            if (false === $size || $size < 1) {
                throw new \RuntimeException('Een archiefdeel is leeg of onleesbaar.');
            }
            $files[$name] = $size;
            $has_database = $has_database || 'db' === $entity;
            $has_files = $has_files || 'db' !== $entity;
        }
    }
    if (!$files || empty($entry['backup_time'])) {
        throw new \RuntimeException('De back-up bevat geen bruikbare archiefdelen.');
    }
    $type = $has_database ? ($has_files ? 'full' : 'database') : 'files';
    return ['id' => $id, 'timestamp' => (int) $entry['backup_time'], 'files' => $files, 'type' => $type];
}

function backup_directory(): string {
    global $updraftplus;
    if (!is_object($updraftplus) || !method_exists($updraftplus, 'backups_dir_location')) {
        throw new \RuntimeException('Activeer eerst UpdraftPlus.');
    }
    $dir = realpath($updraftplus->backups_dir_location());
    if (!$dir || !is_dir($dir)) {
        throw new \RuntimeException('De lokale UpdraftPlus-map is niet beschikbaar.');
    }
    return $dir;
}

function local_file(string $name): string {
    $dir = backup_directory();
    $path = realpath($dir . DIRECTORY_SEPARATOR . $name);
    if (!$path || dirname($path) !== $dir || basename($path) !== $name || !is_file($path) || !is_readable($path)) {
        throw new \RuntimeException('Een lokaal back-uponderdeel ontbreekt. Bewaar de UpdraftPlus-archieven op de server.');
    }
    return $path;
}

function locked(callable $task, bool $admin = false): void {
    global $wpdb;
    $name = 'upc_' . md5(DB_NAME . ':' . $wpdb->prefix);
    $result = $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0)', $name));
    if ('1' !== (string) $result) {
        if ($admin) {
            throw new \RuntimeException('Er loopt al een upload of de hosting kan geen uploadvergrendeling nemen. Probeer het over een minuut opnieuw.');
        }
        return;
    }
    try {
        $task();
    } finally {
        $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $name));
    }
}

function backup_parent_name(): string {
    $url = home_url();
    $host = sanitize_title((string) wp_parse_url($url, PHP_URL_HOST));
    return 'WordPress-backups-' . $host . '-' . substr(hash('sha256', $url), 0, 8);
}

function new_job(array $backup, array $connection): array {
    return $backup + [
        'parent' => backup_parent_name(),
        'account' => $connection['account'], 'confirmed' => [], 'attempts' => 0,
        'next' => 0, 'error' => '',
        'folder' => gmdate('Y-m-d_H-i-s', $backup['timestamp']) . '_UTC_' . $backup['id'],
    ];
}

function schedule_days($stored): array {
    // Earlier versions stored a single ISO weekday or the string 'daily'.
    return 'daily' === $stored ? range(1, count(WEEKDAYS)) : array_map('intval', (array) $stored);
}

function due_upload_slot(array $config, \DateTimeImmutable $now, array $state): ?string {
    if (!$config['enabled'] || !in_array((int) $now->format('N'), schedule_days($config['day']), true)) {
        return null;
    }

    $time = $now->format('H:i');
    if ($time < $config['time']) {
        return null;
    }
    $second = $config['time_second'];
    $date = $now->format('Y-m-d');
    $slot = $date . ' ' . ($second !== '' && $time >= $second ? $second : $config['time']);
    $last_slot = $state['schedule_slot'] ?? '';
    // A run recorded by an earlier version already used today's schedule.
    if ($last_slot >= $slot || ($last_slot === '' && ($state['schedule_date'] ?? '') === $date)) {
        return null;
    }
    return $slot;
}

function tick(bool $manual = false): void {
    if (!get_option(CONNECTION)) {
        return;
    }
    locked(static function () use ($manual) {
        $config = settings();
        $connection = get_option(CONNECTION, []);
        $state = get_option(STATE, []);
        $now = current_datetime();
        $job = $state['job'] ?? null;
        $recovery = $state['recovery'] ?? null;
        if ($recovery && $recovery['attempts'] < 5) {
            if ($manual || $recovery['next'] <= time()) {
                process_recovery($state, $recovery, $connection);
            }
            return;
        }

        if (!$manual && (!$job || $job['attempts'] >= 5) && ($slot = due_upload_slot($config, $now, $state)) !== null) {
            try {
                $backup = latest_backup($config['source']);
                $same_failed_job = $job && $job['id'] === $backup['id'];
                $already_sent = ($state['done'][$connection['account']]['id'] ?? '') === $backup['id'];
                if (!$same_failed_job && !$already_sent) {
                    $job = new_job($backup, $connection);
                    $state['job'] = $job;
                    $state['schedule_date'] = $now->format('Y-m-d');
                    $state['schedule_slot'] = $slot;
                    unset($state['schedule_error']);
                    update_option(STATE, $state, false);
                }
            } catch (\RuntimeException $error) {
                $state['schedule_error'] = $error->getMessage();
                update_option(STATE, $state, false);
            }
        }

        if (!$job || $job['attempts'] >= 5 || (!$manual && $job['next'] > time())) {
            if (!$manual && !$job && !empty($state['retention_pending'])) {
                apply_retention($state, $connection, (int) $config['keep']);
            }
            return;
        }

        // Save the attempt first so a PHP timeout also counts toward the retry limit.
        ++$job['attempts'];
        $job['next'] = time() + 5 * MINUTE_IN_SECONDS;
        $job['error'] = 'De vorige overdracht is gestart. Na een onderbreking probeert WordPress het opnieuw.';
        $state['job'] = $job;
        update_option(STATE, $state, false);
        try {
            if ($job['account'] !== $connection['account']) {
                throw new \RuntimeException('Het gekoppelde account verschilt van het doel van deze upload.');
            }
            $finished = copy_next($job, $connection);
            if ($finished) {
                $state['done'][$connection['account']] = [
                    'id' => $job['id'], 'folder' => $job['parent'] . '/' . $job['folder'], 'time' => time(),
                ];
                $state['managed'][$connection['account']][$job['folder_id']] = [
                    'name' => $job['folder'],
                    'parent_id' => $job['parent_id'],
                    'manifest' => $job['manifest'],
                    'files' => array_merge(array_keys($job['files']), ['VOLTOOID.json']),
                    'uploaded_at' => time(),
                ];
                $state['retention_pending'] = $connection['account'];
                unset($state['job'], $state['schedule_error']);
                update_option(STATE, $state, false);
            } else {
                $job['attempts'] = 0;
                $job['next'] = 0;
                $job['error'] = '';
                $state['job'] = $job;
            }
        } catch (\RuntimeException $error) {
            $job['error'] = $error->getMessage();
            $state['job'] = $job;
        }
        update_option(STATE, $state, false);
        if (!isset($state['job']) && !empty($state['retention_pending'])) {
            apply_retention($state, $connection, (int) $config['keep']);
        }
    }, $manual);
}

function copy_next(array &$job, array $connection): bool {
    $region = $connection['region'];
    $token = token_from($connection);
    if (!isset($job['folder_id'])) {
        $job['parent_id'] = create_folder($region, $token, 0, $job['parent']);
        $job['folder_id'] = create_folder($region, $token, $job['parent_id'], $job['folder']);
    } elseif (!isset($job['parent_id'])) {
        $job['parent_id'] = create_folder($region, $token, 0, $job['parent']);
    }
    $folder = cloud_folder($connection, $job['folder_id']);
    if (($folder['name'] ?? '') !== $job['folder'] || (int) ($folder['parentfolderid'] ?? 0) !== $job['parent_id']) {
        throw new \RuntimeException('De pCloud-map voor deze back-up is verplaatst of hernoemd.');
    }
    $remote_files = cloud_children($folder);

    $remaining = array_diff_key($job['files'], $job['confirmed']);
    if ($remaining) {
        $name = array_key_first($remaining);
        $path = local_file($name);
        if (filesize($path) !== $remaining[$name]) {
            throw new \RuntimeException('Een archiefdeel is sinds het klaarzetten gewijzigd.');
        }
        $hash = hash_file('sha1', $path);
        if (!$hash) {
            throw new \RuntimeException('De controlesom van een archiefdeel kon niet worden berekend.');
        }
        $file_id = (int) ($remote_files[$name]['fileid'] ?? 0);
        if (!$file_id) {
            if (isset($remote_files['VOLTOOID.json']) || isset($remote_files[$name])) {
                throw new \RuntimeException('De pCloud-map bevat al een voltooid herstelpunt of een conflicterend bestand.');
            }
            $file_id = upload($region, $token, $job['folder_id'], new \CURLFile($path, 'application/octet-stream', $name));
        }
        $remote = api($region, 'checksumfile', ['fileid' => $file_id], $token);
        if (!hash_equals($hash, (string) ($remote['sha1'] ?? ''))) {
            throw new \RuntimeException('De controlesom van het geüploade archiefdeel komt niet overeen.');
        }
        $job['confirmed'][$name] = $hash;
        return false;
    }

    $manifest = wp_json_encode([
        'updraft_backup_utc' => gmdate('c', $job['timestamp']),
        'files_sha1' => $job['confirmed'],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (false === $manifest) {
        throw new \RuntimeException('De lijst van geverifieerde archieven kon niet worden gemaakt.');
    }
    $job['manifest'] = sha1($manifest);
    $marker_id = (int) ($remote_files['VOLTOOID.json']['fileid'] ?? 0);
    if (!$marker_id) {
        $marker_id = upload($region, $token, $job['folder_id'], new \CURLStringFile($manifest, 'VOLTOOID.json', 'application/json'));
    }
    $marker = api($region, 'checksumfile', ['fileid' => $marker_id], $token);
    if (!hash_equals($job['manifest'], (string) ($marker['sha1'] ?? ''))) {
        throw new \RuntimeException('De voltooiingslijst in pCloud wijkt af van de archiefdelen.');
    }
    return true;
}

function create_folder(string $region, string $token, int $parent, string $name): int {
    $response = api($region, 'createfolderifnotexists', ['folderid' => $parent, 'name' => $name], $token);
    $id = (int) ($response['metadata']['folderid'] ?? 0);
    if ($id < 1) {
        throw new \RuntimeException('pCloud gaf geen geldige doelmap terug.');
    }
    return $id;
}

function upload(string $region, string $token, int $folder, \CURLFile|\CURLStringFile $file): int {
    if (!function_exists('curl_init')) {
        throw new \RuntimeException('De PHP cURL-extensie is vereist voor uploads.');
    }
    $handle = curl_init('https://' . api_host($region) . '/uploadfile');
    curl_setopt_array($handle, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['folderid' => (string) $folder, 'nopartial' => '1', 'renameifexists' => '1', 'file' => $file],
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 90,
    ]);
    $body = curl_exec($handle);
    if (false === $body) {
        throw new \RuntimeException(sprintf('De upload is onderbroken (cURL %d).', curl_errno($handle)));
    }
    $data = api_result(curl_getinfo($handle, CURLINFO_RESPONSE_CODE), $body, 'uploadfile');
    $id = (int) ($data['metadata'][0]['fileid'] ?? 0);
    if ($id < 1 || ($data['metadata'][0]['name'] ?? '') !== $file->postname) {
        throw new \RuntimeException('pCloud bevestigde het geüploade bestand niet.');
    }
    return $id;
}

function field(string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim(wp_unslash($_POST[$key])) : '';
}

function admin_page_url(string $tab = 'backups'): string {
    return add_query_arg(['page' => 'updraft-pcloud-copy', 'tab' => $tab], admin_url('tools.php'));
}

function handle_admin(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Je hebt geen toegang.', '', ['response' => 403]);
    }
    check_admin_referer('upc_change');
    $action = field('operation');
    $tab = match ($action) {
        'queue', 'retry' => 'upload',
        'scan', 'recover', 'recover_retry', 'recover_cancel' => 'recover',
        'process' => 'recover' === field('tab') ? 'recover' : 'upload',
        default => 'backups',
    };
    $notice = 'Opgeslagen.';
    try {
        if ('process' === $action) {
            tick(true);
            wp_safe_redirect(admin_page_url($tab));
            exit;
        }
        locked(static function () use ($action, &$notice) {
            $config = settings();
            $state = get_option(STATE, []);
            if ('schedule' === $action) {
                $enabled = '1' === field('enabled');
                $submitted = isset($_POST['days']) && is_array($_POST['days']) ? $_POST['days'] : [];
                $days = array_values(array_filter(range(1, count(WEEKDAYS)), static fn ($day) => '1' === ($submitted[$day] ?? null)));
                $time = field('time');
                $second = field('time_second');
                $source = field('source');
                $keep = filter_var(field('keep'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000]]);
                if ($enabled && !$days) {
                    throw new \RuntimeException('Vink minstens één uploaddag aan.');
                }
                if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $time)
                    || !array_key_exists($source, source_labels()) || false === $keep) {
                    throw new \RuntimeException('Kies een geldige tijd, back-upsoort en bewaarlimiet.');
                }
                if ($second !== '' && (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $second) || $second <= $time)) {
                    throw new \RuntimeException('Kies een tweede tijdstip dat later is dan het eerste.');
                }
                $config['enabled'] = $enabled;
                $config['day'] = match (count($days)) {
                    count(WEEKDAYS) => 'daily',
                    1 => (string) $days[0],
                    default => $days,
                };
                $config['time'] = $time;
                $config['time_second'] = $second;
                $config['source'] = $source;
                $config['keep'] = $keep;
                update_option(SETTINGS, $config, false);
            } elseif ('app' === $action) {
                if (!preg_match('/^[A-Za-z0-9_-]+$/D', field('client'))) {
                    throw new \RuntimeException('Enter the Client ID from pCloud My Apps.');
                }
                $config['client'] = field('client');
                update_option(SETTINGS, $config, false);
            } elseif ('connect' === $action) {
                $attempt = ['time' => time(), 'success' => false, 'message' => ''];
                try {
                    connect($config, $state);
                    $attempt['success'] = true;
                } catch (\RuntimeException $error) {
                    $attempt['message'] = $error->getMessage();
                }
                update_option(CONNECTION_STATUS, $attempt, false);
                $notice = '';
            } elseif (in_array($action, ['queue', 'retry'], true)) {
                $connection = get_option(CONNECTION, []);
                token_from($connection);
                if ('retry' === $action && !empty($state['job'])) {
                    $state['job']['attempts'] = 0;
                    $state['job']['next'] = 0;
                    $state['job']['error'] = '';
                } elseif (!empty($state['job']) && $state['job']['attempts'] < 5) {
                    throw new \RuntimeException('Er staat al een back-up klaar voor verzending.');
                } else {
                    $id = field('backup');
                    if (!valid_backup_id($id)) {
                        throw new \RuntimeException('Kies eerst een UpdraftPlus-back-up.');
                    }
                    $state['job'] = new_job(latest_backup('any', $id), $connection);
                }
                update_option(STATE, $state, false);
                $notice = 'De back-up staat klaar. WordPress verwerkt de upload op de achtergrond.';
            } elseif (in_array($action, ['scan', 'recover', 'recover_retry', 'recover_cancel'], true)) {
                $notice = handle_recovery($action, $state, get_option(CONNECTION, []));
            }
        }, true);
    } catch (\RuntimeException $error) {
        $notice = $error->getMessage();
    }
    set_transient('upc_notice_' . get_current_user_id(), $notice, MINUTE_IN_SECONDS);
    wp_safe_redirect(admin_page_url($tab));
    exit;
}

function source_labels(): array {
    return ['any' => 'Elke geslaagde back-up', 'full' => 'Bestanden en database', 'database' => 'Alleen database', 'files' => 'Alleen bestanden'];
}

function form_start(string $operation): void {
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    wp_nonce_field('upc_change');
    echo '<input type="hidden" name="action" value="upc_change"><input type="hidden" name="operation" value="' . esc_attr($operation) . '">';
}

function render(): void {
    $config = settings();
    $state = get_option(STATE, []);
    $connection = get_option(CONNECTION, []);
    $attempt = get_option(CONNECTION_STATUS, []);
    $failed = $attempt && empty($attempt['success']);
    $account = $connection['account'] ?? '';
    $done = $state['done'][$account] ?? null;
    $catalog = ($state['catalog']['account'] ?? '') === $account ? ($state['catalog'] ?? []) : [];
    $job = $state['job'] ?? null;
    $recovery = $state['recovery'] ?? null;
    $tabs = ['backups' => 'Back-ups', 'upload' => 'Uploaden', 'recover' => 'Ophalen'];
    $tab = isset($_GET['tab']) && is_string($_GET['tab']) ? wp_unslash($_GET['tab']) : 'backups';
    $tab = isset($tabs[$tab]) ? $tab : 'backups';
    $upload_pending = $job && $job['attempts'] < 5;
    $recovery_pending = $recovery && $recovery['attempts'] < 5;
    $labels = source_labels();
    $sources = [];
    if ('upload' === $tab) {
        try {
            $sources = available_backups();
        } catch (\RuntimeException $error) {
            $sources = [];
        }
    }
    $notice_key = 'upc_notice_' . get_current_user_id();
    $notice = get_transient($notice_key);
    delete_transient($notice_key);
    ?>
    <div class="wrap upc-admin">
        <h1>TFSC Updraft to PCloud</h1>
        <?php if ($failed) : ?>
            <div class="notice notice-error inline" role="alert"><p><strong>pCloud connection failed.</strong> <?php echo esc_html($attempt['message']); ?>
                <?php if ($connection) : ?>The previous connection was kept.<?php endif; ?></p></div>
        <?php elseif ($connection) : ?>
            <div class="notice notice-success inline" role="status"><p><strong>Connected to pCloud (<?php echo esc_html($connection['region']); ?>).</strong></p></div>
        <?php else : ?>
            <div class="notice notice-info inline"><p><strong>pCloud is not connected.</strong> Open <a href="<?php echo esc_url(admin_page_url() . '#upc-connection'); ?>">pCloud connection settings</a> to get started.</p></div>
        <?php endif; ?>
        <?php if ($notice) : ?><div class="notice notice-info inline" role="status"><p><?php echo esc_html($notice); ?></p></div><?php endif; ?>

        <nav class="nav-tab-wrapper upc-tabs" id="upc-transfer" aria-label="Back-upbeheer">
            <?php foreach ($tabs as $key => $label) : ?>
                <a href="<?php echo esc_url(admin_page_url($key)); ?>" class="nav-tab<?php echo $tab === $key ? ' nav-tab-active' : ''; ?>"<?php if ($tab === $key) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </nav>

        <?php if ('backups' === $tab) : ?>
            <details id="upc-connection" class="upc-panel upc-connection" <?php echo (!$connection || $failed) ? 'open' : ''; ?>>
                <summary><strong><?php echo $connection ? 'Change pCloud connection' : 'Set up pCloud connection'; ?></strong></summary>
                <?php if ($attempt) : ?>
                    <p>Last connection attempt <?php echo esc_html(wp_date('d-m-Y H:i', $attempt['time'])); ?>.</p>
                <?php endif; ?>
                <p>Open your approved app in <a href="https://docs.pcloud.com/my_apps/" target="_blank" rel="noopener noreferrer">pCloud My Apps</a> to find its Client ID and Client Secret.</p>
                <?php form_start('app'); ?>
                <p class="upc-field"><label>Client ID <input type="text" name="client" value="<?php echo esc_attr($config['client']); ?>" required></label></p>
                <?php submit_button('Save Client ID', 'secondary'); ?></form>
                <?php if ($config['client']) : ?>
                    <?php $auth = add_query_arg(['client_id' => $config['client'], 'response_type' => 'code'], 'https://my.pcloud.com/oauth2/authorize'); ?>
                    <p><a href="<?php echo esc_url($auth); ?>" target="_blank" rel="noopener noreferrer">Authorize in pCloud and get an Access Code</a>.</p>
                    <?php form_start('connect'); ?>
                    <p class="upc-field"><label>Storage region <select name="region">
                        <option value="EU" <?php selected($connection['region'] ?? 'EU', 'EU'); ?>>Europe (eapi.pcloud.com)</option>
                        <option value="US" <?php selected($connection['region'] ?? 'EU', 'US'); ?>>United States (api.pcloud.com)</option>
                    </select></label></p>
                    <p class="upc-field"><label>Client Secret <input type="password" name="secret" autocomplete="off" required></label></p>
                    <p class="upc-field"><label>Access Code <input type="password" name="code" autocomplete="off" required></label></p>
                    <p>Use the Host Name shown by pCloud to select the storage region. Copy the Access Code from the authorization page.</p>
                    <?php submit_button('Connect to pCloud', 'primary'); ?></form>
                <?php endif; ?>
            </details>

            <div class="upc-overview">
                <section class="upc-panel">
                    <h2>Lokale back-ups</h2>
                    <p>Maak, bekijk en herstel back-ups in UpdraftPlus.</p>
                    <p><a class="button" href="<?php echo esc_url(admin_url('options-general.php?page=updraftplus')); ?>">Open UpdraftPlus</a></p>
                </section>
                <section class="upc-panel">
                    <h2>pCloud-doelmap</h2>
                    <p><code><?php echo esc_html(!empty($catalog['path']) ? $catalog['path'] : backup_parent_name()); ?></code></p>
                    <p>Elke upload krijgt een gedateerde submap. Bekijk de map in <a href="https://my.pcloud.com/" target="_blank" rel="noopener noreferrer">pCloud Files</a>.</p>
                </section>
            </div>
            <section class="upc-panel">
                <h2>Automatisch uploaden en bewaren</h2>
                <?php form_start('schedule'); ?>
                <p><label><input type="checkbox" name="enabled" value="1" <?php checked($config['enabled']); ?>> Automatisch uploaden</label></p>
                <p class="upc-field"><label>Welke back-ups <select name="source">
                    <?php foreach ($labels as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($config['source'], $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select></label></p>
                <fieldset class="upc-schedule-days">
                    <legend>Uploaddagen</legend>
                    <div>
                        <?php $days = schedule_days($config['day']); ?>
                        <?php foreach (WEEKDAYS as $index => $label) : $day = $index + 1; ?>
                            <label><input type="checkbox" name="days[<?php echo esc_attr((string) $day); ?>]" value="1" <?php checked(in_array($day, $days, true)); ?>> <?php echo esc_html($label); ?></label>
                        <?php endforeach; ?>
                    </div>
                    <p class="description">Vink alle zeven dagen aan om iedere dag te uploaden.</p>
                </fieldset>
                <div class="upc-schedule-time">
                    <label>Eerste tijdstip <input type="time" name="time" value="<?php echo esc_attr($config['time']); ?>" required></label>
                    <label>Tweede tijdstip (optioneel) <input type="time" name="time_second" value="<?php echo esc_attr($config['time_second']); ?>"></label>
                    <span><?php echo esc_html(wp_timezone_string()); ?></span>
                </div>
                <p class="upc-field"><label>Herstelpunten bewaren in pCloud <input type="number" min="0" max="1000" step="1" name="keep" value="<?php echo esc_attr((string) $config['keep']); ?>" required></label></p>
                <p>0 bewaart alles. Met een limiet ruimt de plug-in na een nieuwe geslaagde upload oudere, door deze installatie beheerde pCloud-kopieën op. Stel het aantal lokale back-ups apart in UpdraftPlus in.</p>
                <?php submit_button('Schema opslaan', 'primary'); ?></form>
                <?php if (!empty($state['schedule_error'])) : ?><p><?php echo esc_html($state['schedule_error']); ?></p><?php endif; ?>
                <?php if (!empty($state['retention_error'])) : ?><p><?php echo esc_html($state['retention_error']); ?></p><?php endif; ?>
            </section>
        <?php elseif ('upload' === $tab) : ?>
            <section class="upc-panel">
                <h2>Back-up uploaden</h2>
                <p>UpdraftPlus bepaalt welke bestanden en database in de back-up zitten. Kies hier welke complete, geslaagde set de plug-in kopieert.</p>
                <?php if (!$connection) : ?><p><a href="<?php echo esc_url(admin_page_url() . '#upc-connection'); ?>">Koppel pCloud</a> om een back-up te uploaden.</p><?php endif; ?>
                <?php if (!$sources) : ?><p>Er is geen geslaagde lokale UpdraftPlus-back-up met alle archiefdelen gevonden.</p><?php endif; ?>
                <?php if ($connection && $sources && (!$job || $job['attempts'] >= 5)) : ?>
                    <?php form_start('queue'); ?>
                    <p class="upc-field"><label>Lokale back-up <select name="backup" required>
                        <?php foreach ($sources as $source) : ?>
                            <option value="<?php echo esc_attr($source['id']); ?>"><?php echo esc_html(wp_date('d-m-Y H:i', $source['timestamp']) . ' · ' . $labels[$source['type']] . ' · ' . count($source['files']) . ' archiefdelen'); ?></option>
                        <?php endforeach; ?>
                    </select></label></p>
                    <?php submit_button('Upload klaarzetten', 'primary'); ?></form>
                <?php endif; ?>
                <?php if (!$job && $done) : ?>
                    <div class="notice notice-success inline" role="status">
                        <p><strong>Upload voltooid.</strong> <?php echo esc_html(wp_date('d-m-Y H:i', $done['time'])); ?><br>
                            <code><?php echo esc_html($done['folder']); ?></code></p>
                    </div>
                <?php endif; ?>
                <?php if ($job) : ?>
                    <p><?php echo esc_html(sprintf('Upload: %d van %d archiefdelen gecontroleerd.', count($job['confirmed']), count($job['files']))); ?>
                    <?php if ($job['attempts'] >= 5) : ?> Automatische pogingen gestopt.<?php endif; ?></p>
                    <?php if ($job['error']) : ?><p><?php echo esc_html($job['error']); ?></p><?php endif; ?>
                    <?php if ($job['error']) : form_start('retry'); submit_button('Upload opnieuw proberen', 'secondary'); ?></form><?php endif; ?>
                <?php endif; ?>

                <?php if ($connection && $upload_pending && !$recovery_pending) : ?>
                    <?php form_start('process'); ?>
                    <input type="hidden" name="tab" value="upload">
                    <?php submit_button('Volgende uploadstap uitvoeren', 'secondary'); ?></form>
                <?php elseif ($upload_pending && $recovery_pending) : ?>
                    <p>Het ophalen van het herstelpunt krijgt voorrang. <a href="<?php echo esc_url(admin_page_url('recover')); ?>">Ga naar Ophalen</a>.</p>
                <?php endif; ?>
            </section>
        <?php else : ?>
            <section class="upc-panel">
                <h2>Back-up uit pCloud terughalen</h2>
                <p>De plug-in haalt de archieven naar de lokale UpdraftPlus-map. Daarna kies je in UpdraftPlus zelf of je de website wilt herstellen.</p>
                <?php if (!$recovery && !empty($state['recovered'])) : ?>
                    <div class="notice notice-success inline" role="status"><p><strong>Herstelpunt beschikbaar in UpdraftPlus.</strong>
                        <?php if (isset($state['recovered']['downloaded'], $state['recovered']['local'])) : ?>
                            <?php echo esc_html(sprintf('Uit pCloud gedownload en gecontroleerd: %d. Lokale archiefdelen hergebruikt: %d.', $state['recovered']['downloaded'], $state['recovered']['local'])); ?>
                        <?php endif; ?>
                        Bekijk <?php echo esc_html($state['recovered']['name']); ?> onder <a href="<?php echo esc_url(admin_url('options-general.php?page=updraftplus')); ?>">Bestaande back-ups in UpdraftPlus</a>.</p></div>
                <?php endif; ?>
                <?php if (!$connection) : ?>
                    <p><a href="<?php echo esc_url(admin_page_url() . '#upc-connection'); ?>">Koppel pCloud</a> om een herstelpunt op te halen.</p>
                <?php elseif (!$recovery) : ?>
                    <?php form_start('scan'); submit_button('Herstelpunten controleren', 'secondary'); ?></form>
                <?php endif; ?>
                <?php if ($connection && !$recovery && !empty($catalog['backups'])) : ?>
                    <?php form_start('recover'); ?>
                    <p class="upc-field"><label>Herstelpunt in pCloud <select name="remote_backup" required>
                        <?php foreach ($catalog['backups'] as $entry) : ?>
                            <option value="<?php echo esc_attr((string) $entry['id']); ?>"><?php echo esc_html($entry['name']); ?></option>
                        <?php endforeach; ?>
                    </select></label></p>
                    <p><label><input type="checkbox" name="force_download" value="1"> Download alle archiefdelen opnieuw voor controle</label></p>
                    <p>Ook als dezelfde back-up lokaal al aanwezig is, haalt de plug-in een tijdelijke kopie uit pCloud.</p>
                    <?php submit_button('Terughalen naar UpdraftPlus', 'primary'); ?></form>
                <?php elseif ($connection && !$recovery) : ?>
                    <p><?php echo $catalog ? 'Geen herstelpunten met een voltooiingslijst gevonden.' : 'Klik op Herstelpunten controleren om de beschikbare back-ups te laden.'; ?></p>
                <?php endif; ?>
                <?php if ($recovery) : ?>
                    <p><?php echo esc_html(sprintf('Download: %d van %d archiefdelen gecontroleerd.', count($recovery['confirmed']), count($recovery['files']))); ?>
                    <?php if ($recovery['attempts'] >= 5) : ?> Automatische pogingen gestopt.<?php endif; ?></p>
                    <?php if ($recovery['error']) : ?><p><?php echo esc_html($recovery['error']); ?></p>
                        <?php form_start('recover_retry'); submit_button('Terughalen opnieuw proberen', 'secondary'); ?></form>
                    <?php endif; ?>
                    <?php form_start('recover_cancel'); submit_button('Tijdelijke download opruimen', 'secondary'); ?></form>
                <?php endif; ?>

                <?php if ($connection && $recovery_pending) : ?>
                    <?php form_start('process'); ?>
                    <input type="hidden" name="tab" value="recover">
                    <?php submit_button('Volgende downloadstap uitvoeren', 'secondary'); ?></form>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!wp_next_scheduled(TICK) || (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON)) : ?>
            <p class="upc-note">Controleer of de hosting geplande WordPress-taken uitvoert. Een lopende overdracht kun je ook handmatig verder verwerken.</p>
        <?php endif; ?>
    </div>
    <?php
}
