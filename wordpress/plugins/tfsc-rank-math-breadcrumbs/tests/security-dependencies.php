<?php
require '/wordpress/wp-load.php';
$checks = [];
function dependency_check( $condition, $message ) {
    if ( ! $condition ) { throw new Exception( $message ); }
    $GLOBALS['checks'][] = $message;
}
dependency_check( ! class_exists( '\\RankMath\\Helper' ) && ! class_exists( '\\Elementor\\Widget_Base' ), 'Both dependencies are actually inactive' );
dependency_check( ! \TFSC\RankMathBreadcrumbs\can_manage_auto_redirects(), 'Cleanup is disabled without Rank Math' );
$graph = [ 'WebPage' => [ '@type' => 'WebPage' ] ];
dependency_check( \TFSC\RankMathBreadcrumbs\preserve_marked_entities( $graph ) === $graph, 'Schema filter safely leaves data unchanged without Rank Math' );
dependency_check( \TFSC\RankMathBreadcrumbs\match_query_redirect( false, [ 'sources' => [ [ 'pattern' => 'x', 'comparison' => 'exact' ] ] ] ) === false, 'Redirect filter safely stops without its engine' );
dependency_check( ! \TFSC\RankMathBreadcrumbs\enable_single_location_sitemap( false ), 'Sitemap fallback safely stops without its engine' );
\TFSC\RankMathBreadcrumbs\register_widget( new stdClass() );
dependency_check( true, 'Widget callback safely stops without Elementor' );
echo json_encode( [ 'scope' => 'inactive dependencies in real WordPress', 'passed' => count( $checks ), 'checks' => $checks ], JSON_PRETTY_PRINT );
