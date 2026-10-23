<?php

/**
 * Single Vacs | Taxonomy Terms Loop
 */
/**
 * TFSC - Vacancy Taxonomy Terms Loop
 * Version 1.1 - Base + Exact Level Query IDs
 *
 * ============================================================
 * DOEL
 * ============================================================
 *
 * Op een single vacature:
 *
 * huidige vacature
 * → Elementor Taxonomy Loop
 * → Elementor Source bepaalt taxonomy
 * → alleen terms tonen die aan deze vacature gekoppeld zijn
 * → optioneel exact taxonomy-level isoleren
 * → links wijzen naar TFSC vacature-archive URLs
 *
 *
 * ============================================================
 * ELEMENTOR QUERY IDS
 * ============================================================
 *
 * vacature_terms
 * → alle direct aan de vacature gekoppelde terms
 *
 * vacature_terms_level_0
 * → alleen terms met exact 0 ancestors
 *
 * vacature_terms_level_1
 * → alleen terms met exact 1 ancestor
 *
 * vacature_terms_level_2
 * → alleen terms met exact 2 ancestors
 *
 *
 * Elementor Source bepaalt volledig de taxonomy.
 *
 *
 * ============================================================
 * VOORBEELD LOCATIE
 * ============================================================
 *
 * Nederland
 * → level 0
 *
 * Zuid-Holland
 * → level 1
 *
 * Alphen aan den Rijn
 * → level 2
 *
 *
 * vacature_terms
 * → alle gekoppelde locatie-terms
 *
 * vacature_terms_level_2
 * → alleen Alphen aan den Rijn
 *
 *
 * ============================================================
 * URL STRUCTUUR
 * ============================================================
 *
 * Native:
 *
 * /locatie/nederland/zuid-holland/alphen-aan-den-rijn/
 *
 * Wordt:
 *
 * /vacatures/locatie/nederland/zuid-holland/alphen-aan-den-rijn/
 *
 *
 * Vlakke taxonomy:
 *
 * /vacatures/kennisgebied/elektrotechniek/
 *
 *
 * ============================================================
 * BELANGRIJK
 * ============================================================
 *
 * - Verandert NIET globaal alle term-links.
 * - Alleen terms uit deze specifieke Elementor-query worden
 *   aangepast.
 * - Elementor Source blijft leidend.
 * - Elementor eigen widgetinstellingen blijven zoveel mogelijk
 *   intact.
 */


/**
 * ============================================================
 * 1. CONFIG
 * ============================================================
 */

function tfsc_vacancy_terms_v1_post_type() {

    return defined( 'TFSC_VACANCY_POST_TYPE' )
        ? TFSC_VACANCY_POST_TYPE
        : 'vacature';
}


function tfsc_vacancy_terms_v1_combo_base() {

    return defined( 'TFSC_COMBO_BASE' )
        ? TFSC_COMBO_BASE
        : 'vacatures';
}


/**
 * ============================================================
 * 2. HUIDIGE VACATURE
 * ============================================================
 *
 * Werkt op:
 *
 * - normale single vacature
 * - Elementor Theme Builder preview
 * - preview_id fallback
 */

