<?php

/**
 * Exported from Code Snippets on dev on 2026-10-16.
 * Source snippet ID: 39
 * Active at export: true
 * Scope: global
 */

add_filter( 'elementor/utils/get_the_archive_title', function( $title ) {

    if ( is_category() ) {
        return single_cat_title( '', false );
    }

    if ( is_tag() ) {
        return single_tag_title( '', false );
    }

    if ( is_tax() ) {
        return single_term_title( '', false );
    }

    if ( is_post_type_archive() ) {
        return post_type_archive_title( '', false );
    }

    return $title;
});
