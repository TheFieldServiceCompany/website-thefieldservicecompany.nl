<?php

/**
 * TFSC - Rank Math description voor single vacatures
 *
 * Voorgesteld WordPress Code Snippets ID: 234
 * Repository-status: klaar voor review; niet automatisch uitgerold naar LIVE
 *
 * DOEL
 * ============================================================
 * Geeft iedere publieke single vacature een unieke, beheerbare meta description.
 * De volgorde is bewust:
 *
 * 1. een handmatig ingevulde Rank Math-description blijft leidend;
 * 2. anders wordt de handmatige WordPress-samenvatting/excerpt gebruikt;
 * 3. anders volgt een compacte fallback op basis van titel en locatie.
 *
 * Open Graph en Twitter/X krijgen dezelfde description. De snippet verandert
 * geen SEO-title, canonical, robots, schema, vacaturearchief of zichtbare
 * Elementor-inhoud.
 */

/**
 * Normaliseer en begrens tekst voor gebruik als meta description.
 */
function tfsc_single_vacancy_clean_description( $text, $max_length = 160 ) {
    $text = strip_shortcodes( (string) $text );
    $text = wp_strip_all_tags( $text, true );
    $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $text = preg_replace( '/\s+/u', ' ', $text );
    $text = trim( (string) $text );

    if ( '' === $text ) {
        return '';
    }

    $length = function_exists( 'mb_strlen' )
        ? mb_strlen( $text, 'UTF-8' )
        : strlen( $text );

    if ( $length <= $max_length ) {
        return $text;
    }

    $slice = function_exists( 'mb_substr' )
        ? mb_substr( $text, 0, $max_length - 1, 'UTF-8' )
        : substr( $text, 0, $max_length - 1 );

    $slice = preg_replace( '/\s+\S*$/u', '', $slice );
    $slice = rtrim( (string) $slice, " \t\n\r\0\x0B,.;:-" );

    return $slice . '…';
}

/**
 * Geef de meest specifieke gekoppelde locatieterm terug.
 */
function tfsc_single_vacancy_location( $post_id ) {
    $terms = get_the_terms( $post_id, 'locatie' );

    if ( ! is_array( $terms ) || empty( $terms ) ) {
        return '';
    }

    usort(
        $terms,
        function( $left, $right ) {
            $left_depth  = count( get_ancestors( $left->term_id, 'locatie', 'taxonomy' ) );
            $right_depth = count( get_ancestors( $right->term_id, 'locatie', 'taxonomy' ) );

            return $right_depth <=> $left_depth;
        }
    );

    return sanitize_text_field( $terms[0]->name );
}

/**
 * Bouw de description voor de huidige single vacature.
 */
function tfsc_single_vacancy_description( $current = '' ) {
    if ( ! is_singular( 'vacature' ) ) {
        return $current;
    }

    $post_id = (int) get_queried_object_id();
    $post    = get_post( $post_id );

    if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
        return $current;
    }

    /**
     * Redactionele Rank Math-invoer heeft altijd voorrang op automatisering.
     */
    $manual = tfsc_single_vacancy_clean_description(
        get_post_meta( $post_id, 'rank_math_description', true )
    );

    if ( '' !== $manual ) {
        return $manual;
    }

    /**
     * De vacature-excerpt is ook de korte tekst die op vacaturekaarten wordt
     * gebruikt en is daardoor de beste automatische bron.
     */
    $excerpt = tfsc_single_vacancy_clean_description( $post->post_excerpt );

    if ( '' !== $excerpt ) {
        return $excerpt;
    }

    $title    = tfsc_single_vacancy_clean_description( get_the_title( $post_id ), 100 );
    $location = tfsc_single_vacancy_location( $post_id );

    if ( '' !== $location ) {
        $fallback = sprintf(
            'Bekijk de vacature %1$s in %2$s. Lees de functie, vereisten en arbeidsvoorwaarden en solliciteer via The Field Service Company.',
            $title,
            $location
        );
    } else {
        $fallback = sprintf(
            'Bekijk de vacature %s. Lees de functie, vereisten en arbeidsvoorwaarden en solliciteer via The Field Service Company.',
            $title
        );
    }

    return tfsc_single_vacancy_clean_description( $fallback );
}

add_filter(
    'rank_math/frontend/description',
    'tfsc_single_vacancy_description',
    90
);

add_filter(
    'rank_math/opengraph/facebook/og_description',
    'tfsc_single_vacancy_description',
    90
);

add_filter(
    'rank_math/opengraph/twitter/twitter_description',
    'tfsc_single_vacancy_description',
    90
);
