<?php

/**
 * rewrite breadcrumbs met taxonomy slug - v3.5
 *
 * In pods kan je rewrite slug instellen en dan pakt deze code het op. 
 */
/**
 * TFSC - Rank Math breadcrumbs
 *
 * Custom breadcrumb-logica voor:
 *
 * 1. Beroepengids CPT archive
 * 2. Beroepengids singular artikelen
 * 3. Beroepengids taxonomy base archives
 * 4. Beroepengids taxonomy term archives
 * 5. Native vacature taxonomy term archives
 *
 * Alle overige pagina's blijven aan Rank Math.
 */

add_filter(
    'rank_math/frontend/breadcrumb/items',
    function( $crumbs, $class ) {

        /**
         * ====================================================
         * 1. BEROEPENGIDS CPT ARCHIVE
         * ====================================================
         *
         * Voorbeeld:
         *
         * /beroepengids/artikelen/
         *
         * Home
         * > Beroepengids
         * > Artikelen
         */

        if ( is_post_type_archive() ) {

            $post_type =
                get_query_var( 'post_type' );

            if ( is_array( $post_type ) ) {
                $post_type =
                    reset( $post_type );
            }

            if ( $post_type ) {

                $post_type_object =
                    get_post_type_object(
                        $post_type
                    );

                if (
                    $post_type_object &&
                    ! empty(
                        $post_type_object->has_archive
                    )
                ) {

                    $archive_url =
                        get_post_type_archive_link(
                            $post_type
                        );

                    if ( $archive_url ) {

                        $archive_path =
                            trim(
                                (string) wp_parse_url(
                                    $archive_url,
                                    PHP_URL_PATH
                                ),
                                '/'
                            );

                        if (
                            0 === strpos(
                                $archive_path,
                                'beroepengids/'
                            )
                        ) {

                            $result = [];

                            if ( ! empty( $crumbs[0] ) ) {
                                $result[] = $crumbs[0];
                            }

                            $result[] = [
                                'Beroepengids',
                                home_url(
                                    '/beroepengids/'
                                ),
                            ];

                            $result[] = [
                                $post_type_object
                                    ->labels
                                    ->name,
                                '',
                            ];

                            return $result;
                        }
                    }
                }
            }
        }


        /**
         * ====================================================
         * 2. BEROEPENGIDS SINGULAR ARTIKELEN
         * ====================================================
         *
         * Voorbeeld:
         *
         * /beroepengids/artikel/servicemonteur/
         *
         * Home
         * > Beroepengids
         * > Artikelen
         * > Servicemonteur
         */

        if ( is_singular() ) {

            $post_type =
                get_post_type();

            if ( $post_type ) {

                $post_type_object =
                    get_post_type_object(
                        $post_type
                    );

                if (
                    $post_type_object &&
                    ! empty(
                        $post_type_object->has_archive
                    )
                ) {

                    $archive_url =
                        get_post_type_archive_link(
                            $post_type
                        );

                    if ( $archive_url ) {

                        $archive_path =
                            trim(
                                (string) wp_parse_url(
                                    $archive_url,
                                    PHP_URL_PATH
                                ),
                                '/'
                            );

                        /**
                         * Alleen CPT's waarvan het archief
                         * onder /beroepengids/ staat.
                         */
                        if (
                            0 === strpos(
                                $archive_path,
                                'beroepengids/'
                            )
                        ) {

                            $result = [];

                            if ( ! empty( $crumbs[0] ) ) {
                                $result[] = $crumbs[0];
                            }

                            /**
                             * Beroepengids pagina.
                             */
                            $result[] = [
                                'Beroepengids',
                                home_url(
                                    '/beroepengids/'
                                ),
                            ];

                            /**
                             * CPT archief.
                             */
                            $result[] = [
                                $post_type_object
                                    ->labels
                                    ->name,
                                $archive_url,
                            ];

                            /**
                             * Huidig artikel.
                             */
                            $result[] = [
                                get_the_title(),
                                '',
                            ];

                            return $result;
                        }
                    }
                }
            }
        }


        /**
         * ====================================================
         * 3. BEROEPENGIDS TAXONOMY BASE ARCHIVES
         * ====================================================
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
                                (string) $taxonomy
                                    ->rewrite['slug'],
                                '/'
                            )
                        )
                    )
                );

            if ( ! $segments ) {
                return $crumbs;
            }

            $result = [];

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
                            $taxonomy
                                ->labels
                                ->name
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
         * 4. BEROEPENGIDS TAXONOMY TERM ARCHIVES
         * ====================================================
         *
         * Rank Math breadcrumb behouden.
         *
         * Alleen taxonomy-base item klikbaar maken.
         */

        if ( is_tax() ) {

            $term =
                get_queried_object();

            if ( $term instanceof WP_Term ) {

                $taxonomy_name =
                    sanitize_key(
                        $term->taxonomy
                    );

                $is_beroepengids_taxonomy =
                    0 === strpos(
                        $taxonomy_name,
                        'beroep_'
                    )
                    ||
                    0 === strpos(
                        $taxonomy_name,
                        'beroepengids_'
                    );

                if ( $is_beroepengids_taxonomy ) {

                    $taxonomy =
                        get_taxonomy(
                            $taxonomy_name
                        );

                    if (
                        $taxonomy &&
                        ! empty(
                            $taxonomy->rewrite['slug']
                        )
                    ) {

                        $base_url =
                            home_url(
                                '/'
                                . trim(
                                    $taxonomy
                                        ->rewrite['slug'],
                                    '/'
                                )
                                . '/'
                            );

                        if ( count( $crumbs ) >= 2 ) {

                            $base_index =
                                count( $crumbs ) - 2;

                            $crumbs[
                                $base_index
                            ][1] =
                                $base_url;
                        }
                    }

                    return $crumbs;
                }
            }
        }


        /**
         * ====================================================
         * 5. NATIVE VACATURE TAXONOMY TERM ARCHIVES
         * ====================================================
         *
         * Voorbeeld:
         *
         * Home
         * > Vacatures
         * > Nederland
         * > Provincie Noord-Brabant
         * > Eindhoven
         */

        if ( is_tax() ) {

            $term =
                get_queried_object();

            if (
                ! $term instanceof WP_Term
            ) {
                return $crumbs;
            }

            $vacancy_post_type =
                defined(
                    'TFSC_VACANCY_POST_TYPE'
                )
                    ? TFSC_VACANCY_POST_TYPE
                    : 'vacature';

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
             * Alleen echte native taxonomy URL.
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
                    $_SERVER[
                        'REQUEST_URI'
                    ] ?? '',
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

            $combo_base =
                defined(
                    'TFSC_COMBO_BASE'
                )
                    ? trim(
                        TFSC_COMBO_BASE,
                        '/'
                    )
                    : 'vacatures';

            $taxonomy_slug =
                ! empty(
                    $taxonomy->rewrite['slug']
                )
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

            $term_path = [];

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
         */

        return $crumbs;
    },
    10,
    2
);
