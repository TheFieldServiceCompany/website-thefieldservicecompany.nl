<?php

/**
 * TFSC - Eenmalig Rank Math sitemapcache volledig vernieuwen
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_rank_math_sitemap_flush_version' )
            === $version
        ) {
            return;
        }

        $result = [
            'version' => $version,
            'flushed' => false,
            'error'   => '',
        ];

        try {
            if ( class_exists( '\\RankMath\\Sitemap\\Cache' ) ) {
                \RankMath\Sitemap\Cache::invalidate_storage();
                $result['flushed'] = true;
            } else {
                $result['error'] = 'Rank Math sitemapcacheklasse ontbreekt.';
            }
        } catch ( Throwable $exception ) {
            $result['error'] = $exception->getMessage();
        }

        update_option(
            'tfsc_rank_math_sitemap_flush_result',
            $result,
            false
        );

        update_option(
            'tfsc_rank_math_sitemap_flush_version',
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
            '/rank-math-sitemap-flush',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_rank_math_sitemap_flush_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
