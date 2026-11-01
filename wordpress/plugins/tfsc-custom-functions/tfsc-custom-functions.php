<?php
/**
 * Plugin Name: TFSC Custom Functions Plugin
 * Description: TFSC frontend styles, local fonts, search titles and compatibility shortcodes.
 * Version: 0.3.0
 * Author: The Field Service Company
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TFSC_CUSTOM_FUNCTIONS_VERSION', '0.3.0' );

// Fonts are hosted locally; Elementor must not request Google Fonts remotely.
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

// Existing icon widgets render SVGs. Retain the established font-library policy.
add_action( 'elementor/frontend/after_register_styles', 'tfsc_custom_functions_remove_icon_fonts', 20 );
function tfsc_custom_functions_remove_icon_fonts() {
    foreach ( array( 'solid', 'regular', 'brands' ) as $style ) {
        wp_deregister_style( 'elementor-icons-fa-' . $style );
    }
}

// Native block styles are left to WordPress: articles contain tables and images.

add_filter( 'elementor/utils/get_the_archive_title', 'tfsc_custom_functions_archive_title' );
function tfsc_custom_functions_archive_title( $title ) {
    if ( is_search() ) {
        return 'Jouw zoekresultaten voor:<span style="color:#00a1ff"> ' . esc_html( get_search_query() ) . '</span>';
    }
    return $title;
}

add_action( 'wp_enqueue_scripts', 'tfsc_custom_functions_enqueue_styles', PHP_INT_MAX );
function tfsc_custom_functions_enqueue_styles() {
    $filename = 'tfsc-custom-stylesheet.css';
    $path     = plugin_dir_path( __FILE__ ) . $filename;

    if ( ! is_file( $path ) || ! is_readable( $path ) ) {
        return;
    }

    $modified = filemtime( $path );
    $version  = false !== $modified ? (string) $modified : TFSC_CUSTOM_FUNCTIONS_VERSION;

    // Preserve this handle and priority for existing theme/Elementor overrides.
    wp_enqueue_style( 'theme-override', plugin_dir_url( __FILE__ ) . $filename, array(), $version, 'all' );
}

add_action( 'wp_head', 'tfsc_custom_functions_preload_fonts' );
function tfsc_custom_functions_preload_fonts() {
    $fonts = array(
        'inter-tight-v9-latin-regular.woff2',
        'barlow-semi-condensed-v16-latin-600.woff2',
    );

    foreach ( $fonts as $font ) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url( content_url( '/uploads/' . $font ) )
        );
    }
}

// Keep the public shortcode name: one saved template still references it.
add_shortcode( 'rm_get_estimated_time_to_read', 'tfsc_custom_functions_reading_time' );
function tfsc_custom_functions_reading_time() {
    $post = get_post();
    if ( ! $post instanceof WP_Post ) {
        return '';
    }

    $text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
    $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $words = preg_match_all( '/[\p{L}\p{N}]+(?:[\x{2019}\x{0027}-][\p{L}\p{N}]+)*/u', $text );
    if ( false === $words ) {
        $words = str_word_count( $text );
    }

    if ( $words < 200 ) {
        return 'Minder dan 1 minuut.';
    }
    $minutes = max( 1, (int) round( $words / 200 ) );
    return $minutes . ( 1 === $minutes ? ' minuut' : ' minuten' );
}

// No current content reference found. Retained for external/backward compatibility.
add_shortcode( 'current_page_url', 'tfsc_custom_functions_current_page_url' );
function tfsc_custom_functions_current_page_url() {
    global $wp;
    $request = isset( $wp->request ) ? $wp->request : '';
    return esc_url( home_url( $request ) );
}