function tfsc_vacancy_terms_v1_get_post_id() {

    static $resolved = null;


    if ( null !== $resolved ) {
        return $resolved;
    }


    $resolved = 0;

    $object =
        get_queried_object();


    /**
     * Normale vacature frontend.
     */
    if (
        $object instanceof WP_Post &&
        tfsc_vacancy_terms_v1_post_type()
            === get_post_type( $object->ID )
    ) {

        return $resolved =
            (int) $object->ID;
    }


    /**
     * Elementor Theme Builder preview.
     */
    if (
        $object instanceof WP_Post &&
        'elementor_library'
            === get_post_type( $object->ID )
    ) {

        $template_id =
            (int) $object->ID;


        if (
            did_action( 'elementor/loaded' ) &&
            class_exists( '\Elementor\Plugin' )
        ) {

            try {

                $document =
                    \Elementor\Plugin::$instance
                        ->documents
                        ->get( $template_id );


                if ( $document ) {

                    $preview_id =
                        absint(
                            $document->get_settings(
                                'preview_id'
                            )
                        );


                    if (
                        $preview_id &&
                        tfsc_vacancy_terms_v1_post_type()
                            === get_post_type( $preview_id )
                    ) {

                        return $resolved =
                            $preview_id;
                    }
                }

            } catch ( Throwable $e ) {
                // Meta fallback hieronder.
            }
        }


        $settings =
            get_post_meta(
                $template_id,
                '_elementor_page_settings',
                true
            );


        if (
            is_array( $settings ) &&
            ! empty( $settings['preview_id'] )
        ) {

            $preview_id =
                absint(
                    $settings['preview_id']
                );


            if (
                $preview_id &&
                tfsc_vacancy_terms_v1_post_type()
                    === get_post_type( $preview_id )
            ) {

                return $resolved =
                    $preview_id;
            }
        }
    }


    /**
     * WordPress / Elementor preview fallback.
     */
    if (
        ! empty( $_GET['preview_id'] )
    ) {

        $preview_id =
            absint(
                wp_unslash(
                    $_GET['preview_id']
                )
            );


        if (
            $preview_id &&
            tfsc_vacancy_terms_v1_post_type()
                === get_post_type( $preview_id )
        ) {

            return $resolved =
                $preview_id;
        }
    }


    /**
     * Laatste fallback.
     */
    global $post;


    if (
        $post instanceof WP_Post &&
        tfsc_vacancy_terms_v1_post_type()
            === get_post_type( $post->ID )
    ) {

        return $resolved =
            (int) $post->ID;
    }


    return 0;
}


/**
 * ============================================================
 * 3. QUERY ID PARSER
 * ============================================================
 *
 * vacature_terms
 *
 * vacature_terms_level_0
 * vacature_terms_level_1
 * vacature_terms_level_2
 */

function tfsc_vacancy_terms_v1_parse_query_id(
    $query_id
) {

    $query_id =
        (string) $query_id;


    /**
     * Base query.
     */
    if (
        'vacature_terms'
        === $query_id
    ) {

        return [
            'active' =>
                true,

            'level' =>
                null,
        ];
    }


    /**
     * Exact level query.
     */
    if (
        preg_match(
            '/^vacature_terms_level_([0-9]+)$/',
            $query_id,
            $matches
        )
    ) {

        return [
            'active' =>
                true,

            'level' =>
                (int) $matches[1],
        ];
    }


    return false;
}


/**
 * ============================================================
 * 4. EXACT LEVEL FILTER
 * ============================================================
 *
 * Niet-hiërarchische taxonomies hebben alleen level 0.
 */

function tfsc_vacancy_terms_v1_filter_level(
    $term_ids,
    $taxonomy,
    $level
) {

    if (
        null === $level
    ) {

        return array_values(
            array_unique(
                array_map(
                    'intval',
                    $term_ids
                )
            )
        );
    }


    if (
        ! $term_ids
    ) {

        return [];
    }


    /**
     * Vlakke taxonomy:
     *
     * level 0 = alles
     * hoger = niets
     */
    if (
        ! is_taxonomy_hierarchical(
            $taxonomy
        )
    ) {

        return (
            0 === (int) $level
        )
            ? array_values(
                array_unique(
                    array_map(
                        'intval',
                        $term_ids
                    )
                )
            )
            : [];
    }


    return array_values(
        array_filter(
            array_unique(
                array_map(
                    'intval',
                    $term_ids
                )
            ),
            function(
                $term_id
            ) use (
                $taxonomy,
                $level
            ) {

                return (
                    count(
                        get_ancestors(
                            $term_id,
                            $taxonomy,
                            'taxonomy'
                        )
                    )
                    ===
                    (int) $level
                );
            }
        )
    );
}


/**
 * ============================================================
 * 5. ELEMENTOR TAXONOMY LOOP
 * ============================================================
 *
 * Elementor Source bepaalt taxonomy.
 */

