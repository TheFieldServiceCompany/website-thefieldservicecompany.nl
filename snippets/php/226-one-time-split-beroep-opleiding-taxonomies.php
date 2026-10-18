<?php

/**
 * TFSC - Eenmalig splitsen Beroepengids Opleidingen
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 *
 * De kindtermen uit de tijdelijke taxonomy `beroep_opleiding` worden
 * zonder extra containerlaag gemigreerd naar:
 *
 * - `beroep_opleidingsniveau`
 * - `beroep_studierichting`
 *
 * De naam, slug, beschrijving en Pods-intro worden behouden. Iedere nieuwe
 * term wordt op slug gekoppeld aan de corresponderende vacaturetaxonomy.
 * Daardoor blijven vacatureknoppen en gerelateerde gegevens generiek werken.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if ( get_option( 'tfsc_beroep_opleiding_split_version' ) === $version ) {
            return;
        }

        if (
            ! function_exists( 'pods' ) ||
            ! taxonomy_exists( 'beroep_opleiding' ) ||
            ! taxonomy_exists( 'beroep_opleidingsniveau' ) ||
            ! taxonomy_exists( 'beroep_studierichting' )
        ) {
            return;
        }

        $branches = [
            'opleidingsniveau' => [
                'target_taxonomy'  => 'beroep_opleidingsniveau',
                'vacancy_taxonomy' => 'opleidingsniveau',
                'relationship'     => 'relationship_opleidingsniveau',
            ],
            'studierichting' => [
                'target_taxonomy'  => 'beroep_studierichting',
                'vacancy_taxonomy' => 'vac_studierichting',
                'relationship'     => 'relationship_vac_studierichting',
            ],
        ];

        $result = [
            'version' => $version,
            'saved'   => [],
            'errors'  => [],
        ];

        foreach ( $branches as $parent_slug => $config ) {

            $parent = get_term_by( 'slug', $parent_slug, 'beroep_opleiding' );

            if ( ! $parent instanceof WP_Term ) {
                $result['errors'][] = 'Bronparent ontbreekt: ' . $parent_slug;
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
                $result['errors'][] = 'Brontermen niet geladen: ' . $parent_slug;
                continue;
            }

            foreach ( $children as $source ) {

                $target = get_term_by(
                    'slug',
                    $source->slug,
                    $config['target_taxonomy']
                );

                $term_args = [
                    'name'        => $source->name,
                    'slug'        => $source->slug,
                    'description' => $source->description,
                    'parent'      => 0,
                ];

                if ( $target instanceof WP_Term ) {
                    $saved_term = wp_update_term(
                        (int) $target->term_id,
                        $config['target_taxonomy'],
                        $term_args
                    );
                } else {
                    $saved_term = wp_insert_term(
                        $source->name,
                        $config['target_taxonomy'],
                        $term_args
                    );
                }

                if ( is_wp_error( $saved_term ) ) {
                    $result['errors'][] = sprintf(
                        'Term niet opgeslagen: %s (%s)',
                        $source->slug,
                        $saved_term->get_error_message()
                    );
                    continue;
                }

                $target_term_id = (int) $saved_term['term_id'];
                $source_pod = pods( 'beroep_opleiding', (int) $source->term_id );
                $target_pod = pods( $config['target_taxonomy'], $target_term_id );

                if ( ! $source_pod || ! $source_pod->exists() ) {
                    $result['errors'][] = 'Bron-Pod niet geladen: ' . $source->slug;
                    continue;
                }

                if ( ! $target_pod || ! $target_pod->exists() ) {
                    $result['errors'][] = 'Doel-Pod niet geladen: ' . $source->slug;
                    continue;
                }

                $intro = (string) $source_pod->field( 'intro_taxonomy' );

                if (
                    '' !== trim( $intro ) &&
                    false === $target_pod->save( 'intro_taxonomy', $intro )
                ) {
                    $result['errors'][] = 'Intro niet opgeslagen: ' . $source->slug;
                    continue;
                }

                $vacancy_term = get_term_by(
                    'slug',
                    $source->slug,
                    $config['vacancy_taxonomy']
                );

                if ( ! $vacancy_term instanceof WP_Term ) {
                    $result['errors'][] = sprintf(
                        'Vacatureterm ontbreekt: %s / %s',
                        $config['vacancy_taxonomy'],
                        $source->slug
                    );
                    continue;
                }

                if (
                    false === $target_pod->save(
                        $config['relationship'],
                        [ (int) $vacancy_term->term_id ]
                    )
                ) {
                    $result['errors'][] = 'Relatie niet opgeslagen: ' . $source->slug;
                    continue;
                }

                $result['saved'][] = [
                    'source_term_id'   => (int) $source->term_id,
                    'target_term_id'   => $target_term_id,
                    'slug'             => $source->slug,
                    'target_taxonomy'  => $config['target_taxonomy'],
                    'vacancy_taxonomy' => $config['vacancy_taxonomy'],
                    'vacancy_term_id'  => (int) $vacancy_term->term_id,
                    'relationship'     => $config['relationship'],
                ];
            }
        }

        if ( [] === $result['errors'] ) {
            flush_rewrite_rules( false );
        }

        update_option( 'tfsc_beroep_opleiding_split_result', $result, false );
        update_option( 'tfsc_beroep_opleiding_split_version', $version, false );
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
            '/opleidingen-split',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option( 'tfsc_beroep_opleiding_split_result', [] )
                    );
                },
            ]
        );
    }
);
