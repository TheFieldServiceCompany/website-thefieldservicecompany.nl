<?php

/**
 * TFSC - Rank Math centrale vacature-description
 *
 * WordPress Code Snippets ID: nog niet toegewezen
 * Repository-status: gereed voor import, nog niet uitgerold naar LIVE
 * Onderzoeksdatum: 23 oktober 2026
 *
 * DOEL
 * ============================================================
 * Laat een individuele vacature zonder handmatig ingevulde Rank Math-
 * description de centrale template volgen uit:
 *
 * Rank Math SEO > Titles & Meta > Vacature > Single Vacature Description
 *
 * WAAROM VIA EEN FILTER?
 * ============================================================
 * Rank Math 1.0.280 behandelt een opgeslagen WordPress-excerpt vóór de
 * centrale description-template. Daardoor kan een vacature zonder handmatige
 * Rank Math-description toch alleen het excerpt als meta-description krijgen.
 *
 * Deze filter wijzigt die prioriteit uitsluitend voor het posttype
 * `vacature`:
 *
 * 1. een handmatig ingevulde Rank Math-description blijft leidend;
 * 2. anders wordt de actuele centrale Rank Math-template gebruikt;
 * 3. ontbreekt Rank Math of de centrale template, dan blijft de bestaande
 *    description ongewijzigd.
 *
 * Er staat geen SEO-tekst hardcoded in deze snippet. Variabelen zoals
 * %title%, %sep% en %excerpt% worden door Rank Math vervangen.
 *
 * AFBAKENING
 * ============================================================
 * De snippet verandert geen titles, canonicals, robots, schema, archieven,
 * vacature-combo's, zichtbare excerpts of WordPress-content.
 *
 * Onderbouwing:
 * - Rank Math master / versie 1.0.280:
 *   includes/frontend/paper/class-singular.php
 * - Rank Math-supportadvies:
 *   https://wordpress.org/support/topic/rankmath-meta-description-not-visible-in-all-pages/
 */

add_filter(
    'rank_math/frontend/description',
    function( $description ) {
        if ( ! is_singular( 'vacature' ) ) {
            return $description;
        }

        $post = get_queried_object();

        if (
            ! $post instanceof WP_Post
            || ! class_exists( '\\RankMath\\Post' )
            || ! class_exists( '\\RankMath\\Helper' )
        ) {
            return $description;
        }

        $custom_description = \RankMath\Post::get_meta(
            'description',
            $post->ID
        );

        if (
            is_string( $custom_description )
            && '' !== trim( $custom_description )
        ) {
            return $description;
        }

        $template = \RankMath\Helper::get_settings(
            "titles.pt_{$post->post_type}_description"
        );

        if ( ! is_string( $template ) || '' === trim( $template ) ) {
            return $description;
        }

        return \RankMath\Helper::replace_vars( $template, $post );
    },
    90
);
