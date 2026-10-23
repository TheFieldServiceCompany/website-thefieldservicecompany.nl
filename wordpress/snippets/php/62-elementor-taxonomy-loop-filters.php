<?php

/**
 * Exported from Code Snippets on dev on 2026-10-16.
 * Source snippet ID: 62
 * Active at export: true
 * Scope: global
 */

/**
 * TFSC - Elementor Taxonomy Loop filters
 *
 * Query ID's:
 *
 * taxonomy_level_X
 *
 * current_taxonomy
 * current_taxonomy_level_X
 *
 * current_term_children
 *
 * Toont op een normaal hiërarchisch taxonomy-termarchief uitsluitend de
 * directe kinderen van de huidige term. Dit is bijvoorbeeld nodig voor:
 *
 * Opleidingen
 * → Opleidingsniveau
 * → uitsluitend VMBO / LBO, MBO, HBO en WO / Universiteit
 */


/**
 * Term IDs van exact taxonomy-level ophalen.
 */
function tfsc_taxonomy_level_term_ids(
    $taxonomy,
    $level
) {

    if ( ! taxonomy_exists( $taxonomy ) ) {
        return [];
    }

    $terms = get_terms( [
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ] );

    if (
        is_wp_error( $terms ) ||
        ! $terms
    ) {
        return [];
    }

    $ids = [];

    foreach ( $terms as $term ) {

        if (
            count(
                get_ancestors(
                    $term->term_id,
                    $taxonomy,
                    'taxonomy'
                )
            ) === (int) $level
        ) {
            $ids[] = (int) $term->term_id;
        }
    }

    return $ids;
}


/**
 * Elementor Taxonomy Loop.
 */
add_filter(
    'elementor/loop_taxonomy/args',
    function( $args, $settings, $display_settings ) {

        $query_id = sanitize_key(
            $settings['term_taxonomy_id']
            ?? ''
        );

        if ( ! $query_id ) {
            return $args;
        }


        /**
         * ====================================================
         * taxonomy_level_X
         * ====================================================
         */

        if (
            preg_match(
                '/^taxonomy_level_([0-9]+)$/',
                $query_id,
                $matches
            )
        ) {

            $taxonomy =
                $args['taxonomy']
                ?? '';

            if ( is_array( $taxonomy ) ) {
                $taxonomy = reset( $taxonomy );
            }

            if ( ! taxonomy_exists( $taxonomy ) ) {
                return $args;
            }

            unset(
                $args['term_taxonomy_id']
            );

            $ids =
                tfsc_taxonomy_level_term_ids(
                    $taxonomy,
                    (int) $matches[1]
                );

            $args['include'] =
                $ids ?: [ 0 ];

            return $args;
        }


        /**
         * ====================================================
         * current_term_children
         * ====================================================
         *
         * Werkt op een normaal taxonomy-termarchief en toont
         * alleen de directe kinderen van de huidige term.
         *
         * Bladterm zonder kinderen:
         * → bewust geen terms tonen.
         */

        if ( 'current_term_children' === $query_id ) {

            $term = get_queried_object();

            if (
                ! $term instanceof WP_Term ||
                ! taxonomy_exists( $term->taxonomy ) ||
                ! is_taxonomy_hierarchical( $term->taxonomy )
            ) {
                $args['include'] = [ 0 ];
                return $args;
            }

            unset(
                $args['term_taxonomy_id'],
                $args['post_taxonomy_term_taxonomy_id'],
                $args['include']
            );

            $args['taxonomy']   = $term->taxonomy;
            $args['parent']     = (int) $term->term_id;
            $args['hide_empty'] = false;
            $args['orderby']    = 'name';
            $args['order']      = 'ASC';

            return $args;
        }


        /**
         * ====================================================
         * current_taxonomy
         * current_taxonomy_level_X
         * ====================================================
         */

        $is_current =
            'current_taxonomy' === $query_id;

        $has_level =
            preg_match(
                '/^current_taxonomy_level_([0-9]+)$/',
                $query_id,
                $matches
            );

        if (
            ! $is_current &&
            ! $has_level
        ) {
            return $args;
        }


        $taxonomy =
            get_query_var(
                'tfsc_taxonomy_archive'
            );

        if (
            ! $taxonomy &&
            isset( $GLOBALS['wp_query'] )
        ) {
            $taxonomy =
                $GLOBALS['wp_query']->get(
                    'tfsc_taxonomy_archive'
                );
        }

        if (
            ! $taxonomy ||
            ! taxonomy_exists( $taxonomy )
        ) {
            $args['include'] = [ 0 ];
            return $args;
        }


        unset(
            $args['term_taxonomy_id']
        );

        $args['taxonomy']   = $taxonomy;
        $args['hide_empty'] = false;
        $args['orderby']    = 'name';
        $args['order']      = 'ASC';


        /**
         * current_taxonomy = alle terms.
         */
        if ( $is_current ) {
            return $args;
        }


        /**
         * current_taxonomy_level_X.
         */
        $ids =
            tfsc_taxonomy_level_term_ids(
                $taxonomy,
                (int) $matches[1]
            );

        $args['include'] =
            $ids ?: [ 0 ];

        return $args;
    },
    20,
    3
);
