<?php

/**
 * rewrite breadcrumbs met taxonomy slug - v3.2 - Optimized [CLONE]
 *
 * In pods kan je rewrite slug instellen en dan pakt deze code het op. 
 */
/**
 * TFSC - Rank Math breadcrumbs
 *
 * Custom breadcrumb-logica uitsluitend voor:
 *
 * 1. Beroepengids taxonomy base archives
 * 2. Native vacature taxonomy term archives
 *
 * Alle andere taxonomies, CPT archives en singulars
 * blijven volledig aan Rank Math overgelaten.
 */

add_filter(
    'rank_math/frontend/breadcrumb/items',
    function( $crumbs, $class ) {

        /**
         * ====================================================
         * 1. BEROEPENGIDS TAXONOMY BASE ARCHIVES
         * ====================================================
         *
         * Dit is bewust een uitzondering.
         *
         * Alleen taxonomy base archives van de beroepengids
         * krijgen hier een zelf opgebouwde breadcrumb.
         *
         * Bijvoorbeeld:
         * beroep_functienaam
         */

        $base_taxonomy =
            get_query_var(
                'tfsc_taxonomy_archive'
            );

        if (
            $base_taxonomy &&
            taxonomy_exists( $base_taxonomy )
        ) {

            $base_taxonomy =
                sanitize_key(
                    $base_taxonomy
                );

            /**
             * Alleen beroepengids taxonomies.
             *
             * Alle andere taxonomy base archives
             * blijven ongemoeid.
             */
            $is_beroepengids_taxonomy =
                0 === strpos(
                    $base_taxonomy,
                    'beroep_'
                )
                ||
                0 === strpos(
                    $base_taxonomy,
                    'beroepengids_'
                );

            if ( ! $is_beroepengids_taxonomy ) {
                return $crumbs;
            }

            $taxonomy =
                get_taxonomy(
                    $base_taxonomy
                );

            if (
                ! $taxonomy ||
                empty( $taxonomy->rewrite['slug'] )
            ) {
                return $crumbs;
            }

            $segments =
                array_values(
                    array_filter(
                        explode(
                            '/',
                            trim(
                                (string) $taxonomy->rewrite['slug'],
                                '/'
                            )
                        )
                    )
                );

            if ( ! $segments ) {
                return $crumbs;
            }

            $result = [];

            /**
             * Home.
             */
            if ( ! empty( $crumbs[0] ) ) {
                $result[] = $crumbs[0];
            }

            $current = '';
            $last =
                count( $segments ) - 1;

            foreach (
                $segments
                as $index => $segment
            ) {

                $current .=
                    '/' . $segment;

                $label =
                    $index === $last
                        ? (
                            $taxonomy->labels->name
                            ?? ucfirst(
                                str_replace(
                                    [ '-', '_' ],
                                    ' ',
                                    $segment
                                )
                            )
                        )
                        : ucfirst(
                            str_replace(
                                [ '-', '_' ],
                                ' ',
                                $segment
                            )
                        );

                $result[] = [
                    $label,
                    $index === $last
                        ? ''
                        : home_url(
                            $current . '/'
                        ),
                ];
            }

            return $result;
        }


        /**
         * ====================================================
         * 2. NATIVE VACATURE TAXONOMY TERM ARCHIVES
         * ====================================================
         *
         * Alleen taxonomies die daadwerkelijk aan het
         * vacature CPT gekoppeld zijn.
         *
         * Voorbeeld:
         *
         * /locatie/nederland/noord-brabant/eindhoven/
         *
         * wordt:
         *
         * Home
         * > Vacatures
         * > Nederland
         * > Provincie Noord-Brabant
         * > Eindhoven
         *
         * Dus GEEN taxonomy base "Locaties".
         *
         * Andere CPT-taxonomies blijven volledig ongemoeid.
         */

        if ( is_tax() ) {

            $term =
                get_queried_object();

            if (
                ! $term instanceof WP_Term
            ) {
                return $crumbs;
            }

            /**
             * Vacature post type.
             */
            $vacancy_post_type =
                defined( 'TFSC_VACANCY_POST_TYPE' )
                    ? TFSC_VACANCY_POST_TYPE
                    : 'vacature';

            /**
             * Alleen taxonomies die daadwerkelijk
             * aan het vacature CPT gekoppeld zijn.
             */
            $vacancy_taxonomies =
                get_object_taxonomies(
                    $vacancy_post_type,
                    'names'
                );

            if (
                ! in_array(
                    $term->taxonomy,
                    $vacancy_taxonomies,
                    true
                )
            ) {
                return $crumbs;
            }

            /**
             * Alleen de echte native taxonomy URL.
             *
             * Combo archives onder /vacatures/...
             * blijven ongemoeid.
             */
            $term_url =
                get_term_link(
                    $term
                );

            if ( is_wp_error( $term_url ) ) {
                return $crumbs;
            }

            $native_path =
                untrailingslashit(
                    wp_parse_url(
                        $term_url,
                        PHP_URL_PATH
                    )
                );

            $current_path =
                wp_parse_url(
                    $_SERVER['REQUEST_URI'] ?? '',
                    PHP_URL_PATH
                );

            $current_path =
                preg_replace(
                    '#/page/[0-9]+/?$#',
                    '/',
                    $current_path
                );

            $current_path =
                untrailingslashit(
                    $current_path
                );

            if (
                $native_path !==
                $current_path
            ) {
                return $crumbs;
            }

            $taxonomy =
                get_taxonomy(
                    $term->taxonomy
                );

            if ( ! $taxonomy ) {
                return $crumbs;
            }

            /**
             * Combo base.
             */
            $combo_base =
                defined( 'TFSC_COMBO_BASE' )
                    ? trim(
                        TFSC_COMBO_BASE,
                        '/'
                    )
                    : 'vacatures';

            /**
             * Taxonomy rewrite slug.
             */
            $taxonomy_slug =
                ! empty( $taxonomy->rewrite['slug'] )
                    ? trim(
                        $taxonomy->rewrite['slug'],
                        '/'
                    )
                    : $term->taxonomy;

            $result = [];

            /**
             * Home.
             */
            if ( ! empty( $crumbs[0] ) ) {
                $result[] = $crumbs[0];
            }

            /**
             * Vacatures.
             */
            $result[] = [
                'Vacatures',
                home_url(
                    '/' . $combo_base . '/'
                ),
            ];

            /**
             * Geen taxonomy-base breadcrumb.
             *
             * Dus bijvoorbeeld GEEN:
             *
             * Locaties
             * Functienamen
             * Bedrijfssectoren
             */

            $term_path = [];

            /**
             * Hiërarchische ancestors.
             */
            if (
                is_taxonomy_hierarchical(
                    $term->taxonomy
                )
            ) {

                foreach (
                    array_reverse(
                        get_ancestors(
                            $term->term_id,
                            $term->taxonomy,
                            'taxonomy'
                        )
                    )
                    as $ancestor_id
                ) {

                    $ancestor =
                        get_term(
                            $ancestor_id,
                            $term->taxonomy
                        );

                    if (
                        ! $ancestor instanceof WP_Term ||
                        is_wp_error( $ancestor )
                    ) {
                        continue;
                    }

                    $term_path[] =
                        $ancestor->slug;

                    $result[] = [
                        $ancestor->name,
                        home_url(
                            '/'
                            . $combo_base
                            . '/'
                            . $taxonomy_slug
                            . '/'
                            . implode(
                                '/',
                                $term_path
                            )
                            . '/'
                        ),
                    ];
                }
            }

            /**
             * Huidige term.
             */
            $result[] = [
                $term->name,
                '',
            ];

            return $result;
        }


        /**
         * ====================================================
         * ALLES ANDERS
         * ====================================================
         *
         * Geen custom TFSC breadcrumb-logica.
         *
         * Rank Math bepaalt de breadcrumb.
         */

        return $crumbs;
    },
    10,
    2
);
