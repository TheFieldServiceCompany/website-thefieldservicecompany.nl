<?php

/**
 * Update linkzoeker van text editor met alle normale taxonomy terms [v2.0] optimized
 */
/**
 * TFSC - Alle taxonomy termen in WordPress linkzoeker
 *
 * Alle publieke native taxonomy-term archieven.
 * Geen virtuele combo-URL's.
 */

add_filter(
    'wp_link_query',
    function( $results, $query ) {

        $search = trim(
            (string) ( $query['s'] ?? '' )
        );

        if ( '' === $search ) {
            return $results;
        }

        $taxonomies = array_keys(
            get_taxonomies(
                [
                    'public'             => true,
                    'publicly_queryable' => true,
                ],
                'objects'
            )
        );

        $taxonomies = array_values(
            array_diff(
                $taxonomies,
                [ 'post_format' ]
            )
        );

        if ( ! $taxonomies ) {
            return $results;
        }

        $terms = get_terms( [
            'taxonomy'   => $taxonomies,
            'hide_empty' => false,
            'search'     => $search,
            'number'     => 50,
        ] );

        if (
            is_wp_error( $terms ) ||
            ! $terms
        ) {
            return $results;
        }

        foreach ( $terms as $term ) {

            if ( ! $term instanceof WP_Term ) {
                continue;
            }

            $GLOBALS['tfsc_force_native_term_link'] = true;
            $url = get_term_link( $term );
            unset( $GLOBALS['tfsc_force_native_term_link'] );

            if ( is_wp_error( $url ) ) {
                continue;
            }

            $taxonomy =
                get_taxonomy(
                    $term->taxonomy
                );

            $results[] = [
                'ID' =>
                    'term-'
                    . $term->taxonomy
                    . '-'
                    . $term->term_id,

                'title' =>
                    $term->name,

                'permalink' =>
                    $url,

                'info' =>
                    $taxonomy->labels->singular_name
                    ?? $term->taxonomy,
            ];
        }

        return $results;
    },
    20,
    2
);
