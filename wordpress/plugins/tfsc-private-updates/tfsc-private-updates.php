<?php
/**
 * Plugin Name: TFSC Private Updates
 * Description: Goedgekeurde TFSC-pluginreleases in het gewone WordPress-updatescherm.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Update URI: https://github.com/TheFieldServiceCompany/website-thefieldservicecompany.nl
 * Author: The Field Service Company
 * License: GPL-2.0-or-later
 */
namespace TFSC\PrivateUpdates;
defined( 'ABSPATH' ) || exit;

final class Plugin {
 const VERSION = '0.1.0';
 const CATALOG = '_tfsc_private_release_catalog';
 const PACKAGE = '_tfsc_private_release_';
 const LOCK = '_tfsc_private_release_lock';
 const MAX_ZIP = 2097152;
 const MAX_EXPANDED = 10485760;
 const KEEP = 3;

 public static function targets() {
  return [
   'tfsc-cache-warmer/tfsc-cache-warmer.php' => 'TFSC Cache Warmer',
   'tfsc-combo-archive-manager/tfsc-combo-archive-manager.php' => 'TFSC Combo Archive Manager',
   'tfsc-ai-crawler-log-main/tfsc-ai-crawler-log.php' => 'TFSC Crawler Log',
   'tfsc-custom-functions/tfsc-custom-functions.php' => 'TFSC Custom Functions',
   'tfsc-form-bridge/tfsc-form-bridge.php' => 'TFSC Form Bridge',
   'tfsc-media-filters/tfsc-media-filters.php' => 'TFSC Mediafilters',
   'tfsc-rank-math-breadcrumbs/tfsc-rank-math-breadcrumbs.php' => 'TFSC Rank Math Breadcrumbs',
   'tfsc-schema-read-endpoint/tfsc-schema-read-endpoint.php' => 'TFSC Schema Read Endpoint',
   'server-location-test-probe/server-location-test-probe.php' => 'TFSC Server Location Test Probe',
   'tfsc-structured-data/tfsc-structured-data.php' => 'TFSC Structured Data',
   'updraft-pcloud-copy/updraft-pcloud-copy.php' => 'TFSC Updraft to pCloud',
   'tfsc-private-updates/tfsc-private-updates.php' => 'TFSC Private Updates',
  ];
 }
 public static function boot() {
  add_action( 'rest_api_init', [ __CLASS__, 'routes' ] );
  add_filter( 'pre_set_site_transient_update_plugins', [ __CLASS__, 'updates' ], 100 );
  add_filter( 'site_transient_update_plugins', [ __CLASS__, 'updates' ], 100 );
  add_filter( 'upgrader_pre_download', [ __CLASS__, 'download' ], 100, 4 );
  add_filter( 'plugins_api', [ __CLASS__, 'information' ], 100, 3 );
  add_action( 'admin_menu', function () { add_management_page( 'TFSC Updates', 'TFSC Updates', 'update_plugins', 'tfsc-private-updates', [ __CLASS__, 'page' ] ); } );
 }
 public static function allowed() { return current_user_can( 'manage_options' ) && current_user_can( 'install_plugins' ) && current_user_can( 'update_plugins' ); }
 public static function routes() {
  register_rest_route( 'tfsc-updates/v1', '/releases', [
   [ 'methods' => 'GET', 'permission_callback' => [ __CLASS__, 'allowed' ], 'callback' => function () { return self::status(); } ],
   [ 'methods' => 'POST', 'permission_callback' => [ __CLASS__, 'allowed' ], 'callback' => [ __CLASS__, 'publish' ] ],
  ] );
  register_rest_route( 'tfsc-updates/v1', '/releases/(?P<id>[a-f0-9]{32})', [ 'methods' => 'DELETE', 'permission_callback' => [ __CLASS__, 'allowed' ], 'callback' => [ __CLASS__, 'withdraw' ] ] );
 }
 private static function key() { return hash_hmac( 'sha256', 'tfsc-private-release-v1', wp_salt( 'auth' ) . "\0" . wp_salt( 'secure_auth' ) ); }
 private static function signature( array $release ) { unset( $release['signature'] ); return hash_hmac( 'sha256', wp_json_encode( [ get_current_blog_id(), $release ] ), self::key() ); }
 private static function valid( $release ) {
  if ( ! is_array( $release ) ) { return false; }
  foreach ( [ 'signature', 'plugin', 'id', 'version', 'sha256', 'requires', 'requires_php', 'tested', 'state' ] as $key ) { if ( ! isset( $release[$key] ) || ! is_string( $release[$key] ) ) { return false; } }
  return 'approved' === $release['state'] && isset( self::targets()[$release['plugin']] ) && preg_match( '/^[a-f0-9]{32}$/D', $release['id'] ) && preg_match( '/^[a-f0-9]{64}$/D', $release['sha256'] )
   && self::version_ok( $release['version'] ) && self::version_ok( $release['requires'], true ) && self::version_ok( $release['requires_php'], true ) && self::version_ok( $release['tested'] ) && hash_equals( self::signature( $release ), $release['signature'] );
 }
 private static function catalog() { $data = get_option( self::CATALOG, [] ); return is_array( $data ) ? $data : []; }
 private static function headers( $source ) {
  $out = [];
  foreach ( [ 'name' => 'Plugin Name', 'version' => 'Version', 'requires' => 'Requires at least', 'requires_php' => 'Requires PHP' ] as $key => $label ) {
   $out[$key] = preg_match( '/^[ \t\/*#@]*' . preg_quote( $label, '/' ) . ':(.*)$/mi', $source, $match ) ? trim( preg_replace( '/\s*(?:\*\/|\?>).*$/', '', $match[1] ) ) : '';
  }
  return $out;
 }
 private static function version_ok( $version, $empty = false ) { return is_string( $version ) && ( $empty && '' === $version || strlen( $version ) <= 64 && preg_match( '/^[0-9]+\.[0-9]+(?:\.[0-9]+){0,2}(?:[-.][A-Za-z0-9]+)*$/D', $version ) ); }
 private static function temporary( $name ) {
  require_once ABSPATH . 'wp-admin/includes/file.php';
  $file = wp_tempnam( $name );
  if ( ! $file ) { return new \WP_Error( 'staging_failed', 'Een privé tijdelijke map is niet beschikbaar.' ); }
  $parent = trailingslashit( wp_normalize_path( (string) realpath( dirname( $file ) ) ) );
  $uploads = wp_upload_dir();
  foreach ( [ ABSPATH, WP_CONTENT_DIR, $uploads['basedir'] ?? '' ] as $public ) {
   $public = $public ? realpath( $public ) : false;
   if ( $public && str_starts_with( $parent, trailingslashit( wp_normalize_path( $public ) ) ) ) { wp_delete_file( $file ); return new \WP_Error( 'unsafe_temp_directory', 'Stel een tijdelijke map buiten de openbare website in.' ); }
  }
  chmod( $file, 0600 ); return $file;
 }
 public static function inspect( $bytes, $plugin ) {
  if ( ! isset( self::targets()[$plugin] ) || ! is_string( $bytes ) || strlen( $bytes ) > self::MAX_ZIP || strlen( $bytes ) < 22 ) { return new \WP_Error( 'invalid_package', 'Dit pakket of deze plugin is niet toegestaan.', [ 'status' => 400 ] ); }
  if ( ! class_exists( '\ZipArchive' ) ) { return new \WP_Error( 'zip_missing', 'De PHP ZIP-extensie is nodig voor veilige pakketcontrole.', [ 'status' => 503 ] ); }
  require_once ABSPATH . 'wp-admin/includes/file.php';
  $file = self::temporary( 'tfsc-release.zip' );
  if ( is_wp_error( $file ) ) { return $file; }
  if ( ! $file || file_put_contents( $file, $bytes, LOCK_EX ) !== strlen( $bytes ) ) { if ( $file ) { wp_delete_file( $file ); } return new \WP_Error( 'staging_failed', 'Het pakket kon niet worden gecontroleerd.', [ 'status' => 500 ] ); }
  $zip = new \ZipArchive(); $opened = false;
  try {
   if ( true !== $zip->open( $file ) ) { return new \WP_Error( 'invalid_zip', 'Ongeldig ZIP-bestand.', [ 'status' => 400 ] ); }
   $opened = true; $size = 0; $seen = []; $root = dirname( $plugin ) . '/';
   if ( $zip->numFiles < 1 || $zip->numFiles > 300 ) { return new \WP_Error( 'archive_limit', 'Het pakket bevat te veel bestanden.', [ 'status' => 400 ] ); }
   for ( $i = 0; $i < $zip->numFiles; $i++ ) {
    $entry = $zip->statIndex( $i ); $name = is_array( $entry ) ? $entry['name'] : ''; $parts = explode( '/', rtrim( $name, '/' ) );
    if ( ! str_starts_with( $name, $root ) || preg_match( '/[\x00-\x1f\x7f:\\\\]/', $name ) || array_intersect( [ '', '.', '..' ], $parts ) || isset( $seen[strtolower( $name )] ) || ! empty( $entry['encryption_method'] ) ) { return new \WP_Error( 'unsafe_archive_path', 'Het pakket bevat een onveilig of afwijkend pad.', [ 'status' => 400 ] ); }
    $seen[strtolower( $name )] = true; $size += max( 0, (int) $entry['size'] );
    if ( $size > self::MAX_EXPANDED ) { return new \WP_Error( 'archive_limit', 'Het uitgepakte pakket is te groot.', [ 'status' => 400 ] ); }
    $opsys = 0; $attributes = 0;
    if ( $zip->getExternalAttributesIndex( $i, $opsys, $attributes ) && 3 === $opsys && 0120000 === ( ( $attributes >> 16 ) & 0170000 ) ) { return new \WP_Error( 'unsafe_archive_link', 'Symbolische links zijn niet toegestaan.', [ 'status' => 400 ] ); }
   }
   $main = $zip->statName( $plugin );
   if ( ! $main || $main['size'] > 131072 ) { return new \WP_Error( 'main_file_missing', 'Het verwachte pluginbestand ontbreekt.', [ 'status' => 400 ] ); }
   $source = $zip->getFromName( $plugin, 131073 );
   if ( ! is_string( $source ) || strlen( $source ) > 131072 ) { return new \WP_Error( 'main_file_unreadable', 'Het pluginbestand is niet leesbaar.', [ 'status' => 400 ] ); }
   $headers = self::headers( $source );
   if ( '' === $headers['name'] || ! self::version_ok( $headers['version'] ) || ! self::version_ok( $headers['requires'], true ) || ! self::version_ok( $headers['requires_php'], true ) ) { return new \WP_Error( 'invalid_headers', 'De plugin mist geldige versiegegevens.', [ 'status' => 400 ] ); }
   return $headers;
  } finally { if ( $opened ) { $zip->close(); } wp_delete_file( $file ); }
 }
 private static function lock( callable $job ) {
  global $wpdb;
  $owner = wp_json_encode( [ 'token' => wp_generate_uuid4(), 'at' => time() ] );
  $claim = function () use ( $wpdb, $owner ) { return 1 === (int) $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} (option_name,option_value,autoload) VALUES (%s,%s,'no')", self::LOCK, $owner ) ); };
  if ( ! $claim() ) {
   $old = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name=%s", self::LOCK ) ); $data = json_decode( (string) $old, true );
   if ( ! is_array( $data ) || (int) ( $data['at'] ?? 0 ) >= time() - 60 ) { return new \WP_Error( 'release_busy', 'Er wordt al een release verwerkt.', [ 'status' => 409 ] ); }
   $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s", self::LOCK, $old ) );
   if ( ! $claim() ) { return new \WP_Error( 'release_busy', 'Er wordt al een release verwerkt.', [ 'status' => 409 ] ); }
  }
  try { return $job(); }
  finally { $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name=%s AND option_value=%s", self::LOCK, $owner ) ); wp_cache_delete( self::LOCK, 'options' ); wp_cache_delete( 'notoptions', 'options' ); }
 }
 public static function publish( \WP_REST_Request $request ) {
  if ( ! self::allowed() ) { return new \WP_Error( 'forbidden', 'Je hebt geen rechten om releases vrij te geven.', [ 'status' => 403 ] ); }
  if ( strlen( $request->get_body() ) > 3 * 1024 * 1024 ) { return new \WP_Error( 'payload_too_large', 'Het verzoek is te groot.', [ 'status' => 413 ] ); }
  $plugin = $request->get_param( 'plugin' ); $encoded = $request->get_param( 'archive' ); $tested = $request->get_param( 'tested' );
  if ( ! is_string( $plugin ) || ! is_string( $encoded ) || strlen( $encoded ) > 2796204 || ! self::version_ok( $tested ) ) { return new \WP_Error( 'invalid_release', 'Ongeldige releasegegevens.', [ 'status' => 400 ] ); }
  $bytes = base64_decode( $encoded, true ); $headers = self::inspect( $bytes, $plugin ); if ( is_wp_error( $headers ) ) { return $headers; }
  $sha = hash( 'sha256', $bytes );
  $expected = $request->get_param( 'sha256' );
  if ( ! is_string( $expected ) || ! hash_equals( $sha, $expected ) ) { return new \WP_Error( 'checksum_mismatch', 'De pakketcontrole klopt niet.', [ 'status' => 400 ] ); }
  $notes = $request->get_param( 'notes' ); $notes = is_string( $notes ) ? sanitize_textarea_field( substr( $notes, 0, 8000 ) ) : '';
  return self::lock( function () use ( $plugin, $bytes, $headers, $sha, $tested, $notes ) {
   $catalog = self::catalog();
   foreach ( $catalog as $old ) { if ( is_array( $old ) && ( $old['plugin'] ?? '' ) === $plugin && ( $old['version'] ?? '' ) === $headers['version'] && ( $old['sha256'] ?? '' ) !== $sha ) { return new \WP_Error( 'immutable_version', 'Deze versie bestaat al met andere inhoud. Maak een nieuwe versie.', [ 'status' => 409 ] ); } }
   $id = substr( hash( 'sha256', $plugin . "\0" . $headers['version'] . "\0" . $sha ), 0, 32 );
   $release = [ 'id' => $id, 'state' => 'approved', 'plugin' => $plugin, 'name' => self::targets()[$plugin], 'version' => $headers['version'], 'sha256' => $sha, 'requires' => $headers['requires'], 'requires_php' => $headers['requires_php'], 'tested' => $tested, 'notes' => $notes, 'approved_at' => gmdate( 'c' ), 'approved_by' => get_current_user_id() ];
   $release['signature'] = self::signature( $release );
   update_option( self::PACKAGE . $id, base64_encode( $bytes ), false );
   if ( get_option( self::PACKAGE . $id ) !== base64_encode( $bytes ) ) { return new \WP_Error( 'package_save_failed', 'Het pakket kon niet worden bewaard.', [ 'status' => 500 ] ); }
   $catalog[$id] = $release; $keep = [];
   foreach ( $catalog as $key => $item ) { if ( is_array( $item ) && ( $item['plugin'] ?? '' ) === $plugin ) { $keep[$key] = $item; } }
   uasort( $keep, function ( $a, $b ) { return version_compare( $b['version'], $a['version'] ); } );
   $remove = array_keys( array_slice( $keep, self::KEEP, null, true ) );
   foreach ( $remove as $key ) { unset( $catalog[$key] ); }
   update_option( self::CATALOG, $catalog, false );
   if ( get_option( self::CATALOG ) !== $catalog ) { return new \WP_Error( 'catalog_save_failed', 'De release kon niet worden vrijgegeven.', [ 'status' => 500 ] ); }
   foreach ( $remove as $key ) { delete_option( self::PACKAGE . $key ); }
   delete_site_transient( 'update_plugins' );
   unset( $release['signature'] ); return $release;
  } );
 }
 private static function latest( $plugin ) {
  $latest = null;
  foreach ( self::catalog() as $release ) { if ( self::valid( $release ) && $release['plugin'] === $plugin && ( ! $latest || version_compare( $release['version'], $latest['version'], '>' ) ) ) { $latest = $release; } }
  return $latest;
 }
 public static function withdraw( \WP_REST_Request $request ) {
  if ( ! self::allowed() ) { return new \WP_Error( 'forbidden', 'Je hebt geen rechten om releases in te trekken.', [ 'status' => 403 ] ); }
  $id = $request->get_param( 'id' );
  return self::lock( function () use ( $id ) {
   $catalog = self::catalog();
   if ( ! is_string( $id ) || ! isset( $catalog[$id] ) || ! is_array( $catalog[$id] ) ) { return new \WP_Error( 'unknown_release', 'Deze release bestaat niet.', [ 'status' => 404 ] ); }
   $catalog[$id]['state'] = 'withdrawn'; $catalog[$id]['signature'] = self::signature( $catalog[$id] );
   update_option( self::CATALOG, $catalog, false ); delete_site_transient( 'update_plugins' );
   return [ 'id' => $id, 'state' => 'withdrawn', 'installed_files_changed' => false ];
  } );
 }
 public static function updates( $value ) {
  if ( ! is_object( $value ) ) { $value = new \stdClass(); }
  $value->response = isset( $value->response ) && is_array( $value->response ) ? $value->response : [];
  $value->no_update = isset( $value->no_update ) && is_array( $value->no_update ) ? $value->no_update : [];
  require_once ABSPATH . 'wp-admin/includes/plugin.php'; $installed = get_plugins();
  foreach ( self::targets() as $file => $name ) {
   if ( ! isset( $installed[$file] ) ) { continue; }
   // A WordPress.org slug collision must never replace our private targets.
   unset( $value->response[$file], $value->no_update[$file] );
   $release = self::latest( $file );
   $details = (object) [ 'id' => 'tfsc-private/' . $file, 'slug' => dirname( $file ), 'plugin' => $file, 'new_version' => $release ? $release['version'] : $installed[$file]['Version'], 'url' => admin_url( 'tools.php?page=tfsc-private-updates' ), 'package' => $release ? 'tfsc-release://' . $release['id'] : '', 'requires' => $release['requires'] ?? '', 'requires_php' => $release['requires_php'] ?? '', 'tested' => $release['tested'] ?? '', 'icons' => [], 'banners' => [] ];
   if ( $release && version_compare( $release['version'], $installed[$file]['Version'], '>' ) ) { $value->response[$file] = $details; }
   else { $value->no_update[$file] = $details; }
  }
  return $value;
 }
 public static function download( $reply, $package, $upgrader, $extra ) {
  if ( ! is_string( $package ) || ! str_starts_with( $package, 'tfsc-release://' ) ) { return $reply; }
  if ( is_wp_error( $reply ) ) { return $reply; }
  $id = substr( $package, strlen( 'tfsc-release://' ) ); $catalog = self::catalog(); $release = $catalog[$id] ?? null;
  // Native bulk updates pass plugin identity but omit type/action in hook_extra.
  if ( ! preg_match( '/^[a-f0-9]{32}$/D', $id ) || ! self::valid( $release ) || ! ( $upgrader instanceof \Plugin_Upgrader ) || ! is_array( $extra ) || ( $extra['plugin'] ?? '' ) !== $release['plugin'] || ( $extra['type'] ?? 'plugin' ) !== 'plugin' || ( $extra['action'] ?? 'update' ) !== 'update' ) { return new \WP_Error( 'unapproved_package', 'Dit updatepakket is niet vrijgegeven voor deze plugin.' ); }
  $cron = wp_doing_cron() && in_array( $release['plugin'], (array) get_site_option( 'auto_update_plugins', [] ), true );
  if ( ! current_user_can( 'update_plugins' ) && ! $cron ) { return new \WP_Error( 'forbidden_update', 'Je hebt geen rechten om deze update te installeren.' ); }
  global $wp_version;
  if ( $release['requires'] && version_compare( $wp_version, $release['requires'], '<' ) || $release['requires_php'] && version_compare( PHP_VERSION, $release['requires_php'], '<' ) ) { return new \WP_Error( 'incompatible_release', 'Deze release vereist een nieuwere WordPress- of PHP-versie.' ); }
  require_once ABSPATH . 'wp-admin/includes/plugin.php'; $installed = get_plugins();
  if ( ! isset( $installed[$release['plugin']] ) || version_compare( $release['version'], $installed[$release['plugin']]['Version'], '<' ) ) { return new \WP_Error( 'downgrade_refused', 'Deze update zou een ontbrekende plugin of oudere versie installeren.' ); }
  $encoded = get_option( self::PACKAGE . $id, '' ); $bytes = is_string( $encoded ) ? base64_decode( $encoded, true ) : false;
  if ( ! is_string( $bytes ) || ! hash_equals( $release['sha256'], hash( 'sha256', $bytes ) ) ) { return new \WP_Error( 'corrupt_package', 'Het updatepakket ontbreekt of de checksum is veranderd.' ); }
  $headers = self::inspect( $bytes, $release['plugin'] );
  if ( is_wp_error( $headers ) || $headers['version'] !== $release['version'] || $headers['requires'] !== $release['requires'] || $headers['requires_php'] !== $release['requires_php'] ) { return new \WP_Error( 'package_identity_changed', 'Het pakket komt niet overeen met de vrijgave.' ); }
  $file = self::temporary( 'tfsc-approved-update.zip' );
  if ( is_wp_error( $file ) ) { return $file; }
  if ( ! $file || file_put_contents( $file, $bytes, LOCK_EX ) !== strlen( $bytes ) ) { if ( $file ) { wp_delete_file( $file ); } return new \WP_Error( 'staging_failed', 'Het pakket kon niet worden klaargezet.' ); }
  chmod( $file, 0600 ); return $file;
 }
 public static function information( $result, $action, $args ) {
  if ( 'plugin_information' !== $action || ! is_object( $args ) || empty( $args->slug ) ) { return $result; }
  foreach ( self::targets() as $file => $name ) { if ( dirname( $file ) !== $args->slug ) { continue; } $r = self::latest( $file ); if ( ! $r ) { return $result; } return (object) [ 'name' => $name, 'slug' => $args->slug, 'version' => $r['version'], 'author' => 'The Field Service Company', 'requires' => $r['requires'], 'requires_php' => $r['requires_php'], 'tested' => $r['tested'], 'sections' => [ 'description' => 'Goedgekeurde eigen pluginrelease voor deze website.', 'changelog' => nl2br( esc_html( $r['notes'] ) ) ], 'download_link' => 'tfsc-release://' . $r['id'] ]; }
  return $result;
 }
 public static function status() {
  require_once ABSPATH . 'wp-admin/includes/plugin.php'; $installed = get_plugins(); $rows = [];
  foreach ( self::targets() as $file => $name ) { if ( ! isset( $installed[$file] ) ) { continue; } $r = self::latest( $file ); $rows[] = [ 'plugin' => $file, 'name' => $name, 'installed' => $installed[$file]['Version'], 'approved' => $r['version'] ?? null, 'sha256' => $r['sha256'] ?? null, 'update_available' => $r && version_compare( $r['version'], $installed[$file]['Version'], '>' ), 'auto_update_enabled' => in_array( $file, (array) get_site_option( 'auto_update_plugins', [] ), true ) ]; }
  return [ 'version' => self::VERSION, 'zip_available' => class_exists( '\ZipArchive' ), 'plugins' => $rows ];
 }
 public static function page() {
  if ( ! current_user_can( 'update_plugins' ) ) { return; }
  $status = self::status();
  echo '<div class="wrap"><h1>TFSC Updates</h1><p>Goedgekeurde versies van onze eigen plugins verschijnen onder Plugins en Dashboard → Updates. Een nieuwe codewijziging moet eerst worden getest en als release vrijgegeven.</p><p><a class="button button-primary" href="' . esc_url( admin_url( 'update-core.php' ) ) . '">Naar WordPress-updates</a> <a class="button" href="' . esc_url( admin_url( 'plugins.php' ) ) . '">Naar Plugins</a></p>';
  if ( ! $status['zip_available'] ) { echo '<div class="notice notice-error"><p>De PHP ZIP-extensie ontbreekt. Nieuwe releases kunnen niet veilig worden verwerkt.</p></div>'; }
  echo '<table class="widefat striped"><thead><tr><th>Plugin</th><th>Geïnstalleerd</th><th>Vrijgegeven</th><th>Status</th></tr></thead><tbody>';
  foreach ( $status['plugins'] as $row ) { echo '<tr><td>' . esc_html( $row['name'] ) . '</td><td>' . esc_html( $row['installed'] ) . '</td><td>' . esc_html( $row['approved'] ?? 'Nog geen release' ) . '</td><td>' . esc_html( $row['update_available'] ? 'Update beschikbaar' : ( $row['approved'] ? 'Bijgewerkt' : 'Nog niet aangesloten op een vrijgegeven pakket' ) ) . '</td></tr>'; }
  echo '</tbody></table><p>Automatische installatie stel je zelf per plugin in op de gewone pluginpagina. Deze voorziening schakelt dat niet automatisch in.</p></div>';
 }
}
Plugin::boot();
