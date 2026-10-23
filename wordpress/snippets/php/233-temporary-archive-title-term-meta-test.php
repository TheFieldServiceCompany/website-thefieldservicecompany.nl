<?php

/**
 * TFSC - Temporary archive-title term-meta test endpoint
 *
 * Temporary, administrator-only REST endpoint used to verify that the
 * `pods_archive_title` term meta overrides the visible Elementor archive H1.
 * Keep this snippet inactive outside the test.
 */

add_action(
    'rest_api_init',
    function() {
        register_rest_route(
            'tfsc-debug/v1',
            '/archive-title-term-meta-test',
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function( WP_REST_Request $request ) {
                    $term_id = (int) $request->get_param( 'term_id' );
                    $value   = (string) $request->get_param( 'value' );

                    $term = get_term( $term_id );

                    if ( ! $term instanceof WP_Term || is_wp_error( $term ) ) {
                        return new WP_Error(
                            'tfsc_invalid_term',
                            'De opgegeven term bestaat niet.',
                            [ 'status' => 400 ]
                        );
                    }

                    if ( '' === $value ) {
                        delete_term_meta( $term_id, 'pods_archive_title' );
                    } else {
                        update_term_meta(
                            $term_id,
                            'pods_archive_title',
                            sanitize_text_field( $value )
                        );
                    }

                    clean_term_cache( $term_id, $term->taxonomy );

                    return rest_ensure_response(
                        [
                            'term_id'  => $term_id,
                            'taxonomy' => $term->taxonomy,
                            'stored'   => get_term_meta(
                                $term_id,
                                'pods_archive_title',
                                true
                            ),
                        ]
                    );
                },
                'args'                => [
                    'term_id' => [
                        'type'     => 'integer',
                        'required' => true,
                    ],
                    'value'   => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                ],
            ]
        );
    }
);
