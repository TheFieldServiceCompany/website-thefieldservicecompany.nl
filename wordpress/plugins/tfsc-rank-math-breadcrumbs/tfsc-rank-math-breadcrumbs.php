<?php
/**
 * Plugin Name: TFSC Rank Math Breadcrumbs
 * Description: Keeps existing Elementor breadcrumb widgets working with Rank Math Free.
 * Version: 0.1.1
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: The Field Service Company
 * License: GPL-2.0-or-later
 * Text Domain: tfsc-rank-math-breadcrumbs
 */

namespace TFSC\RankMathBreadcrumbs;

defined( 'ABSPATH' ) || exit;

/**
 * Reuse the stored widget type so template IDs, classes and settings survive.
 * Pro keeps ownership of its widget whenever it is active.
 */
function register_widget( $widgets_manager ) {

    if (
        defined( 'RANK_MATH_PRO_VERSION' ) ||
        ! function_exists( 'rank_math_the_breadcrumbs' ) ||
        ! class_exists( '\Elementor\Widget_Base' )
    ) {
        return;
    }

    require_once __DIR__ . '/widget-breadcrumbs.php';
    $widgets_manager->register( new Widget_Breadcrumbs() );
}

add_action( 'elementor/widgets/register', __NAMESPACE__ . '\\register_widget', 100 );
