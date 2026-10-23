<?php

/**
 * TFSC - Eenmalig Rank Math instellen voor gesplitste opleidingstaxonomieën
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_split_opleiding_rank_math_version' )
            === $version
        ) {
            return;
        }

        $taxonomies = [
            'beroep_opleidingsniveau',
            'beroep_studierichting',
        ];

        $titles = get_option( 'rank-math-options-titles', [] );
        $sitemap = get_option( 'rank-math-options-sitemap', [] );

        $titles = is_array( $titles ) ? $titles : [];
        $sitemap = is_array( $sitemap ) ? $sitemap : [];

        foreach ( $taxonomies as $taxonomy_name ) {
            $titles[ 'tax_' . $taxonomy_name . '_title' ] =
                '%term% voor field service | Beroepengids';

            $titles[ 'tax_' . $taxonomy_name . '_description' ] =
                '%term_description%';

            $sitemap[ 'tax_' . $taxonomy_name . '_sitemap' ] = 'on';
        }

        update_option( 'rank-math-options-titles', $titles );
        update_option( 'rank-math-options-sitemap', $sitemap );

        if ( class_exists( '\RankMath\Sitemap\Cache' ) ) {
            \RankMath\Sitemap\Cache::invalidate_storage();
        }

        $result = [
            'version'    => $version,
            'taxonomies' => [],
        ];

        foreach ( $taxonomies as $taxonomy_name ) {
            $result['taxonomies'][ $taxonomy_name ] = [
                'title'       => $titles[ 'tax_' . $taxonomy_name . '_title' ],
                'description' => $titles[ 'tax_' . $taxonomy_name . '_description' ],
                'sitemap'     => $sitemap[ 'tax_' . $taxonomy_name . '_sitemap' ],
            ];
        }

        update_option(
            'tfsc_split_opleiding_rank_math_result',
            $result,
            false
        );

        update_option(
            'tfsc_split_opleiding_rank_math_version',
            $version,
            false
        );
    },
    100
);


add_action(
    'rest_api_init',
    function() {
        register_rest_route(
            'tfsc-debug/v1',
            '/split-opleiding-rank-math',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_split_opleiding_rank_math_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
