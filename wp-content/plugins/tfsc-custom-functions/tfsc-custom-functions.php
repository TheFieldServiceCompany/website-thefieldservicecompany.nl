<?php

/**
* Plugin Name: TFSC Custom Functions Plugin
* Description: Alle aanpassingen in de functions.php worden in deze custom file gezet. Edit hem via ftp en dan de plugin directory.
* Author: The Field Service Company
* Version: 0.1
*/

/* Your code goes below here. */

// ** Hoe ik deze plugin gebouwd heb staat hier: https://wpmudev.com/blog/why-you-shouldnt-use-functions-php **//

// filter for stopping elementor to load google fonts from website Google and load them locally//
add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );

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
return 'Jouw zoekresultaten voor:<span style="color:#00a1ff"> ' . get_search_query();
}
return $title;
}


/** add form attachments to email and delete from server /
* https://github.com/elementor/elementor/issues/4868
* @param string $filename Unique file name.
* @param string $ext File extension, eg. ".png".
* @param string $dir Directory path.
* @param callable|null $unique_filename_callback Callback function that generates the unique file name.
*/

add_filter( 'wp_unique_filename', function( $filename, $ext, $dir, $unique_filename_callback ) {
$FORM_FIELD_FILE_ID = 'cv_upload';	// enter YOUR field ID

static $file_count = 0; //used to get the current file when multiple uploads in a form

// first make sure its an upload via elementor forms
// or you can once again only add this filter hook if you know that this is
// a desired form as in the above example
if ( false === strpos( $dir, '/elementor/forms' ) ) {
    return $filename;
}

// get the original file name
$original_filename = $_FILES['form_fields'][$FORM_FIELD_FILE_ID][$file_count]['name'];

// Bump counter for next file
$file_count++;
// Return your desired file name (original_filename)
return $original_filename;

}, 10, 4);


use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Form_Record;

/**
* Class Elementor_Form_Email_Attachments
* Send Elementor Form upload field as attachments to email
*/
class Elementor_Form_Email_Attachments {
// Set to true if you want the files to be removed from
// the server after they are sent by email
const DELETE_ATTACHMENT_FROM_SERVER = false;
public $attachments_array = [];

public function __construct() {
add_action( 'elementor_pro/forms/process', [ $this, 'init_form_email_attachments' ], 11, 2 );
}

/**
* @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
* @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
*/
public function init_form_email_attachments( $record, $ajax_handler ) {
// check if we have attachments
$files = $record->get( 'files' );
if ( empty( $files ) ) {
    return;
}
// Store attachment in local var
foreach ( $files as $id => $files_array ){

    /* New Fix For multiple files */

    $len = 20;

for($i = 0; $i < $len; $i++) {
            $this->attachments_array[] = $files_array['path'][$i];

}

}

// if local var has attachments setup filter hook
if ( 0 < count( $this->attachments_array ) ) {
    add_filter( 'wp_mail', [ $this, 'wp_mail' ] );
    add_action( 'elementor_pro/forms/new_record', [ $this, 'remove_wp_mail_filter' ], 5 );
}
}

public function remove_wp_mail_filter() {
if ( self::DELETE_ATTACHMENT_FROM_SERVER ) {

    foreach ( $this->attachments_array as $uploaded_file ) {
        unlink( $uploaded_file );
    }
}

$this->attachments_array = [];
remove_filter( 'wp_mail', [ $this, 'wp_mail' ] );

}

public function wp_mail( $args ) {
// check for no attachment flag
$no_attachments = strpos($args['message'], '[elementor_form_no_attachment]');
if ( FALSE !== $no_attachments ) {
// remove the flag from the message
$args['message'] = str_replace('[elementor_form_no_attachment]', '', $args['message']);
} else {
// attach files instead
$args['attachments'] = $this->attachments_array;
}

return $args;
}
}
new Elementor_Form_Email_Attachments();

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


//* Preloading JS files -> https://macarthur.me/posts/preloading-javascript-in-wordpress */

add_action('wp_head', function () {
    global $wp_scripts;

    foreach ($wp_scripts->queue as $handle) {
      $script = $wp_scripts->registered[$handle];

      //-- If version is set, append to end of source.
      $source = $script->src . ($script->ver ? "?ver={$script->ver}" : "");

      //-- Spit out the tag.
      echo "<link rel='preload' href='{$source}' as='script'/>\n";
    }
  }, 1);

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