add_filter(
    'elementor/loop_taxonomy/args',
    function(
        $args,
        $settings,
        $display_settings
    ) {

        $query_id =
            $settings['term_taxonomy_id']
            ??
            $settings[
                'post_taxonomy_term_taxonomy_id'
            ]
            ??
            '';


        $parsed =
            tfsc_vacancy_terms_v1_parse_query_id(
                $query_id
            );


        if ( ! $parsed ) {
            return $args;
        }


        $post_id =
            tfsc_vacancy_terms_v1_get_post_id();


        if ( ! $post_id ) {

            $args['include'] =
                [ 0 ];

            return $args;
        }


        /**
         * Elementor Source bepaalt taxonomy.
         */
        $taxonomy =
            $args['taxonomy']
            ??
            $settings[
                'post_taxonomy_query_post_type'
            ]
            ??
            '';


        if (
            is_array( $taxonomy )
        ) {

            $taxonomy =
                reset(
                    $taxonomy
                );
        }


        $taxonomy =
            sanitize_key(
                $taxonomy
            );


        if (
            ! $taxonomy ||
            ! taxonomy_exists( $taxonomy )
        ) {

            $args['include'] =
                [ 0 ];

            return $args;
        }


        /**
         * Alleen terms die direct aan deze vacature hangen.
         */
        $term_ids =
            wp_get_object_terms(
                $post_id,
                $taxonomy,
                [
                    'fields' =>
                        'ids',
                ]
            );


        if (
            is_wp_error( $term_ids ) ||
            ! $term_ids
        ) {

            $args['include'] =
                [ 0 ];

            return $args;
        }


        $term_ids =
            array_values(
                array_unique(
                    array_filter(
                        array_map(
                            'intval',
                            $term_ids
                        )
                    )
                )
            );


        /**
         * Exact level indien Query ID dit vraagt.
         */
        $term_ids =
            tfsc_vacancy_terms_v1_filter_level(
                $term_ids,
                $taxonomy,
                $parsed['level']
            );


        if ( ! $term_ids ) {

            $args['include'] =
                [ 0 ];

            return $args;
        }


        /**
         * Alleen geselecteerde vacatureterms tonen.
         */
        unset(
            $args['term_taxonomy_id']
        );


        $args['taxonomy'] =
            $taxonomy;

        $args['include'] =
            $term_ids;

        $args['hide_empty'] =
            false;


        /**
         * Parent/child groepering door WP uitzetten.
         *
         * Exact level wordt hierboven al bepaald.
         */
        $args['hierarchical'] =
            false;


        /**
         * Marker voor sortering.
         */
        $args[
            'tfsc_vacancy_terms_v1'
        ] = 1;


        /**
         * Alleen exacte terms uit deze loop markeren
         * voor aangepaste term-links.
         */
        foreach (
            $term_ids
            as $term_id
        ) {

            $GLOBALS[
                'tfsc_vacancy_terms_v1_active_terms'
            ][ $taxonomy ][ $term_id ] =
                true;
        }


        return $args;
    },
    35,
    3
);


/**
 * ============================================================
 * 6. SORTERING
 * ============================================================
 *
 * Alleen deze loop.
 */

add_filter(
    'get_terms',
    function(
        $terms,
        $taxonomies,
        $args,
        $term_query
    ) {

        if (
            empty(
                $args[
                    'tfsc_vacancy_terms_v1'
                ]
            ) ||
            ! is_array( $terms ) ||
            ! $terms
        ) {

            return $terms;
        }


        if (
            isset( $args['orderby'] ) &&
            'name'
                !== strtolower(
                    (string) $args['orderby']
                )
        ) {

            return $terms;
        }


        $order =
            strtoupper(
                (string) (
                    $args['order']
                    ?? 'ASC'
                )
            );


        if (
            ! in_array(
                $order,
                [
                    'ASC',
                    'DESC',
                ],
                true
            )
        ) {

            $order =
                'ASC';
        }


        usort(
            $terms,
            function(
                $a,
                $b
            ) use (
                $order
            ) {

                if (
                    ! $a instanceof WP_Term ||
                    ! $b instanceof WP_Term
                ) {

                    return 0;
                }


                $compare =
                    strnatcasecmp(
                        remove_accents(
                            wp_strip_all_tags(
                                $a->name
                            )
                        ),
                        remove_accents(
                            wp_strip_all_tags(
                                $b->name
                            )
                        )
                    );


                return (
                    'DESC' === $order
                )
                    ? -$compare
                    : $compare;
            }
        );


        return $terms;
    },
    9999,
    4
);


