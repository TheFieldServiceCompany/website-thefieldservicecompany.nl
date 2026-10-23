<?php

/**
 * Exported from Code Snippets on dev on 2026-10-16.
 * Source snippet ID: 99
 * Active at export: true
 * Scope: global
 */

/**
 * TFSC - Rank Math breadcrumbs voor combo-archieven
 *
 * Voorbeeld:
 * Home > Vacatures > Field Service Engineer > Elektrotechniek
 */

add_filter(
    'rank_math/frontend/breadcrumb/items',
    function( $crumbs, $class ) {

        if (
            ! function_exists( 'tfsc_is_combo_archive' ) ||
            ! function_exists( 'tfsc_get_combo_archive' ) ||
            ! tfsc_is_combo_archive()
        ) {
            return $crumbs;
        }

        $combo = tfsc_get_combo_archive();

        if ( ! is_array( $combo ) || ! $combo ) {
            return $crumbs;
        }

        $base = trim( TFSC_COMBO_BASE, '/' );

        $new_crumbs = [
            [
                'Home',
                home_url( '/' ),
            ],
            [
                'Vacatures',
                home_url( '/' . $base . '/' ),
            ],
        ];

        $path_parts = [];
        $last_index = count( $combo ) - 1;

        foreach ( $combo as $index => $item ) {

            if (
                empty( $item['taxonomy'] ) ||
                empty( $item['term'] ) ||
                ! $item['term'] instanceof WP_Term
            ) {
                continue;
            }

            $term = $item['term'];

            $path_parts[] =
                sanitize_key(
                    $item['taxonomy']
                );

            if (
                ! empty( $item['path'] ) &&
                is_array( $item['path'] )
            ) {

                foreach ( $item['path'] as $path_term ) {

                    if ( $path_term instanceof WP_Term ) {
                        $path_parts[] = $path_term->slug;
                    }
                }

            } else {

                $path_parts[] = $term->slug;
            }

            $url =
                home_url(
                    '/'
                    . $base
                    . '/'
                    . implode(
                        '/',
                        array_map(
                            'rawurlencode',
                            $path_parts
                        )
                    )
                    . '/'
                );

            $new_crumbs[] = [
                $term->name,
                $index === $last_index
                    ? ''
                    : $url,
            ];
        }

        return $new_crumbs;
    },
    20,
    2
);
