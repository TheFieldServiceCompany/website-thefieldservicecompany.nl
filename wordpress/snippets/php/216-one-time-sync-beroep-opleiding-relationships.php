<?php

/**
 * TFSC - Eenmalige migratie relaties Beroepengids Opleidingen
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 *
 * Opleidingsniveau-kindtermen worden op slug gekoppeld aan de
 * vacaturetaxonomie `opleidingsniveau`.
 *
 * Studierichting-kindtermen worden op slug gekoppeld aan de
 * vacaturetaxonomie `vac_studierichting`.
 *
 * De parenttermen krijgen bewust geen vacature-relatie.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_beroep_opleiding_rel_sync_version' ) === $version
        ) {
            return;
        }

        if (
            ! function_exists( 'pods' ) ||
            ! taxonomy_exists( 'beroep_opleiding' )
        ) {
            return;
        }

        $branches = [
            'opleidingsniveau' => [
                'target_taxonomy' => 'opleidingsniveau',
                'field'           => 'relationship_opleidingsniveau',
            ],
            'studierichting' => [
                'target_taxonomy' => 'vac_studierichting',
                'field'           => 'relationship_vac_studierichting',
            ],
        ];

        $result = [
            'version' => $version,
            'saved'   => [],
            'errors'  => [],
        ];

        foreach ( $branches as $parent_slug => $config ) {

            $parent = get_term_by(
                'slug',
                $parent_slug,
                'beroep_opleiding'
            );

            if ( ! $parent instanceof WP_Term ) {
                $result['errors'][] =
                    'Parent ontbreekt: ' . $parent_slug;
                continue;
            }

            $children = get_terms(
                [
                    'taxonomy'   => 'beroep_opleiding',
                    'parent'     => (int) $parent->term_id,
                    'hide_empty' => false,
                ]
            );

            if ( is_wp_error( $children ) ) {
                $result['errors'][] =
                    'Kindtermen konden niet worden geladen: ' . $parent_slug;
                continue;
            }

            foreach ( $children as $child ) {

                $target = get_term_by(
                    'slug',
                    $child->slug,
                    $config['target_taxonomy']
                );

                if ( ! $target instanceof WP_Term ) {
                    $result['errors'][] =
                        'Doelterm ontbreekt: '
                        . $config['target_taxonomy']
                        . ' / '
                        . $child->slug;
                    continue;
                }

                $pod = pods(
                    'beroep_opleiding',
                    (int) $child->term_id
                );

                if (
                    ! $pod ||
                    ! $pod->exists()
                ) {
                    $result['errors'][] =
                        'Pods-term kon niet worden geladen: ' . $child->slug;
                    continue;
                }

                $save_result = $pod->save(
                    $config['field'],
                    (int) $target->term_id
                );

                if ( false === $save_result ) {
                    $result['errors'][] =
                        'Relatie kon niet worden opgeslagen: ' . $child->slug;
                    continue;
                }

                $result['saved'][] = [
                    'source_term_id' => (int) $child->term_id,
                    'source_slug'    => $child->slug,
                    'field'          => $config['field'],
                    'target_taxonomy'=> $config['target_taxonomy'],
                    'target_term_id' => (int) $target->term_id,
                    'target_slug'    => $target->slug,
                ];
            }
        }

        update_option(
            'tfsc_beroep_opleiding_rel_sync_result',
            $result,
            false
        );

        update_option(
            'tfsc_beroep_opleiding_rel_sync_version',
            $version,
            false
        );
    },
    100
);


/**
 * Tijdelijk alleen-lezen controle-endpoint.
 * Alleen beheerders kunnen het migratieresultaat uitlezen.
 */
add_action(
    'rest_api_init',
    function() {

        register_rest_route(
            'tfsc-debug/v1',
            '/opleidingen-relaties',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_beroep_opleiding_rel_sync_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
