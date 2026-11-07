<?php
/** Adversarial cases run only inside the disposable WordPress database. */
require '/wordpress/wp-load.php';
require_once '/wordpress/wp-admin/includes/upgrade.php';
\RankMath\Installer::create_tables( [ 'redirections' ] );
update_option( 'rank_math_modules', [ 'redirections' ] );
rank_math()->settings->set( 'general', 'redirections_post_redirect', true );
$watcher = new \RankMath\Redirections\Watcher();
$checks = [];
function security_check( $condition, $message ) {
    if ( ! $condition ) { throw new Exception( 'FAIL security: ' . $message ); }
    $GLOBALS['checks'][] = $message;
}
function security_post( $author ) {
    return wp_insert_post( [ 'post_type' => 'post', 'post_status' => 'publish', 'post_author' => $author, 'post_title' => 'Disposable attack fixture ' . wp_generate_uuid4() ] );
}
function security_rule( $source ) {
    $r = \RankMath\Redirections\Redirection::from( [ 'sources' => [ [ 'pattern' => $source, 'comparison' => 'exact' ] ], 'url_to' => 'https://example.test/legitimate/', 'header_code' => 301, 'status' => 'active' ] );
    $r->save(); return $r->get_id();
}
function security_request( $user, $method, $route, $params ) {
    wp_set_current_user( $user );
    $r = new WP_REST_Request( $method, $route ); $r->set_body_params( $params );
    return rest_do_request( $r );
}
$author = get_user_by( 'login', 'security-author' )->ID;
$subscriber = wp_insert_user( [ 'user_login' => 'security-subscriber', 'user_pass' => 'fixture-only', 'user_email' => 'subscriber@example.test', 'role' => 'subscriber' ] );
$admin = get_user_by( 'login', 'localadmin' )->ID;
$attack_post_id = security_post( $author ); $other = security_post( $admin );
$victim = security_rule( 'security-unrelated' );
$payload = [ 'objectID' => $attack_post_id, 'objectType' => 'post', 'meta' => [ 'rank_math_auto_redirect' => [ $victim ] ] ];
foreach ( [ 0 => 'anonymous', $subscriber => 'subscriber' ] as $user => $label ) {
    $r = security_request( $user, 'POST', '/rankmath/v1/updateMeta', $payload );
    security_check( in_array( $r->get_status(), [ 401, 403 ], true ), $label . ' cannot update post metadata' );
    $r = security_request( $user, 'DELETE', '/wp/v2/posts/' . $attack_post_id, [ 'force' => true ] );
    security_check( in_array( $r->get_status(), [ 401, 403 ], true ), $label . ' cannot delete the post' );
}
$r = security_request( $author, 'DELETE', '/wp/v2/posts/' . $other, [ 'force' => true ] );
security_check( $r->get_status() === 403, 'Author cannot delete another user\'s content' );

// A later legitimate URL change must never sign IDs injected into the old field.
$r = security_request( $author, 'POST', '/rankmath/v1/updateMeta', $payload );
security_check( $r->get_status() === 200, 'Attack exercises the actually permitted legacy-meta write' );
$r = security_request( $author, 'POST', '/wp/v2/posts/' . $attack_post_id, [ 'slug' => 'security-genuine-change' ] );
security_check( $r->get_status() === 200, 'Author can perform a legitimate own-post URL change' );
$trusted = \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'post' );
security_check( count( $trusted ) >= 1 && ! in_array( (int) $victim, $trusted, true ), 'Genuine watcher event does not launder poisoned legacy IDs' );
$proof = get_post_meta( $attack_post_id, '_tfsc_rm_verified_redirects', true );
$r = security_request( $author, 'POST', '/rankmath/v1/updateMeta', [ 'objectID' => $attack_post_id, 'objectType' => 'post', 'meta' => [ '_tfsc_rm_verified_redirects' => [ 'ids' => [ $victim ], 'signature' => str_repeat( '0', 64 ) ] ] ] );
security_check( get_post_meta( $attack_post_id, '_tfsc_rm_verified_redirects', true ) === $proof, 'Rank Math metadata endpoint cannot replace the private proof' );
$r = security_request( $author, 'POST', '/wp/v2/settings', [ '_tfsc_rm_redirect_signing_key' => str_repeat( '0', 64 ) ] );
security_check( in_array( $r->get_status(), [ 401, 403 ], true ), 'Author cannot update the server signing option through settings' );

// Assume even an unsafe third-party meta writer: the signature remains binding.
update_post_meta( $attack_post_id, '_tfsc_rm_verified_redirects', [ 'ids' => [ $victim ], 'signature' => $proof['signature'] ] );
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'post' ) === [], 'Changing proof IDs invalidates the signature' );
update_post_meta( $other, '_tfsc_rm_verified_redirects', $proof );
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $other, 'post' ) === [], 'A proof cannot be copied to a different post' );
update_post_meta( $attack_post_id, '_tfsc_rm_verified_redirects', $proof );
update_metadata( 'term', $attack_post_id, '_tfsc_rm_verified_redirects', $proof );
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'term' ) === [], 'A post proof cannot authorize term cleanup' );
$original_blog_id = $GLOBALS['blog_id'];
$GLOBALS['blog_id'] = (int) $original_blog_id + 1;
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'post' ) === [], 'Changing the site identity invalidates the copied proof' );
$GLOBALS['blog_id'] = $original_blog_id;
$key = get_option( '_tfsc_rm_redirect_signing_key' );
delete_option( '_tfsc_rm_redirect_signing_key' );
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'post' ) === [] && get_option( '_tfsc_rm_redirect_signing_key', false ) === false, 'Missing key fails closed without creating a key on reads' );
update_option( '_tfsc_rm_redirect_signing_key', [ 'invalid' ] );
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'post' ) === [], 'Malformed key fails closed without a PHP error' );
update_option( '_tfsc_rm_redirect_signing_key', $key );
security_check( \TFSC\RankMathBreadcrumbs\verified_redirect_ids( $attack_post_id, 'post' ) === $trusted, 'Restoring the original key validates the genuine proof' );

