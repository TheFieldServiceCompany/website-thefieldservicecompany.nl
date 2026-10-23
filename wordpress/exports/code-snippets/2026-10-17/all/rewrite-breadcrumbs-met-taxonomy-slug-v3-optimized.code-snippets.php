<?php

/**
 * rewrite breadcrumbs met taxonomy slug - v3 - Optimized
 *
 * In pods kan je rewrite slug instellen en dan pakt deze code het op. 
 */
/**
 * TFSC - Rank Math breadcrumbs voor:
 * - taxonomy base archives
 * - native taxonomy term archives
 * - CPT archives
 * - CPT singulars
 */

add_filter(
    'rank_math/frontend/breadcrumb/items',
    function( $crumbs, $class ) {

        /**
         * Home breadcrumb behouden en een pad opbouwen.
         */
        $build_path = function(
            $path,
            $last_label = '',
            $last_current = false
        ) use ( $crumbs ) {

            $segments = array_values(
                array_filter(
                    explode(
                        '/',
                        trim( (string) $path, '/' )
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
            $last    = count( $segments ) - 1;

            foreach ( $segments as $index => $segment ) {

                $current .= '/' . $segment;

                $label = (
                    $index === $last &&
                    '' !== $last_label
                )
                    ? $last_label
                    : ucfirst(
                        str_replace(
                            [ '-', '_' ],
                            ' ',
                            $segment
                        )
                    );

                $result[] = [
                    $label,
                    (
                        $last_current &&
                        $index === $last
                    )
                        ? ''
                        : home_url( $current . '/' ),
                ];
            }

            return $result;
        };


        /**
         * URL → relatief pad.
         */
        $relative_path = function( $url ) {

            $path = $url
                ? wp_parse_url(
                    $url,
                    PHP_URL_PATH
                )
                : '';

            return $path
                ? trim( $path, '/' )
                : '';
        };


        /**
         * ====================================================
         * 1. TAXONOMY BASE ARCHIVE
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

            return $build_path(
                $taxonomy->rewrite['slug'],
                $taxonomy->labels->name ?? '',
                true
            );
        }


        /**
         * ====================================================
         * 2. NATIVE TAXONOMY TERM ARCHIVE
         * ====================================================
         *
         * Native taxonomy URL:
         * /locatie/nederland/noord-brabant/eindhoven/
         *
         * Breadcrumb:
         * Home
         * > Vacatures
         * > Locaties
         * > Nederland
         * > Noord-Brabant
         * > Eindhoven
         *
         * Alle breadcrumb-links wijzen naar de combo-structuur:
         * /vacatures/{taxonomy}/...
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
             * Alleen echte native term URL behandelen.
             * Combo/virtuele URLs blijven ongemoeid.
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
             *
             * Bijvoorbeeld:
             * /vacatures/locatie/
             */
            $combo_base =
                defined( 'TFSC_COMBO_BASE' )
                    ? trim(
                        TFSC_COMBO_BASE,
                        '/'
                    )
                    : 'vacatures';

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
             * Taxonomy base.
             *
             * Locaties
             * → /vacatures/locatie/
             */
            $result[] = [
                $taxonomy->labels->name
                    ?? ucfirst(
                        str_replace(
                            [ '-', '_' ],
                            ' ',
                            $taxonomy_slug
                        )
                    ),

                home_url(
                    '/'
                    . $combo_base
                    . '/'
                    . $taxonomy_slug
                    . '/'
                ),
            ];

            /**
             * Pad binnen de taxonomy opbouwen.
             */
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
             *
             * Geen link omdat dit de huidige native pagina is.
             */
            $result[] = [
                $term->name,
                '',
            ];

            return $result;
        }


        /**
         * ====================================================
         * 3. CPT ARCHIVE
         * ====================================================
         */

        if ( is_post_type_archive() ) {

            $post_type =
                get_query_var(
                    'post_type'
                );

            if ( is_array( $post_type ) ) {
                $post_type =
                    reset( $post_type );
            }

            if ( ! $post_type ) {
                return $crumbs;
            }

            $object =
                get_post_type_object(
                    $post_type
                );

            $archive_url =
                get_post_type_archive_link(
                    $post_type
                );

            $archive_path =
                $relative_path(
                    $archive_url
                );

            if (
                ! $object ||
                ! $archive_path
            ) {
                return $crumbs;
            }

            return $build_path(
                $archive_path,
                $object->labels->name ?? '',
                true
            );
        }


        /**
         * ====================================================
         * 4. CPT SINGULAR
         * ====================================================
         */

        if ( is_singular() ) {

            $post_id =
                get_queried_object_id();

            if ( ! $post_id ) {
                return $crumbs;
            }

            $post_type =
                get_post_type(
                    $post_id
                );

            if (
                ! $post_type ||
                in_array(
                    $post_type,
                    [
                        'post',
                        'page',
                        'attachment',
                    ],
                    true
                )
            ) {
                return $crumbs;
            }

            $object =
                get_post_type_object(
                    $post_type
                );

            if (
                ! $object ||
                empty( $object->has_archive )
            ) {
                return $crumbs;
            }

            $archive_path =
                $relative_path(
                    get_post_type_archive_link(
                        $post_type
                    )
                );

            if ( ! $archive_path ) {
                return $crumbs;
            }

            $result =
                $build_path(
                    $archive_path,
                    $object->labels->name ?? ''
                );

            if (
                is_post_type_hierarchical(
                    $post_type
                )
            ) {

                foreach (
                    array_reverse(
                        get_post_ancestors(
                            $post_id
                        )
                    )
                    as $ancestor_id
                ) {

                    $result[] = [
                        get_the_title(
                            $ancestor_id
                        ),
                        get_permalink(
                            $ancestor_id
                        ),
                    ];
                }
            }

            $result[] = [
                get_the_title(
                    $post_id
                ),
                '',
            ];

            return $result;
        }


        return $crumbs;
    },
    10,
    2
);
