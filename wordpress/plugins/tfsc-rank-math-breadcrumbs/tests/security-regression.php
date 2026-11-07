<?php
/** Authenticated low-privilege regression, only in the disposable database. */
require '/wordpress/wp-load.php';
require_once '/wordpress/wp-admin/includes/upgrade.php';
\RankMath\Installer::create_tables( [ 'redirections' ] );
update_option( 'rank_math_modules', [ 'redirections' ] );
$author = wp_insert_user( [ 'user_login' => 'security-author', 'user_pass' => 'fixture-only', 'user_email' => 'author@example.test', 'role' => 'author' ] );
$own = wp_insert_post( [ 'post_type' => 'post', 'post_status' => 'publish', 'post_author' => $author, 'post_title' => 'Disposable security fixture' ] );
$rule = \RankMath\Redirections\Redirection::from( [ 'sources' => [ [ 'pattern' => 'unrelated-admin-rule', 'comparison' => 'exact' ] ], 'url_to' => 'https://example.test/unrelated/', 'header_code' => 301, 'status' => 'active' ] );
$rule->save();
$victim = $rule->get_id();
wp_set_current_user( $author );
$server = rest_get_server();
if ( ! isset( $server->get_routes()['/rankmath/v1/updateMeta'] ) ) { (new \RankMath\Rest\Shared())->register_routes(); }
$request = new WP_REST_Request( 'POST', '/rankmath/v1/updateMeta' );
$request->set_body_params( [ 'objectID' => $own, 'objectType' => 'post', 'meta' => [ 'rank_math_auto_redirect' => [ $victim ] ] ] );
$response = rest_do_request( $request );
$stored = get_post_meta( $own, 'rank_math_auto_redirect', true );
$can_edit_meta = current_user_can('edit_post_meta',$own,'rank_math_auto_redirect');
$delete = new WP_REST_Request( 'DELETE', '/wp/v2/posts/' . $own );
$delete->set_param( 'force', true );
$deleted = rest_do_request( $delete );
$survives = (bool) \RankMath\Redirections\DB::get_redirection_by_id( $victim );
echo json_encode( [ 'scope' => 'local disposable fixture', 'author_can_manage_options' => current_user_can('manage_options'), 'author_can_edit_metadata' => $can_edit_meta, 'metadata_response' => $response->get_status(), 'stored_ids' => $stored, 'own_post_delete_response' => $deleted->get_status(), 'unrelated_rule_survives' => $survives ], JSON_PRETTY_PRINT );
if ( ! $survives ) { throw new Exception( 'SECURITY REGRESSION: untrusted post metadata must not authorize deletion of unrelated redirects.' ); }
