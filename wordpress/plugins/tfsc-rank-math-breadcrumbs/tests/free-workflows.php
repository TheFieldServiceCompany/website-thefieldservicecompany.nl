<?php
/** Exercise Free authoring workflows only in the disposable test database. */
require '/wordpress/wp-load.php';
require_once '/wordpress/wp-admin/includes/upgrade.php';
$checks = [];
function workflow_check( $condition, $message ) {
    if ( ! $condition ) { throw new Exception( 'FAIL: ' . $message ); }
    $GLOBALS['checks'][] = $message;
}
workflow_check( ! defined( 'RANK_MATH_PRO_VERSION' ), 'Workflow test runs without Rank Math Pro' );
\RankMath\Installer::create_tables( [ 'redirections' ] );
rank_math()->settings->set( 'general', 'redirections_post_redirect', true );
register_post_type( 'vacature', [ 'public' => true, 'show_ui' => true, 'rewrite' => [ 'slug' => 'vacature' ] ] );
register_taxonomy( 'functie_fixture', 'vacature', [ 'public' => true, 'rewrite' => [ 'slug' => 'functies' ] ] );
$watcher = new \RankMath\Redirections\Watcher();
$id = wp_insert_post( [ 'post_type' => 'vacature', 'post_status' => 'publish', 'post_title' => 'Workflow fixture', 'post_name' => 'before-fixture' ] );
$before = get_permalink( $id );
wp_update_post( [ 'ID' => $id, 'post_name' => 'after-fixture' ] );
$after = get_permalink( $id );
workflow_check( $before !== $after, 'Actual WordPress update changes the fixture CPT permalink' );
$source = trim( wp_parse_url( $before, PHP_URL_PATH ), '/' );
$redirect = \RankMath\Redirections\DB::match_redirections( $source );
workflow_check( $redirect && (int) $redirect['header_code'] === 301 && $redirect['url_to'] === $after, 'Free automatically creates the correct CPT slug redirect' );

$term = wp_insert_term( 'Before fixture', 'functie_fixture', [ 'slug' => 'before-term' ] );
$term_before = get_term_link( $term['term_id'], 'functie_fixture' );
wp_update_term( $term['term_id'], 'functie_fixture', [ 'slug' => 'after-term' ] );
$term_after = get_term_link( $term['term_id'], 'functie_fixture' );
$term_source = trim( wp_parse_url( $term_before, PHP_URL_PATH ), '/' );
$term_redirect = \RankMath\Redirections\DB::match_redirections( $term_source );
workflow_check( $term_redirect && $term_redirect['url_to'] === $term_after && (int) $term_redirect['header_code'] === 301, 'Free automatically creates the correct taxonomy slug redirect' );

$protected = get_post( $id );
$protected->post_password = 'local-fixture-only';
$paper = new \RankMath\Paper\Singular();
$paper->set_object( $protected );
rank_math()->settings->set( 'titles', 'noindex_password_protected', true );
workflow_check( ( $paper->robots()['index'] ?? '' ) === 'noindex', 'Installed Free renderer applies the password-protection noindex setting' );
rank_math()->settings->set( 'titles', 'noindex_password_protected', false );
workflow_check( ( $paper->robots()['index'] ?? '' ) !== 'noindex', 'Disabled password noindex setting is respected in Free' );

wp_delete_post( $id, true );
workflow_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $redirect['id'], 'active' ), 'Free keeps the stored redirect after deletion; automatic Pro cleanup is not present' );
wp_delete_term( $term['term_id'], 'functie_fixture' );
workflow_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $term_redirect['id'], 'active' ), 'Free also keeps the taxonomy redirect after deletion' );
echo json_encode( [ 'scope' => 'disposable WordPress database; no production mutations', 'passed' => count( $checks ), 'checks' => $checks ], JSON_PRETTY_PRINT );
