<?php

/**
 * TFSC - Eenmalig vullen van intro_taxonomy voor Opleidingen
 *
 * LIVE-uitvoering: 18 oktober 2026
 * Bedoeling: één keer actief uitvoeren en daarna direct deactiveren.
 */

add_action(
    'init',
    function() {

        $version = '2026-10-18-v1';

        if (
            get_option( 'tfsc_beroep_opleiding_intro_version' ) === $version
        ) {
            return;
        }

        if (
            ! function_exists( 'pods' ) ||
            ! taxonomy_exists( 'beroep_opleiding' )
        ) {
            return;
        }

        $parents = [
            'opleidingsniveau' => [
                'intro' => 'Vergelijk opleidingsniveaus binnen field service en ontdek welke functies, praktijkervaring en ontwikkelmogelijkheden aansluiten op jouw achtergrond.',
                'child_intro' => static function( $name ) {
                    return sprintf(
                        'Ontdek welke fieldservicefuncties aansluiten op %s, welke praktijkkennis werkgevers vragen en hoe je vanuit dit opleidingsniveau kunt doorgroeien.',
                        $name
                    );
                },
            ],
            'studierichting' => [
                'intro' => 'Ontdek welke technische studierichtingen aansluiten op field service en vergelijk de kennis, functies en werkomgevingen die bij iedere richting passen.',
                'child_intro' => static function( $name ) {
                    return sprintf(
                        'Ontdek hoe %s aansluit op field service, welke kennis je in het werk toepast en welke functies en technische omgevingen bij deze studierichting passen.',
                        $name
                    );
                },
            ],
        ];

        $result = [
            'version' => $version,
            'saved'   => [],
            'errors'  => [],
        ];

        foreach ( $parents as $slug => $config ) {

            $parent = get_term_by(
                'slug',
                $slug,
                'beroep_opleiding'
            );

            if ( ! $parent instanceof WP_Term ) {
                $result['errors'][] = 'Parent ontbreekt: ' . $slug;
                continue;
            }

            $parent_pod = pods(
                'beroep_opleiding',
                (int) $parent->term_id
            );

            if (
                ! $parent_pod ||
                false === $parent_pod->save(
                    'intro_taxonomy',
                    $config['intro']
                )
            ) {
                $result['errors'][] = 'Parentintro niet opgeslagen: ' . $slug;
            } else {
                $result['saved'][] = $slug;
            }

            $children = get_terms(
                [
                    'taxonomy'   => 'beroep_opleiding',
                    'parent'     => (int) $parent->term_id,
                    'hide_empty' => false,
                ]
            );

            if ( is_wp_error( $children ) ) {
                $result['errors'][] = 'Kindtermen niet geladen: ' . $slug;
                continue;
            }

            foreach ( $children as $child ) {

                $pod = pods(
                    'beroep_opleiding',
                    (int) $child->term_id
                );

                $intro = $config['child_intro']( $child->name );

                if (
                    ! $pod ||
                    false === $pod->save( 'intro_taxonomy', $intro )
                ) {
                    $result['errors'][] =
                        'Kindintro niet opgeslagen: ' . $child->slug;
                    continue;
                }

                $result['saved'][] = $child->slug;
            }
        }

        update_option(
            'tfsc_beroep_opleiding_intro_result',
            $result,
            false
        );

        update_option(
            'tfsc_beroep_opleiding_intro_version',
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
            '/opleidingen-intros',
            [
                'methods'             => 'GET',
                'permission_callback' => function() {
                    return current_user_can( 'manage_options' );
                },
                'callback'            => function() {
                    return rest_ensure_response(
                        get_option(
                            'tfsc_beroep_opleiding_intro_result',
                            []
                        )
                    );
                },
            ]
        );
    }
);
