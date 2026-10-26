<?php
/**
 * Run ONLY against a disposable WordPress database.
 * TFSC_TEST_WP_LOAD=/path/to/wp-load.php php tests/integration.php
 * wp-config.php must define TFSC_MEDIA_FILTERS_TESTS as true.
 */
define( 'WP_ADMIN', true );
$_SERVER['HTTP_HOST'] = '127.0.0.1:8765';
$load = getenv( 'TFSC_TEST_WP_LOAD' );
if ( ! $load || ! is_file( $load ) ) {
	exit( "Set TFSC_TEST_WP_LOAD to a disposable WordPress installation.\n" );
}
require $load;
if ( ! defined( 'TFSC_MEDIA_FILTERS_TESTS' ) || true !== TFSC_MEDIA_FILTERS_TESTS ) {
	exit( "Refusing to run outside a disposable test database.\n" );
}
require_once dirname( __DIR__ ) . '/tfsc-media-filters.php';
wp_set_current_user( 1 );
TFSC_Media_Filters::activate();
$plugin = $GLOBALS['tfsc_media_filters'];
$checks = 0;
function check( $condition, $label ) {
	global $checks, $wpdb;
	if ( ! $condition ) {
		throw new RuntimeException( 'FAIL: ' . $label . ' | DB error: ' . $wpdb->last_error );
	}
	++$checks;
	echo "PASS $label\n";
}
register_taxonomy( 'happyfiles_category', 'attachment', array( 'hierarchical' => true ) );
// Clean up fixtures left behind by an interrupted run, in this disposable DB.
foreach ( get_posts( array( 'post_type' => 'attachment', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_tfsc_test', 'meta_value' => 'yes' ) ) as $old_id ) {
	wp_delete_attachment( $old_id, true );
}
foreach ( get_terms( array( 'taxonomy' => 'happyfiles_category', 'hide_empty' => false, 'fields' => 'ids' ) ) as $old_term ) {
	wp_delete_term( $old_term, 'happyfiles_category' );
}
$folder_a = wp_insert_term( 'Test Productfotos', 'happyfiles_category' )['term_id'];
$folder_b = wp_insert_term( 'Test Anders', 'happyfiles_category' )['term_id'];
$child = wp_insert_term( 'Test Submap', 'happyfiles_category', array( 'parent' => $folder_a ) )['term_id'];
$fixtures = array();
function media_fixture( $name, $w, $h, $size, $folder, $mime = 'image/webp' ) {
	global $fixtures;
	$id = wp_insert_attachment( array( 'post_title' => $name, 'post_status' => 'inherit', 'post_mime_type' => $mime ) );
	update_post_meta( $id, '_wp_attached_file', '2026/10/' . $name );
	$meta = array();
	if ( null !== $w ) { $meta['width'] = $w; }
	if ( null !== $h ) { $meta['height'] = $h; }
	if ( null !== $size ) { $meta['filesize'] = $size; }
	wp_update_attachment_metadata( $id, $meta );
	wp_set_object_terms( $id, $folder, 'happyfiles_category' );
	update_post_meta( $id, '_tfsc_test', 'yes' );
	$fixtures[] = $id;
	return $id;
}
$land = media_fixture( 'product-land.WEBP', 1600, 900, 204800, $folder_a );
$portrait = media_fixture( 'product-staand.jpg', 800, 1200, 512000, $folder_a, 'image/jpeg' );
$square = media_fixture( 'product-vierkant.jpeg', 1200, 1200, 102400, $folder_a, 'image/jpeg' );
$other = media_fixture( 'product-anders.webp', 1800, 900, 204800, $folder_b );
$nested = media_fixture( 'product-submap.webp', 1800, 900, 204800, $child );
$unknown = media_fixture( 'product-onbekend.svg', null, null, null, $folder_a, 'image/svg+xml' );
$pdf = media_fixture( 'product.pdf', null, null, 102400, $folder_a, 'application/pdf' );
update_post_meta( $portrait, '_wp_attachment_image_alt', 'Productfoto' );
update_post_meta( $square, '_wp_attachment_image_alt', '   ' );
$initial_state = TFSC_Media_Filters::reset_index();
while ( empty( $initial_state['ready'] ) ) { $initial_state = $plugin->index_batch( $initial_state ); }
function query_media( $filters, $extra = array() ) {
	global $folder_a;
	return new WP_Query( array_merge( array(
		'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => -1,
		'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids',
		'tax_query' => array( array( 'taxonomy' => 'happyfiles_category', 'field' => 'term_id', 'terms' => array( $folder_a ), 'include_children' => false ) ),
		'tfsc_mf_filters' => TFSC_Media_Filters::sanitize( $filters ),
	), $extra ) );
}
check( query_media( array( 'extension' => 'webp', 'width_min' => 1200 ) )->posts === array( $land ), 'Extension + width retain folder scope' );
check( query_media( array( 'width_min' => 1200, 'width_max' => 1200, 'height_min' => 1200, 'height_max' => 1200 ) )->posts === array( $square ), 'Exact width and height' );
check( query_media( array( 'extension' => '.jpg, .jpeg' ) )->posts === array( $portrait, $square ), 'JPG and JPEG remain distinct and can be combined' );
check( query_media( array( 'size_min' => 200, 'size_max' => 200 ) )->posts === array( $land ), 'Inclusive KB boundaries' );
check( query_media( array( 'size_min' => 100.001, 'size_max' => 199.999 ) )->posts === array(), 'Fractional KB boundaries' );
check( query_media( array( 'orientation' => 'portrait' ) )->posts === array( $portrait ), 'Portrait filter' );
check( query_media( array( 'orientation' => 'square' ) )->posts === array( $square ), 'Square filter' );
check( query_media( array( 'alt' => 'present' ) )->posts === array( $portrait ), 'Present alt text, images only' );
check( query_media( array( 'alt' => 'missing' ) )->posts === array( $land, $square, $unknown ), 'Missing, empty and whitespace alt text; excludes PDF' );
check( query_media( array( 'extension' => 'pdf' ) )->posts === array( $pdf ), 'Non-image extensions' );
check( ! in_array( $unknown, query_media( array( 'width_min' => 0 ) )->posts, true ), 'Unknown dimensions are not zero' );
check( ! in_array( $unknown, query_media( array( 'size_min' => 0 ) )->posts, true ), 'Unknown filesize is not zero' );
$tax = array( 'relation' => 'OR', array( 'taxonomy' => 'happyfiles_category', 'terms' => array( $folder_a ), 'include_children' => true ), array( 'taxonomy' => 'happyfiles_category', 'terms' => array( $folder_b ), 'include_children' => false ) );
check( query_media( array( 'extension' => 'webp' ), array( 'tax_query' => $tax ) )->posts === array( $land, $other, $nested ), 'Nested OR folder query and subfolders preserved' );
check( query_media( array( 'extension' => 'webp' ), array( 'post__in' => array( $other ) ) )->posts === array(), 'Does not widen post__in/folder intersection' );
check( query_media( array( 'extension' => 'webp' ), array( 'meta_query' => array( 'relation' => 'OR', array( 'key' => '_tfsc_test', 'value' => 'no' ), array( 'key' => '_tfsc_test', 'compare' => 'NOT EXISTS' ) ) ) )->posts === array(), 'Existing OR meta query is preserved' );
check( query_media( array( 'width_min' => 1000 ), array( 's' => 'vierkant' ) )->posts === array( $square ), 'Search combines with dimensions' );
$paged = query_media( array( 'extension' => 'jpg,jpeg,webp' ), array( 'posts_per_page' => 1, 'paged' => 2 ) );
check( $paged->posts === array( $portrait ) && 3 === (int) $paged->found_posts && 3 === (int) $paged->max_num_pages, 'Server pagination and total counts' );
$initial = array( 'post_type' => 'attachment', 'tax_query' => $tax, 'meta_query' => array( array( 'key' => '_tfsc_test', 'value' => 'yes' ) ), 'post__in' => array( $land ), 's' => 'product', 'paged' => 2 );
$_REQUEST['query']['tfsc_mf'] = array( 'width_min' => 1200 );
$args = apply_filters( 'ajax_query_attachments_args', $initial );
foreach ( $initial as $key => $value ) { check( $args[ $key ] === $value, 'AJAX preserves ' . $key ); }
check( $args['tfsc_mf_filters'] === array( 'width_min' => 1200 ), 'Grid AJAX accepts sanitized filters' );
unset( $_REQUEST['query']['tfsc_mf'] );
$_GET['tfsc_mf'] = array( 'extension' => 'webp' );
$pagenow = 'upload.php';
$list = new WP_Query();
$wp_the_query = $list;
$list->query( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids', 'tax_query' => array( array( 'taxonomy' => 'happyfiles_category', 'terms' => $folder_a, 'include_children' => false ) ) ) );
check( $list->posts === array( $land ), 'Native list view retains selected folder' );
$wp_the_query = null;
unset( $_GET['tfsc_mf'] );
$current_screen = new class { public function in_admin() { return false; } };
check( count( query_media( array( 'extension' => 'webp' ) )->posts ) === 5, 'Frontend attachment queries stay untouched' );
$current_screen = null;
$subscriber = wp_insert_user( array( 'user_login' => 'tfsc-test-subscriber', 'user_pass' => wp_generate_password(), 'role' => 'subscriber' ) );
wp_set_current_user( $subscriber );
check( count( query_media( array( 'extension' => 'webp' ) )->posts ) === 5, 'Users without upload permission cannot activate filters' );
wp_set_current_user( 1 );
require_once ABSPATH . 'wp-admin/includes/user.php';
wp_delete_user( $subscriber );
$clean = TFSC_Media_Filters::sanitize( array( 'extension' => "WEBP, .jpg, jpg, x');DROP TABLE posts;--", 'width_min' => array( '12' ), 'height_max' => -10, 'size_min' => '1,5', 'orientation' => 'injection', 'alt' => array() ) );
check( ! isset( $clean['width_min'], $clean['height_max'], $clean['orientation'], $clean['alt'] ) && 1.5 === $clean['size_min'] && false === strpos( $clean['extension'], "'" ), 'Untrusted values and SQL payload are safely sanitized' );
check( TFSC_Media_Filters::sanitize( 'not-an-array' ) === array(), 'Malformed filter shape' );
wp_update_attachment_metadata( $land, array( 'width' => 700, 'height' => 1200, 'filesize' => 307200 ) );
check( query_media( array( 'orientation' => 'portrait', 'extension' => 'webp', 'size_min' => 300 ) )->posts === array( $land ), 'Image edits automatically update index and cached queries' );
update_post_meta( $land, '_wp_attached_file', '2026/10/product-land.png' );
check( query_media( array( 'extension' => 'png' ) )->posts === array( $land ), 'File replacement updates indexed extension' );
delete_post_meta( $land, '_wp_attachment_metadata' );
check( query_media( array( 'extension' => 'png', 'width_min' => 1 ) )->posts === array(), 'Removed metadata clears obsolete dimensions' );
// More than one batch, including an attachment added after the high-water mark.
for ( $i = 0; $i < 105; $i++ ) { media_fixture( 'batch-' . $i . '.jpg', 300, 200, 5000, $folder_b, 'image/jpeg' ); }
$state = TFSC_Media_Filters::reset_index();
check( 0 === count( query_media( array( 'extension' => 'jpg' ) )->posts ), 'Incomplete index never presents partial results' );
check( 5 === count( query_media( array() )->posts ), 'Unfiltered library works during indexing' );
$new = media_fixture( 'new-after-start.webp', 1400, 900, 8000, $folder_a );
$state = $plugin->index_batch( $state );
check( ! $state['ready'] && 100 === $state['processed'], 'First bounded batch' );
$saved = get_option( TFSC_Media_Filters::STATE );
$state = $plugin->index_batch( $saved );
check( $state['ready'] && $state['processed'] === $state['total'], 'Index resumes and finishes from saved cursor' );
check( query_media( array( 'extension' => 'webp' ) )->posts === array( $new ), 'Uploads during indexing remain searchable' );
$local = wp_upload_dir()['basedir'] . '/tfsc-test-file.jpg';
file_put_contents( $local, str_repeat( 'x', 2048 ) );
$local_id = media_fixture( 'tfsc-test-file.jpg', null, null, null, $folder_a, 'image/jpeg' );
update_attached_file( $local_id, $local );
check( in_array( $local_id, query_media( array( 'size_min' => 2, 'size_max' => 2 ) )->posts, true ), 'Local filesize fallback when metadata lacks size' );
wp_delete_attachment( $local_id, true );
check( ! $wpdb->get_var( $wpdb->prepare( 'SELECT attachment_id FROM ' . TFSC_Media_Filters::table() . ' WHERE attachment_id=%d', $local_id ) ), 'Deleting media removes its index row' );
check( term_exists( $folder_a, 'happyfiles_category' ), 'HappyFiles folder terms remain intact' );
foreach ( $fixtures as $id ) { wp_delete_attachment( $id, true ); }
foreach ( array( $child, $folder_a, $folder_b ) as $term ) { wp_delete_term( $term, 'happyfiles_category' ); }
echo "\n$checks integration checks passed.\n";