/**
 * ============================================================
 * 7. TERM PATH
 * ============================================================
 */

function tfsc_vacancy_terms_v1_term_path(
    $term
) {

    if (
        ! $term instanceof WP_Term ||
        ! taxonomy_exists(
            $term->taxonomy
        )
    ) {

        return '';
    }


    /**
     * Bestaande bewezen helper gebruiken indien beschikbaar.
     */
    if (
        function_exists(
            'tfsc_taxrel_v4_term_path'
        )
    ) {

        return tfsc_taxrel_v4_term_path(
            $term
        );
    }


    if (
        function_exists(
            'tfsc_derived_rel_v3_term_path'
        )
    ) {

        return tfsc_derived_rel_v3_term_path(
            $term
        );
    }


    $slugs =
        [];


    if (
        is_taxonomy_hierarchical(
            $term->taxonomy
        )
    ) {

        $ancestor_ids =
            array_reverse(
                get_ancestors(
                    $term->term_id,
                    $term->taxonomy,
                    'taxonomy'
                )
            );


        foreach (
            $ancestor_ids
            as $ancestor_id
        ) {

            $ancestor =
                get_term(
                    $ancestor_id,
                    $term->taxonomy
                );


            if (
                $ancestor instanceof WP_Term &&
                ! is_wp_error(
                    $ancestor
                )
            ) {

                $slugs[] =
                    $ancestor->slug;
            }
        }
    }


    $slugs[] =
        $term->slug;


    return implode(
        '/',
        array_map(
            'rawurlencode',
            $slugs
        )
    );
}


/**
 * ============================================================
 * 8. VACATURE ARCHIVE URL
 * ============================================================
 */

function tfsc_vacancy_terms_v1_archive_url(
    $term
) {

    if (
        ! $term instanceof WP_Term
    ) {

        return '';
    }


    $path =
        tfsc_vacancy_terms_v1_term_path(
            $term
        );


    if ( ! $path ) {
        return '';
    }


    return home_url(
        '/'
        .
        trim(
            tfsc_vacancy_terms_v1_combo_base(),
            '/'
        )
        .
        '/'
        .
        trim(
            $term->taxonomy,
            '/'
        )
        .
        '/'
        .
        trim(
            $path,
            '/'
        )
        .
        '/'
    );
}


/**
 * ============================================================
 * 9. TERM LINKS → VACATURE ARCHIVES
 * ============================================================
 *
 * Alleen exact gemarkeerde terms uit deze loop.
 */

add_filter(
    'term_link',
    function(
        $termlink,
        $term,
        $taxonomy
    ) {

        /**
         * Andere TFSC-snippets moeten native links
         * kunnen afdwingen.
         */
        if (
            ! empty(
                $GLOBALS[
                    'tfsc_force_native_term_link'
                ]
            ) ||
            is_admin() ||
            ! $term instanceof WP_Term ||
            ! $taxonomy
        ) {

            return $termlink;
        }


        /**
         * Alleen vacature-context.
         */
        if (
            ! tfsc_vacancy_terms_v1_get_post_id()
        ) {

            return $termlink;
        }


        /**
         * Alleen exacte terms uit vacature_terms-loop.
         */
        if (
            empty(
                $GLOBALS[
                    'tfsc_vacancy_terms_v1_active_terms'
                ][ $taxonomy ][ (int) $term->term_id ]
            )
        ) {

            return $termlink;
        }


        $url =
            tfsc_vacancy_terms_v1_archive_url(
                $term
            );


        return $url
            ?: $termlink;
    },
    35,
    3
);
