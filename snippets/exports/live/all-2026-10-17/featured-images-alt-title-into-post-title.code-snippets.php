<?php

/**
 * Featured images Alt Title into Post Title
 */
/**
 * Dynamische ALT en TITLE voor featured images.
 * Gebruikt automatisch de titel van de huidige post.
 */
add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {

    if ( ! is_singular() ) {
        return $attr;
    }

    $post_id = get_queried_object_id();

    if ( ! $post_id ) {
        return $attr;
    }

    $featured_image_id = get_post_thumbnail_id( $post_id );

    // Alleen aanpassen als deze afbeelding de featured image
    // van de huidige post is.
    if ( (int) $attachment->ID !== (int) $featured_image_id ) {
        return $attr;
    }

    $post_title = get_the_title( $post_id );

    if ( ! $post_title ) {
        return $attr;
    }

    // ALT-tekst
    $attr['alt'] = $post_title;

    // HTML title attribute
    $attr['title'] = $post_title;

    return $attr;

}, 10, 3 );
