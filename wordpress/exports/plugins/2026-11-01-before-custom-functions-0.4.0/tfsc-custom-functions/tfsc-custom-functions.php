<?php

/**
* Plugin Name: TFSC Custom Functions Plugin
* Description: Alle aanpassingen in de functions.php worden in deze custom file gezet. Edit hem via ftp en dan de plugin directory.
* Author: The Field Service Company
* Version: 0.2.0
*/

/* Your code goes below here. */

// ** Hoe ik deze plugin gebouwd heb staat hier: https://wpmudev.com/blog/why-you-shouldnt-use-functions-php **//

// filter om de hardcoded sidebar niet meer in de html te hebben //
add_filter( 'show_recent_comments_widget_style', '__return_false', 99 );

///*****Removing Elementor Icons (Eicons) and Font Awesome plus Google Fonts: https://www.launch2success.com/guide/5-hacks-to-speed-up-your-elementor-wordpress-website/ / */

/*--- Remove Google Fonts ---*/
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

/*--- Remove Font Awesome ---*/
add_action( 'elementor/frontend/after_register_styles',function() {
foreach( [ 'solid', 'regular', 'brands' ] as $style ) {
    wp_deregister_style( 'elementor-icons-fa-' . $style );
}
}, 20 );

/*---  Remove Eicons:  ---*/
#add_action( 'wp_enqueue_scripts', 'remove_default_stylesheet', 20 );
#function remove_default_stylesheet() {
#wp_deregister_style( 'elementor-icons' );
#}

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
wp_dequeue_style( 'wp-block-library' );
wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css' );


/*change zoekresultatenpagina title: https://elementor.com/help/hello-theme-tips/*/

add_filter( 'elementor/utils/get_the_archive_title','archive_callback' );
function archive_callback( $title ) {
if ( is_search() ) {
return 'Jouw zoekresultaten voor:<span style="color:#00a1ff"> ' . esc_html( get_search_query() ) . '</span>';
}
return $title;
}


/*
 * Elementor-bestanden worden door Elementor Pro en TFSC Form Bridge verwerkt.
 * De vroegere globale bestandsnaam- en e-mailfilters stonden vast op het oude
 * veld-ID `cv_upload` en grepen ook in bij andere formulieren. Die legacycode
 * hoort daarom niet meer in deze algemene functiesplugin.
 */

/* Voeg nieuwe CSS StyleSheet toe adds stylesheet file to the end of the queue : Opmerking 4 onder artikel -> https://developer.wordpress.org/reference/functions/wp_enqueue_style/ */
/*Custom Stylesheet*/
function wpdocs_override_stylesheets()
{
    $dir = plugin_dir_url(__FILE__);
    wp_enqueue_style('theme-override', $dir . '/tfsc-custom-stylesheet.css', array(), '0.1.0', 'all');
}
add_action('wp_enqueue_scripts', 'wpdocs_override_stylesheets', PHP_INT_MAX);


//* Add Fonts or other code to <head> -> https://themightymo.com/how-to-preload-font-in-wordpress-functional-plugin/ */
function tmm_add_to_header(){
    ?>
     <link rel="preload" href="/wp-content/uploads/custom-font-types/inter-v12-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
     <link rel="preload" href="/wp-content/uploads/custom-font-types/inter-v12-latin-700.woff2" as="font" type="font/woff2" crossorigin>
       <?php
   };

   add_action('wp_head', 'tmm_add_to_header');


//** Rank Math Custom Code to add Estimate Read Time to articles https://wordpress.org/support/topic/estimated-reading-time-4/ **//

add_shortcode( 'rm_get_estimated_time_to_read', 'define_custom_shortcode_function' );

function define_custom_shortcode_function () {
    global $post;
    $content = wp_strip_all_tags( $post->post_content );
    $time = str_word_count($content)/200;
    if( $time < 1 ){
        return 'Minder dan 1 minuut.';
    }
    return round( $time ) . ' minuten';
}


#############MAKE SHORTCODE CURRENTPAGEURL############
function current_page_url_shortcode() {
    global $wp;
    return home_url( $wp->request );
    }
    add_shortcode('current_page_url', 'current_page_url_shortcode');

?>
