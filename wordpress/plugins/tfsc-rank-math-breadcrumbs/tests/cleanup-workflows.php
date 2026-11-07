<?php
/** Real deletion/redirect workflows, exclusively in the disposable database. */
require '/wordpress/wp-load.php';
require_once '/wordpress/wp-admin/includes/upgrade.php';
$checks = [];
function cleanup_check( $condition, $message ) {
    if ( ! $condition ) { throw new Exception( 'FAIL: ' . $message ); }
    $GLOBALS['checks'][] = $message;
}
function cleanup_fixture_rule( $source, $target ) {
    $rule = \RankMath\Redirections\Redirection::from( [
        'sources' => [ [ 'pattern' => $source, 'comparison' => 'exact' ] ],
        'url_to' => $target, 'header_code' => 301, 'status' => 'active',
    ] );
    $rule->save();
    return $rule->get_id();
}
\RankMath\Installer::create_tables( [ 'redirections' ] );
update_option( 'rank_math_modules', [ 'redirections' ] );
rank_math()->settings->set( 'general', 'redirections_post_redirect', true );
register_post_type( 'vacature', [ 'public' => true, 'show_ui' => true, 'rewrite' => [ 'slug' => 'vacature' ] ] );
register_taxonomy( 'functie_cleanup', 'vacature', [ 'public' => true, 'rewrite' => [ 'slug' => 'functies' ] ] );
$watcher = new \RankMath\Redirections\Watcher();
cleanup_check( \TFSC\RankMathBreadcrumbs\can_manage_auto_redirects(), 'Cleanup is enabled only with Free and the redirect module active' );
$id = wp_insert_post( [ 'post_type' => 'vacature', 'post_status' => 'publish', 'post_title' => 'Cleanup fixture', 'post_name' => 'cleanup-before' ] );
wp_update_post( [ 'ID' => $id, 'post_name' => 'cleanup-after' ] );
$ids = get_post_meta( $id, 'rank_math_auto_redirect', true );
cleanup_check( is_array( $ids ) && count( $ids ) === 1, 'Actual Free slug change saves a Pro-compatible association' );
$rule_id = $ids[0];
do_action( 'rank_math/redirection/post_updated', $rule_id, $id );
cleanup_check( get_post_meta( $id, 'rank_math_auto_redirect', true ) === $ids, 'Repeated notification cannot duplicate the association' );
wp_update_post( [ 'ID' => $id, 'post_name' => 'cleanup-latest' ] );
cleanup_check( get_post_meta( $id, 'rank_math_auto_redirect', true ) === $ids, 'Another URL change retains the existing redirect association' );
$manual = cleanup_fixture_rule( 'manual-kept', get_permalink( $id ) );
wp_trash_post( $id );
cleanup_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $rule_id ), 'Moving content to trash does not delete its automatic redirect' );
wp_untrash_post( $id );
cleanup_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $rule_id ), 'Restoring content from trash retains its redirect' );
wp_delete_post( $id, true );
cleanup_check( ! \RankMath\Redirections\DB::get_redirection_by_id( $rule_id ), 'Permanent deletion removes the associated automatic redirect' );
cleanup_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $manual ), 'Unassociated manual redirect survives, even with the same destination' );
global $wpdb;
cleanup_check( (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}rank_math_redirections_cache WHERE redirection_id=%d", $rule_id ) ) === 0, 'Free deletion API also purges matching redirect cache rows' );

$term = wp_insert_term( 'Cleanup term', 'functie_cleanup', [ 'slug' => 'cleanup-term-before' ] );
wp_update_term( $term['term_id'], 'functie_cleanup', [ 'slug' => 'cleanup-term-after' ] );
$term_ids = get_term_meta( $term['term_id'], 'rank_math_auto_redirect', true );
cleanup_check( is_array( $term_ids ) && count( $term_ids ) === 1, 'Actual taxonomy slug change saves the term association' );
wp_delete_term( $term['term_id'], 'functie_cleanup' );
cleanup_check( ! \RankMath\Redirections\DB::get_redirection_by_id( $term_ids[0] ), 'Permanent term deletion removes its automatic redirect' );

$legacy = wp_insert_post( [ 'post_type' => 'vacature', 'post_status' => 'publish', 'post_title' => 'Legacy fixture' ] );
$legacy_rule = cleanup_fixture_rule( 'legacy-auto', get_permalink( $legacy ) );
update_post_meta( $legacy, 'rank_math_auto_redirect', [ (string) $legacy_rule ] );
\TFSC\RankMathBreadcrumbs\seal_redirect_ids( $legacy, 'post', [ $legacy_rule ] );
wp_delete_post( $legacy, true );
cleanup_check( ! \RankMath\Redirections\DB::get_redirection_by_id( $legacy_rule ), 'Associations previously stored by Pro remain compatible' );
$unmarked = wp_insert_post( [ 'post_type' => 'vacature', 'post_status' => 'publish', 'post_title' => 'Unmarked fixture' ] );
wp_delete_post( $unmarked, true );
cleanup_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $manual ), 'Deleting unmarked content cannot trigger broad redirect deletion' );
cleanup_check( \TFSC\RankMathBreadcrumbs\associated_redirect_ids( [ 0, -1, false, true, 1.5, 'all', '1,2', [], '2', 2 ] ) === [ 2 ], 'Malformed association data is ignored and valid IDs are deduplicated' );
cleanup_check( \TFSC\RankMathBreadcrumbs\associated_redirect_ids( '2' ) === [], 'A malformed scalar field cannot select redirects' );

$disabled = wp_insert_post( [ 'post_type' => 'vacature', 'post_status' => 'publish', 'post_title' => 'Disabled fixture' ] );
$disabled_rule = cleanup_fixture_rule( 'disabled-auto', get_permalink( $disabled ) );
update_post_meta( $disabled, 'rank_math_auto_redirect', [ $disabled_rule ] );
\TFSC\RankMathBreadcrumbs\seal_redirect_ids( $disabled, 'post', [ $disabled_rule ] );
update_option( 'rank_math_modules', [] );
wp_delete_post( $disabled, true );
cleanup_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $disabled_rule ), 'Disabled redirect module prevents automatic cleanup' );
update_option( 'rank_math_modules', [ 'redirections' ] );

$pro_post = wp_insert_post( [ 'post_type' => 'vacature', 'post_status' => 'publish', 'post_title' => 'Pro guard fixture' ] );
$pro_rule = cleanup_fixture_rule( 'pro-owned-auto', get_permalink( $pro_post ) );
define( 'RANK_MATH_PRO_VERSION', 'fixture' );
do_action( 'rank_math/redirection/post_updated', $pro_rule, $pro_post );
cleanup_check( get_post_meta( $pro_post, 'rank_math_auto_redirect', true ) === '', 'When Pro is active, fallback tracking does not run' );
update_post_meta( $pro_post, 'rank_math_auto_redirect', [ $pro_rule ] );
wp_delete_post( $pro_post, true );
cleanup_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $pro_rule ), 'When Pro is active, fallback deletion defers to Pro' );
echo json_encode( [ 'scope' => 'disposable database only', 'passed' => count( $checks ), 'checks' => $checks ], JSON_PRETTY_PRINT );
