<?php

define('ABSPATH', __DIR__ . '/');

final class WP_REST_Request {}
final class WP_REST_Server { const READABLE = 'GET'; }

$tfsc_test_capabilities = [];
$tfsc_test_filtered_capability = null;

function add_action($hook, $callback) {}
function register_rest_route($namespace, $route, $args) {}
function sanitize_key($value) { return preg_replace('/[^a-z0-9_-]/', '', strtolower((string) $value)); }
function apply_filters($hook, $value) {
    global $tfsc_test_filtered_capability;
    return null === $tfsc_test_filtered_capability ? $value : $tfsc_test_filtered_capability;
}
function current_user_can($capability) {
    global $tfsc_test_capabilities;
    return !empty($tfsc_test_capabilities[$capability]);
}

require dirname(__DIR__) . '/tfsc-schema-read-endpoint.php';

$assertions = 0;
function assert_same($expected, $actual, $message) {
    global $assertions;
    ++$assertions;
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL: {$message}\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}
function assert_true($actual, $message) { assert_same(true, (bool) $actual, $message); }

$request = new WP_REST_Request();
assert_same(false, tfsc_schema_read_permission_v2($request), 'Anonieme bezoekers hebben geen toegang.');
$tfsc_test_capabilities = ['read' => true];
assert_same(false, tfsc_schema_read_permission_v2($request), 'Een abonnee met alleen read heeft geen toegang.');
$tfsc_test_capabilities['manage_options'] = true;
assert_same(true, tfsc_schema_read_permission_v2($request), 'Een beheerder met manage_options heeft toegang.');

$tfsc_test_filtered_capability = 'tfsc_read_schema';
$tfsc_test_capabilities = ['tfsc_read_schema' => true];
assert_same(true, tfsc_schema_read_permission_v2($request), 'Een expliciet integratieaccount kan een eigen capability krijgen.');
$tfsc_test_filtered_capability = [];
$tfsc_test_capabilities = ['manage_options' => true];
assert_same('manage_options', tfsc_schema_required_capability_v2(), 'Een ongeldig capabilityfilter valt veilig terug op manage_options.');

assert_same('[redacted]', tfsc_schema_safe_scalar('secret-value', 'api_key'), 'API-sleutels worden geredigeerd.');
assert_same(
    ['public' => 'ok', 'access_token' => '[redacted]', 'nested' => ['password' => '[redacted]']],
    tfsc_schema_safe_scalar(['public' => 'ok', 'access_token' => 'secret', 'nested' => ['password' => 'secret']]),
    'Gevoelige waarden worden ook genest geredigeerd.'
);

$normalized = tfsc_schema_normalize_pods_field_v2([
    'name' => 'external_api_key',
    'label' => 'API key',
    'type' => 'text',
    'default_value' => 'top-secret',
    'options' => ['default' => 'top-secret', 'pick_custom' => 'top-secret'],
]);
assert_same('[redacted]', $normalized['default_value'], 'Een gevoelig Pods-veld lekt geen standaardwaarde.');
assert_same('[redacted]', $normalized['options'], 'Een gevoelig Pods-veld lekt geen ruwe opties.');
assert_same('[redacted]', $normalized['options_filtered']['default'], 'Ook gefilterde Pods-opties blijven geredigeerd.');

$source = file_get_contents(dirname(__DIR__) . '/tfsc-schema-read-endpoint.php');
assert_true(strpos($source, "current_user_can( 'read' )") === false, 'De oude abonnee-capability komt niet meer voor.');
assert_true(strpos($source, "'permission_callback' => 'tfsc_schema_read_permission_v2'") !== false, 'De REST-route behoudt een expliciete permission callback.');
assert_true(strpos($source, 'return rest_ensure_response( $response );') !== false, 'De endpoint blijft read-only en retourneert alleen een REST-respons.');
assert_true(strpos($source, 'getMessage()') === false, 'Interne exceptionteksten komen niet in de schemarespons terecht.');

echo "OK: {$assertions} assertions\n";