$bad_ids = [ 0, -1, true, false, null, 1.5, INF, NAN, [], new stdClass(), '1 OR 1=1', '1);DROP TABLE posts;--', '+1', '01', '1e3', "1\0", "1\n", ' 1', '1 ', str_repeat( '9', 1000 ) ];
foreach ( $bad_ids as $i => $bad ) {
    security_check( \TFSC\RankMathBreadcrumbs\associated_redirect_ids( [ $bad ] ) === [], 'Reject hostile redirect-ID case ' . $i );
}
security_check( \TFSC\RankMathBreadcrumbs\associated_redirect_ids( [ PHP_INT_MAX, (string) PHP_INT_MAX ] ) === [ PHP_INT_MAX ], 'Maximum representable ID is accepted without overflow or duplicate' );

// Tampered proofs on real deletion must preserve unrelated rules.
foreach ( [ [], 'invalid', [ 'ids' => [ $victim ], 'signature' => [] ], [ 'ids' => [ $victim ], 'signature' => str_repeat( '0', 64 ) ], [ 'ids' => [ $victim ], 'signature' => $proof['signature'] ] ] as $i => $invalid ) {
    $bad_post = security_post( $author );
    update_post_meta( $bad_post, 'rank_math_auto_redirect', [ $victim ] );
    update_post_meta( $bad_post, '_tfsc_rm_verified_redirects', $invalid );
    wp_delete_post( $bad_post, true );
    security_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $victim ), 'Tampered proof deletion cannot remove unrelated rule, case ' . $i );
}

// Genuine server/cron deletion works without granting user 0 any capabilities.
update_post_meta( $attack_post_id, '_tfsc_rm_verified_redirects', $proof );
wp_set_current_user( 0 );
wp_delete_post( $attack_post_id, true );
security_check( ! \RankMath\Redirections\DB::get_redirection_by_id( $trusted[0] ) && (bool) \RankMath\Redirections\DB::get_redirection_by_id( $victim ), 'Trusted cron deletion removes only the genuinely associated rule' );

$GLOBALS['wp_query'] = new WP_Query( [ 'p' => $other ] );
$GLOBALS['post'] = get_post( $other );
foreach ( [ null, false, 'bad', [ 'WebPage' => 'bad' ], [ 'WebPage' => new stdClass() ] ] as $i => $data ) {
    security_check( \TFSC\RankMathBreadcrumbs\preserve_marked_entities( $data ) === $data, 'Malformed upstream graph is ignored, case ' . $i );
}
$GLOBALS['post']->post_content = '<a href="https://example.org/?x=&quot;}]&lt;/script&gt;" data-schema-attribute="mentions">Text &amp; quotes "</a>';
$schema = \TFSC\RankMathBreadcrumbs\preserve_marked_entities( [ 'WebPage' => [ '@type' => 'WebPage' ] ] );
$json = wp_json_encode( wp_kses_post_deep( $schema ), JSON_UNESCAPED_SLASHES );
security_check( json_decode( $json, true ) === $schema && strpos( $json, '</script>' ) === false, 'Hostile marked-link text cannot break JSON encoding or close a script' );
$broken = [ 'WebPage' => [ 'about' => 'bad', 'mentions' => 'bad' ] ];
security_check( \TFSC\RankMathBreadcrumbs\preserve_marked_entities( $broken ) === $broken, 'Malformed existing entity properties cannot cause a PHP append error' );
$GLOBALS['post']->post_content = str_repeat( '<a', 20000 );
security_check( \TFSC\RankMathBreadcrumbs\preserve_marked_entities( [ 'WebPage' => [ '@type' => 'WebPage' ] ] ) === [ 'WebPage' => [ '@type' => 'WebPage' ] ], 'Long malformed anchor input does not create entities' );

$_SERVER['REQUEST_URI'] = '/unmatched?url=https://attacker.invalid/&id=1%20OR%201=1';
$rule = [ 'sources' => [ [ 'pattern' => 'trusted?kind=fixture', 'comparison' => 'exact' ] ] ];
security_check( \TFSC\RankMathBreadcrumbs\match_query_redirect( false, $rule ) === false, 'Attacker-controlled query cannot match an unrelated stored rule' );
security_check( (bool) \RankMath\Redirections\DB::get_redirection_by_id( $victim ), 'Read-only schema and query attacks cannot alter redirects' );

echo json_encode( [ 'scope' => 'isolated WordPress only', 'passed' => count( $checks ), 'checks' => $checks ], JSON_PRETTY_PRINT );
