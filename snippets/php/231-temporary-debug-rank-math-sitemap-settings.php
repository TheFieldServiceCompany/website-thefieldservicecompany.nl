<?php

/**
 * TFSC - Tijdelijk uitlezen relevante Rank Math sitemapinstellingen
 *
 * Alleen-lezen controle-endpoint. Na controle deactiveren.
 */

add_action(
    'rest_api_init',
    function() {
        register_rest_route(
            'tfsc-debug/v1',
            '/rank-math-sitemap-settings',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    $settings = get_option( 'rank-math-options-sitemap', [] );
                    $settings = is_array( $settings ) ? $settings : [];

                    return rest_ensure_response(
                        array_filter(
                            $settings,
                            function( $key ) {
                                return false !== strpos( $key, 'empty' ) ||
                                    false !== strpos( $key, 'beroep_opleiding' ) ||
                                    false !== strpos( $key, 'beroep_studierichting' );
                            },
                            ARRAY_FILTER_USE_KEY
                        )
                    );
                },
            ]
        );
    }
);
