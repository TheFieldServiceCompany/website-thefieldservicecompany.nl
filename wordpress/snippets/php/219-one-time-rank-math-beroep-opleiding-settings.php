<?php

/**
 * TFSC - Eenmalig Rank Math instellen voor beroep_opleiding
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_beroep_opleiding_rank_math_version' )
            === $version
        ) {
            return;
        }

        $titles = get_option( 'rank-math-options-titles', [] );

        if ( ! is_array( $titles ) ) {
            $titles = [];
        }

        $titles['tax_beroep_opleiding_title'] =
            '%term% voor field service | Beroepengids';

        $titles['tax_beroep_opleiding_description'] =
            '%term_description%';

        $saved = update_option(
            'rank-math-options-titles',
            $titles
        );

        $result = [
            'version'     => $version,
            'saved'       => (bool) $saved,
            'title'       => $titles['tax_beroep_opleiding_title'],
            'description' => $titles['tax_beroep_opleiding_description'],
        ];

        update_option(
            'tfsc_beroep_opleiding_rank_math_result',
            $result,
            false
        );

        update_option(
            'tfsc_beroep_opleiding_rank_math_version',
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
            '/opleidingen-rank-math',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_beroep_opleiding_rank_math_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
