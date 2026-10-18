<?php

/**
 * TFSC - Eenmalig rewrites en Rank Math-cache vernieuwen na opleidingen-splitsing
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_split_opleiding_rewrite_flush_version' )
            === $version
        ) {
            return;
        }

        $result = [
            'version'          => $version,
            'rewrites_flushed' => false,
            'sitemap_flushed'  => false,
            'error'            => '',
        ];

        try {
            flush_rewrite_rules( false );
            $result['rewrites_flushed'] = true;

            if ( class_exists( '\RankMath\Sitemap\Cache' ) ) {
                \RankMath\Sitemap\Cache::invalidate_storage();
                $result['sitemap_flushed'] = true;
            }
        } catch ( Throwable $exception ) {
            $result['error'] = $exception->getMessage();
        }

        update_option(
            'tfsc_split_opleiding_rewrite_flush_result',
            $result,
            false
        );

        update_option(
            'tfsc_split_opleiding_rewrite_flush_version',
            $version,
            false
        );
    },
    999
);


add_action(
    'rest_api_init',
    function() {
        register_rest_route(
            'tfsc-debug/v1',
            '/split-opleiding-rewrite-flush',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_split_opleiding_rewrite_flush_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
