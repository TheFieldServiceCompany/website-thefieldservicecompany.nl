<?php

/**
 * Related Posts CPT | Taxonomy Base Archives
 */
/**
 * TFSC - Base Taxonomies Related Posts
 *
 * Doel:
 * Toon op een virtuele taxonomy base archive alle posts
 * uit de in Elementor gekozen Source die minimaal één term
 * hebben binnen de actieve taxonomy.
 *
 * Voorbeeld:
 *
 * /beroepengids/functienamen/
 *
 * actieve taxonomy:
 * beroep_functienaam
 *
 * Elementor Source:
 * beroepengids
 *
 * resultaat:
 * alle beroepengids-posts die minimaal één
 * beroep_functienaam-term hebben.
 *
 * Elementor Query ID:
 *
 * base_taxonomies_related_posts
 */


/**
 * ============================================================
 * 1. ACTIEVE TAXONOMY BASE ARCHIVE
 * ============================================================
 */

function tfsc_base_taxonomies_related_posts_current_taxonomy() {

    if (
        ! function_exists(
            'tfsc_get_current_taxonomy_archive'
        )
    ) {
        return '';
    }

    $taxonomy =
        tfsc_get_current_taxonomy_archive();

    if (
        is_object( $taxonomy ) &&
        ! empty( $taxonomy->name )
    ) {
        $taxonomy =
            $taxonomy->name;
    }

    $taxonomy =
        sanitize_key(
            (string) $taxonomy
        );

    if (
        ! $taxonomy ||
        ! taxonomy_exists( $taxonomy )
    ) {
        return '';
    }

    return $taxonomy;
}


/**
 * ============================================================
 * 2. ELEMENTOR QUERY
 * ============================================================
 *
 * Query ID:
 * base_taxonomies_related_posts
 *
 * LET OP:
 * De Elementor Source blijft leidend.
 *
 * Deze query verandert dus NIET het post type.
 * Hij voegt alleen de actieve taxonomy base toe
 * als EXISTS-filter.
 */

add_action(
    'elementor/query/base_taxonomies_related_posts',
    function( $query ) {

        $taxonomy =
            tfsc_base_taxonomies_related_posts_current_taxonomy();

        if ( ! $taxonomy ) {

            $query->set(
                'post__in',
                [ 0 ]
            );

            return;
        }

        /**
         * Alleen posts tonen die minimaal één term
         * hebben binnen de actieve taxonomy.
         *
         * Het post type / de Source blijft volledig
         * door Elementor bepaald.
         */
        $query->set(
            'tax_query',
            [
                [
                    'taxonomy' =>
                        $taxonomy,

                    'operator' =>
                        'EXISTS',
                ],
            ]
        );
    },
    30
);
