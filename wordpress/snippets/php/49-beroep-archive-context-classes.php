<?php

/**
 * Exported from Code Snippets on dev on 2026-10-16.
 * Source snippet ID: 49
 * Active at export: true
 * Scope: global
 */

/**
 * Deze snippet is voor de archives van beroepen, om de query die informatie bevat te tonen (beroepen posts of current taxonomies), zodat we 1 archive template kunnen gebruiken voor allen.
 *
 * TFSC - Archive context classes
 *
 * Voor één generiek archive-template met:
 *
 * .tfsc_query_beroepen
 * .tfsc_query_taxonomies_current
 *
 */

add_filter( 'body_class', function( $classes ) {

    // CPT archive Beroepen.
    if ( is_post_type_archive( 'beroepengids' ) ) {
        $classes[] = 'tfsc-archive-beroepen';
    }

    // TFSC taxonomy-base archive.
    if ( get_query_var( 'tfsc_taxonomy_archive' ) ) {
        $classes[] = 'tfsc-archive-taxonomy';
    }

    return $classes;
});
