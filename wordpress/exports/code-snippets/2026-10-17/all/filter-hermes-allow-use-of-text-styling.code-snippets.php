<?php

/**
 * Filter | Hermes allow use of text styling
 */
add_filter( 'wp_kses_allowed_html', function( $allowed, $context ) {

    if ( 'pre_term_description' !== $context ) {
        return $allowed;
    }

    $allowed['h2'] = array();
    $allowed['h3'] = array();
    $allowed['p'] = array();
    $allowed['strong'] = array();
    $allowed['em'] = array();
    $allowed['ul'] = array();
    $allowed['ol'] = array();
    $allowed['li'] = array();
    $allowed['br'] = array();

    return $allowed;

}, 10, 2 );
