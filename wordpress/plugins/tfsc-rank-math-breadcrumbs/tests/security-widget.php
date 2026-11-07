<?php
require '/wordpress/wp-load.php';
$checks = 0;
$fixture_id = wp_insert_post( [ 'post_status' => 'publish', 'post_type' => 'post', 'post_title' => 'Security render fixture' ] );
$GLOBALS['wp_query'] = new WP_Query( [ 'p' => $fixture_id ] );
$GLOBALS['post'] = get_post( $fixture_id );
foreach ( [ 'script', 'iframe', 'nav onclick="alert(1)"', '"><script>alert(1)</script>', '../wp-config.php', "nav\0", 'javascript:alert(1)', [ 'nav' ], null ] as $payload ) {
    $widget = \Elementor\Plugin::instance()->elements_manager->create_element_instance( [
        'id' => 'secur123', 'elType' => 'widget', 'widgetType' => 'breadcrumbs',
        'settings' => [ 'html_tag' => $payload ], 'elements' => [],
    ] );
    ob_start(); $widget->print_element(); $html = ob_get_clean();
    if ( stripos( $html, '<script' ) !== false || stripos( $html, '<iframe' ) !== false || stripos( $html, 'onclick=' ) !== false || strpos( $html, 'rank-math-breadcrumb' ) === false ) {
        throw new Exception( 'Hostile widget tag was not rejected safely.' );
    }
    ++$checks;
}
echo json_encode( [ 'scope' => 'real Elementor with hostile html_tag settings', 'passed' => $checks ] );
// The next request verifies graceful absence of both dependencies.
update_option( 'active_plugins', [ 'tfsc-rank-math-breadcrumbs/tfsc-rank-math-breadcrumbs.php' ] );
