<?php
/**
 * Plugin Name: TFSC Updraft to PCloud
 * Description: Kopieert lokale UpdraftPlus-back-ups op een ingesteld tijdstip naar pCloud.
 * Version: 0.1.1
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
const TICK = 'upc_upload_tick';
const INTERVAL = 'upc_every_minute';

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
add_action('admin_post_upc_change', __NAMESPACE__ . '\\handle_admin');

function settings(): array {
    return wp_parse_args(get_option(SETTINGS, []), [
        'enabled' => false, 'day' => '7', 'time' => '04:00', 'client' => '',
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
    if (!empty($state['job']) && $state['job']['account'] !== $account) {
        throw new \RuntimeException('An upload is still queued for a different pCloud account.');
    }
    $iv = random_bytes(12);
    $ciphertext = openssl_encrypt($token, 'aes-256-gcm', secret_key(), OPENSSL_RAW_DATA, $iv, $tag);
    if (false === $ciphertext) {
        throw new \RuntimeException('The pCloud access token could not be encrypted.');
    }
    update_option(CONNECTION, [
        'account' => $account, 'region' => $region, 'sealed' => base64_encode($iv . $tag . $ciphertext),
    ], false);
}

function latest_backup(): array {
    global $updraftplus;
    if (!is_object($updraftplus) || !class_exists('UpdraftPlus_Options') || !method_exists($updraftplus, 'backups_dir_location')) {
        throw new \RuntimeException('Activeer eerst UpdraftPlus.');
    }
    $last = \UpdraftPlus_Options::get_updraft_option('updraft_last_backup', []);
    if (empty($last['success']) || empty($last['backup_time']) || empty($last['backup_array']) ||
        !preg_match('/^[a-f0-9]{12}$/D', $last['backup_nonce'] ?? '')) {
        throw new \RuntimeException('UpdraftPlus heeft geen recente geslaagde back-up gemeld.');
    }

    $nonce = $last['backup_nonce'];
    $names = [];
    $entities = array_keys($updraftplus->get_backupable_file_entities());
    $entities[] = 'db';
    foreach ($entities as $entity) {
        foreach ((array) ($last['backup_array'][$entity] ?? []) as $name) {
            if (!is_string($name) || basename($name) !== $name ||
                !preg_match('/^backup_[^\/\\\\]+_' . $nonce . '-[^\/\\\\]+\.(zip|gz|crypt)$/D', $name)) {
                throw new \RuntimeException('UpdraftPlus gaf een onverwachte archiefnaam terug.');
            }
            $names[] = $name;
        }
    }
    if (!$names || count($names) !== count(array_unique($names))) {
        throw new \RuntimeException('De back-up bevat geen bruikbare lijst met archiefdelen.');
    }
    $files = [];
    foreach ($names as $name) {
        $path = local_file($name);
        $size = filesize($path);
        if (false === $size || $size < 1) {
            throw new \RuntimeException('Een archiefdeel is leeg of onleesbaar.');
        }
        $files[$name] = $size;
    }
    $timestamp = (int) $last['backup_time'];
    return ['id' => $nonce, 'timestamp' => $timestamp, 'files' => $files];
}

function local_file(string $name): string {
    global $updraftplus;
    if (!is_object($updraftplus) || !method_exists($updraftplus, 'backups_dir_location')) {
        throw new \RuntimeException('Activeer eerst UpdraftPlus.');
    }
    $dir = realpath($updraftplus->backups_dir_location());
    $path = $dir ? realpath($dir . DIRECTORY_SEPARATOR . $name) : false;
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

function new_job(array $backup, array $connection): array {
    $url = home_url();
    $host = sanitize_title((string) wp_parse_url($url, PHP_URL_HOST));
    return $backup + [
        'parent' => 'WordPress-backups-' . $host . '-' . substr(hash('sha256', $url), 0, 8),
        'account' => $connection['account'], 'confirmed' => [], 'attempts' => 0,
        'next' => 0, 'error' => '',
        'folder' => gmdate('Y-m-d_H-i-s', $backup['timestamp']) . '_UTC_' . $backup['id'],
    ];
}

function schedule_due(array $config, \DateTimeImmutable $now, array $state): bool {
    return $config['enabled'] && ($config['day'] === 'daily' || $config['day'] === $now->format('N')) &&
        $now->format('H:i') >= $config['time'] && ($state['schedule_date'] ?? '') !== $now->format('Y-m-d');
}

function tick(): void {
    if (!get_option(CONNECTION)) {
        return;
    }
    locked(static function () {
        $config = settings();
        $connection = get_option(CONNECTION, []);
        $state = get_option(STATE, []);
        $now = current_datetime();
        $job = $state['job'] ?? null;

        if ((!$job || $job['attempts'] >= 5) && schedule_due($config, $now, $state)) {
            try {
                $backup = latest_backup();
                $same_failed_job = $job && $job['id'] === $backup['id'];
                $already_sent = ($state['done'][$connection['account']]['id'] ?? '') === $backup['id'];
                if (!$same_failed_job && !$already_sent) {
                    $job = new_job($backup, $connection);
                    $state['job'] = $job;
                    $state['schedule_date'] = $now->format('Y-m-d');
                    unset($state['schedule_error']);
                    update_option(STATE, $state, false);
                }
            } catch (\RuntimeException $error) {
                $state['schedule_error'] = $error->getMessage();
                update_option(STATE, $state, false);
            }
        }

        if (!$job || $job['attempts'] >= 5 || $job['next'] > time()) {
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
                unset($state['job'], $state['schedule_error']);
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
    });
}

function copy_next(array &$job, array $connection): bool {
    $region = $connection['region'];
    $token = token_from($connection);
    if (!isset($job['folder_id'])) {
        $parent = create_folder($region, $token, 0, $job['parent']);
        $job['folder_id'] = create_folder($region, $token, $parent, $job['folder']);
    }

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
        $remote_path = '/' . $job['parent'] . '/' . $job['folder'] . '/' . $name;
        $remote = api($region, 'checksumfile', ['path' => $remote_path], $token, [2009]);
        if (!hash_equals($hash, (string) ($remote['sha1'] ?? ''))) {
            $file_id = upload($region, $token, $job['folder_id'], new \CURLFile($path, 'application/octet-stream', $name));
            $remote = api($region, 'checksumfile', ['fileid' => $file_id], $token);
        }
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
    upload($region, $token, $job['folder_id'], new \CURLStringFile($manifest, 'VOLTOOID.json', 'application/json'));
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
        CURLOPT_POSTFIELDS => ['folderid' => (string) $folder, 'nopartial' => '1', 'file' => $file],
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
    if ($id < 1) {
        throw new \RuntimeException('pCloud bevestigde het geüploade bestand niet.');
    }
    return $id;
}

function field(string $key): string {
    return isset($_POST[$key]) && is_string($_POST[$key]) ? trim(wp_unslash($_POST[$key])) : '';
}

function handle_admin(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Je hebt geen toegang.', '', ['response' => 403]);
    }
    check_admin_referer('upc_change');
    $notice = 'Opgeslagen.';
    try {
        locked(static function () use (&$notice) {
            $config = settings();
            $state = get_option(STATE, []);
            $action = field('operation');
            if ('schedule' === $action) {
                $day = field('day');
                $time = field('time');
                if (!preg_match('/^(daily|[1-7])$/D', $day) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $time)) {
                    throw new \RuntimeException('Kies een geldige dag en tijd.');
                }
                $config['enabled'] = '1' === field('enabled');
                $config['day'] = $day;
                $config['time'] = $time;
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
                    $state['job'] = new_job(latest_backup(), $connection);
                }
                update_option(STATE, $state, false);
                $notice = 'De back-up staat klaar. WordPress verwerkt de upload op de achtergrond.';
            }
        }, true);
    } catch (\RuntimeException $error) {
        $notice = $error->getMessage();
    }
    set_transient('upc_notice_' . get_current_user_id(), $notice, MINUTE_IN_SECONDS);
    wp_safe_redirect(admin_url('tools.php?page=updraft-pcloud-copy'));
    exit;
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
    $connection_status = get_option(CONNECTION_STATUS, []);
    $job = $state['job'] ?? null;
    $done = $state['done'][$connection['account'] ?? ''] ?? null;
    $notice_key = 'upc_notice_' . get_current_user_id();
    $notice = get_transient($notice_key);
    delete_transient($notice_key);
    ?>
    <div class="wrap">
        <h1>TFSC Updraft to PCloud</h1>
        <?php if ($notice) : ?><div class="notice notice-info"><p><?php echo esc_html($notice); ?></p></div><?php endif; ?>
        <h2>Uploadschema</h2>
        <?php form_start('schedule'); ?>
        <p><label><input type="checkbox" name="enabled" value="1" <?php checked($config['enabled']); ?>> Automatisch uploaden</label></p>
        <p><label>Dag <select name="day">
            <?php foreach (['daily' => 'Iedere dag', '1' => 'Maandag', '2' => 'Dinsdag', '3' => 'Woensdag', '4' => 'Donderdag', '5' => 'Vrijdag', '6' => 'Zaterdag', '7' => 'Zondag'] as $day => $label) : ?>
                <option value="<?php echo esc_attr((string) $day); ?>" <?php selected($config['day'], (string) $day); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select></label> <label>Tijd <input type="time" name="time" value="<?php echo esc_attr($config['time']); ?>" required></label>
        <span><?php echo esc_html(wp_timezone_string()); ?></span></p>
        <p>Plan deze tijd na de UpdraftPlus-back-up. WordPress start de upload rond of na de gekozen tijd.</p>
        <?php submit_button('Schema opslaan', 'primary'); ?></form>

        <h2>pCloud connection</h2>
        <p><strong><?php echo $connection ? 'Connected (' . esc_html($connection['region']) . ')' : 'Not connected'; ?></strong></p>
        <?php if ($connection_status) : ?>
            <p>Last connection attempt <?php echo esc_html(wp_date('d-m-Y H:i', $connection_status['time'])); ?>.
            <strong><?php echo $connection_status['success'] ? 'Succeeded' : 'Failed'; ?></strong>
            <?php if (!$connection_status['success']) : ?>. <?php echo esc_html($connection_status['message']); ?><?php endif; ?></p>
        <?php endif; ?>
        <p>Open your approved app in <a href="https://docs.pcloud.com/my_apps/" target="_blank" rel="noopener noreferrer">pCloud My Apps</a> to find its Client ID and Client Secret.</p>
        <?php form_start('app'); ?>
        <p><label>Client ID <input type="text" name="client" value="<?php echo esc_attr($config['client']); ?>" required></label></p>
        <?php submit_button('Save Client ID', 'secondary'); ?></form>
        <?php if ($config['client']) : ?>
            <?php $auth = add_query_arg(['client_id' => $config['client'], 'response_type' => 'code'], 'https://my.pcloud.com/oauth2/authorize'); ?>
            <p><a href="<?php echo esc_url($auth); ?>" target="_blank" rel="noopener noreferrer">Authorize in pCloud and get an Access Code</a>.</p>
            <?php form_start('connect'); ?>
            <p><label>Storage region <select name="region">
                <option value="EU" <?php selected($connection['region'] ?? 'EU', 'EU'); ?>>Europe (eapi.pcloud.com)</option>
                <option value="US" <?php selected($connection['region'] ?? 'EU', 'US'); ?>>United States (api.pcloud.com)</option>
            </select></label></p>
            <p><label>Client Secret <input type="password" name="secret" autocomplete="off" required></label></p>
            <p><label>Access Code <input type="password" name="code" autocomplete="off" required></label></p>
            <p>Use the Host Name shown by pCloud to select the storage region. Copy the Access Code from the authorization page.</p>
            <?php submit_button('Connect to pCloud', 'secondary'); ?></form>
        <?php endif; ?>

        <h2>Uploadstatus</h2>
        <?php if ($done) : ?>
            <p>Laatste volledige upload <?php echo esc_html(wp_date('d-m-Y H:i', $done['time'])); ?>.
                Map <code><?php echo esc_html($done['folder']); ?></code>.</p>
        <?php endif; ?>
        <?php if ($job) : ?>
            <p><?php echo esc_html(sprintf('%d van %d archiefdelen gecontroleerd.', count($job['confirmed']), count($job['files']))); ?>
            <?php if ($job['attempts'] >= 5) : ?> Automatische pogingen gestopt.<?php endif; ?></p>
            <?php if ($job['error']) : ?><p><?php echo esc_html($job['error']); ?></p><?php endif; ?>
            <?php if ($job['error']) : form_start('retry'); submit_button('Opnieuw proberen', 'secondary'); ?></form><?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($state['schedule_error'])) : ?><p><?php echo esc_html($state['schedule_error']); ?></p><?php endif; ?>
        <?php if ($connection && (!$job || $job['attempts'] >= 5)) : ?>
            <?php form_start('queue'); submit_button('Nieuwste back-up klaarzetten voor upload', 'secondary'); ?></form>
        <?php endif; ?>
        <?php if (!wp_next_scheduled(TICK) || (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON)) : ?>
            <p>Controleer of je hosting de geplande WordPress-taken uitvoert. Anders blijft de upload in de wachtrij staan.</p>
        <?php endif; ?>
    </div>
    <?php
}
