<?php

/**
 * CPT → Relationship Taxonomies Pods  → link to Combo Archives [v5.1]  WORKING - don't remove
 *
 * Voor op de pagina van beroepen, de gekoppelde functienamen en locaties. 
 */
/**
 * TFSC - Generic Derived Relationship Taxonomy Terms
 * Version 3.1 - Match Logic Synced With Combo Base
 *
 * ============================================================
 * DOEL
 * ============================================================
 *
 * Toont in een Elementor Taxonomy Loop de taxonomy-terms die
 * voorkomen binnen OPEN vacatures die overeenkomen met alle
 * actieve Pods relationship_ velden van de huidige CPT/post.
 *
 *
 * ============================================================
 * ELEMENTOR
 * ============================================================
 *
 * Elementor Source bepaalt de taxonomy.
 *
 * Voorbeeld:
 *
 * Source:
 * locatie
 *
 * Query ID:
 * relationship_level_1
 *
 *
 * Of:
 *
 * Source:
 * kennisgebied
 *
 * Query ID:
 * relationship_level_0
 *
 *
 * De taxonomy hoeft dus NIET meer in de Query ID te staan.
 *
 *
 * ============================================================
 * RELATIONSHIP LOGICA
 * ============================================================
 *
 * Verschillende taxonomies:
 * AND
 *
 * Binnen dezelfde taxonomy:
 *
 * kennisgebied
 * → AND
 *
 * overige taxonomies
 * → IN / OR
 *
 *
 * ============================================================
 * UITGESLOTEN FIELD
 * ============================================================
 *
 * relationship_vacature_button_url
 *
 * wordt nooit gebruikt om vacatures te filteren.
 *
 *
 * ============================================================
 * LEVEL
 * ============================================================
 *
 * relationship_level_0
 * → alleen top-level terms
 *
 * relationship_level_1
 * → exact één ancestor
 *
 * relationship_level_2
 * → exact twee ancestors
 *
 * Elementor Filter by Depth kan UIT blijven.
 */


/**
 * ============================================================
 * 1. CONFIG
 * ============================================================
 */

function tfsc_derived_rel_v3_vacancy_post_type() {

    return defined( 'TFSC_VACANCY_POST_TYPE' )
        ? TFSC_VACANCY_POST_TYPE
        : 'vacature';
}


function tfsc_derived_rel_v3_status_taxonomy() {

    return defined( 'TFSC_STATUS_TAXONOMY' )
        ? TFSC_STATUS_TAXONOMY
        : 'vac_status';
}


function tfsc_derived_rel_v3_open_status() {

    return defined( 'TFSC_OPEN_STATUS_SLUG' )
        ? TFSC_OPEN_STATUS_SLUG
        : 'open';
}


function tfsc_derived_rel_v3_combo_base() {

    return defined( 'TFSC_COMBO_BASE' )
        ? TFSC_COMBO_BASE
        : 'vacatures';
}


/**
 * ============================================================
 * 2. CPT / POST CONTEXT
 * ============================================================
 */

function tfsc_derived_rel_v3_get_context_post_id() {

    static $resolved = null;

    if ( null !== $resolved ) {
        return $resolved;
    }

    $resolved = 0;
    $object   = get_queried_object();


    /**
     * Normale frontend.
     */
    if (
        $object instanceof WP_Post &&
        'elementor_library' !== get_post_type( $object->ID )
    ) {
        return $resolved = (int) $object->ID;
    }


    /**
     * Elementor Theme Builder.
     */
    if (
        $object instanceof WP_Post &&
        'elementor_library' === get_post_type( $object->ID )
    ) {

        $template_id = (int) $object->ID;

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

                    $preview_id = absint(
                        $document->get_settings(
                            'preview_id'
                        )
                    );

                    if (
                        $preview_id &&
                        'elementor_library' !==
                        get_post_type( $preview_id )
                    ) {
                        return $resolved = $preview_id;
                    }
                }

            } catch ( Throwable $e ) {
                // Meta fallback hieronder.
            }
        }


        $settings = get_post_meta(
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
                'elementor_library' !==
                get_post_type( $preview_id )
            ) {
                return $resolved = $preview_id;
            }
        }
    }


    /**
     * WordPress / Elementor preview fallback.
     */
    if ( ! empty( $_GET['preview_id'] ) ) {

        $preview_id = absint(
            wp_unslash(
                $_GET['preview_id']
            )
        );

        if (
            $preview_id &&
            'elementor_library' !==
            get_post_type( $preview_id )
        ) {
            return $resolved = $preview_id;
        }
    }


    /**
     * Laatste fallback.
     */
    global $post;

    if (
        $post instanceof WP_Post &&
        'elementor_library' !==
        get_post_type( $post->ID )
    ) {
        return $resolved = (int) $post->ID;
    }


    return 0;
}


/**
 * ============================================================
 * 3. HUIDIGE POD
 * ============================================================
 */

function tfsc_derived_rel_v3_get_current_pod() {

    static $resolved = false;
    static $pod      = false;

    if ( $resolved ) {
        return $pod;
    }

    $resolved = true;

    if ( ! function_exists( 'pods' ) ) {
        return false;
    }

    $post_id =
        tfsc_derived_rel_v3_get_context_post_id();

    if ( ! $post_id ) {
        return false;
    }

    $post_type =
        get_post_type(
            $post_id
        );

    if ( ! $post_type ) {
        return false;
    }

    $pod = pods(
        $post_type,
        $post_id
    );

    if (
        ! $pod ||
        ! $pod->exists()
    ) {
        $pod = false;
    }

    return $pod;
}


/**
 * ============================================================
 * 4. RAW RELATIONSHIP NORMALISEREN
 * ============================================================
 */

function tfsc_derived_rel_v3_normalize_relationship(
    $value
) {

    if ( empty( $value ) ) {
        return [];
    }


    if (
        is_object( $value ) ||
        (
            is_array( $value ) &&
            (
                isset( $value['term_id'] ) ||
                isset( $value['pod_item_id'] ) ||
                isset( $value['id'] ) ||
                isset( $value['ID'] )
            )
        )
    ) {
        $value = [ $value ];
    }


    if ( ! is_array( $value ) ) {
        return [];
    }


    $result = [];


    foreach ( $value as $relation ) {

        $taxonomy = '';
        $term_id  = 0;


        if ( is_array( $relation ) ) {

            $taxonomy = sanitize_key(
                $relation['taxonomy']
                ?? ''
            );

            $term_id = (int) (
                $relation['term_id']
                ?? $relation['pod_item_id']
                ?? $relation['id']
                ?? $relation['ID']
                ?? 0
            );

        } elseif ( is_object( $relation ) ) {

            $taxonomy = sanitize_key(
                $relation->taxonomy
                ?? ''
            );

            $term_id = (int) (
                $relation->term_id
                ?? $relation->pod_item_id
                ?? $relation->id
                ?? $relation->ID
                ?? 0
            );

        } else {

            continue;
        }


        if (
            ! $taxonomy ||
            ! $term_id ||
            ! taxonomy_exists( $taxonomy )
        ) {
            continue;
        }


        $term = get_term(
            $term_id,
            $taxonomy
        );


        if (
            $term instanceof WP_Term &&
            ! is_wp_error( $term )
        ) {

            $result[] = [
                'taxonomy' => $taxonomy,
                'term_id'  => $term_id,
            ];
        }
    }


    return $result;
}


/**
 * ============================================================
 * 5. ALLE ACTIEVE RELATIONSHIPS
 * ============================================================
 */

function tfsc_derived_rel_v3_relationship_groups() {

    static $cache = null;

    if ( null !== $cache ) {
        return $cache;
    }

    $cache = [];

    $pod =
        tfsc_derived_rel_v3_get_current_pod();

    if ( ! $pod ) {
        return [];
    }


    $fields =
        $pod->fields();

    if (
        ! is_array( $fields ) ||
        ! $fields
    ) {
        return [];
    }


    foreach (
        $fields
        as $field_name => $definition
    ) {

        if (
            ! is_string( $field_name ) ||
            0 !== strpos(
                $field_name,
                'relationship_'
            ) ||
            'relationship_vacature_button_url'
            ===
            $field_name
        ) {
            continue;
        }


        $value =
            $pod->field(
                $field_name
            );


        $relationships =
            tfsc_derived_rel_v3_normalize_relationship(
                $value
            );


        foreach (
            $relationships
            as $relationship
        ) {

            $cache[
                $relationship['taxonomy']
            ][] =
                (int)
                $relationship['term_id'];
        }
    }


    /**
     * Dubbelen verwijderen.
     */
    foreach (
        $cache
        as $taxonomy => $ids
    ) {

        $ids = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $ids
                    )
                )
            )
        );


        if ( $ids ) {

            $cache[ $taxonomy ] =
                $ids;

        } else {

            unset(
                $cache[ $taxonomy ]
            );
        }
    }


    return $cache;
}


/**
 * ============================================================
 * 6. OPEN VACATURES OP BASIS VAN ALLE RELATIONSHIPS
 * ============================================================
 *
 * Zelfde match-logica als de Combo Base.
 *
 * Verschillende taxonomies = AND.
 *
 * Binnen dezelfde taxonomy:
 *
 * kennisgebied = AND
 * overige = IN / OR
 */

function tfsc_derived_rel_v3_get_vacancy_ids(
    $relationship_groups
) {

    static $cache = [];

    if ( empty( $relationship_groups ) ) {
        return [];
    }


    /**
     * Stabiele cache-key.
     */
    ksort(
        $relationship_groups
    );

    foreach (
        $relationship_groups
        as &$ids
    ) {

        $ids = array_values(
            array_unique(
                array_map(
                    'intval',
                    $ids
                )
            )
        );

        sort( $ids );
    }

    unset( $ids );


    $key = md5(
        serialize(
            $relationship_groups
        )
    );


    if ( isset( $cache[ $key ] ) ) {
        return $cache[ $key ];
    }


    $post_type =
        tfsc_derived_rel_v3_vacancy_post_type();


    if ( ! post_type_exists( $post_type ) ) {
        return [];
    }


    $tax_query = [
        'relation' => 'AND',
    ];


    foreach (
        $relationship_groups
        as $taxonomy => $term_ids
    ) {

        if (
            ! taxonomy_exists( $taxonomy ) ||
            ! $term_ids
        ) {
            continue;
        }


        $term_ids = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'intval',
                        $term_ids
                    )
                )
            )
        );


        if ( ! $term_ids ) {
            continue;
        }


        /**
         * Match-methode uit de Combo Base gebruiken.
         *
         * Fallback:
         * kennisgebied = AND
         * overige = OR
         */
        $method =
            function_exists(
                'tfsc_get_combo_taxonomy_match_method'
            )
                ? tfsc_get_combo_taxonomy_match_method(
                    $taxonomy
                )
                : (
                    'kennisgebied' === $taxonomy
                        ? 'AND'
                        : 'OR'
                );


        $tax_query[] = [
            'taxonomy' =>
                $taxonomy,

            'field' =>
                'term_id',

            'terms' =>
                $term_ids,

            'operator' =>
                (
                    'AND' === $method &&
                    count( $term_ids ) > 1
                )
                    ? 'AND'
                    : 'IN',

            'include_children' =>
                false,
        ];
    }


    /**
     * Geen bruikbare relationship-query.
     */
    if ( count( $tax_query ) <= 1 ) {
        return [];
    }


    /**
     * Alleen OPEN vacatures.
     */
    $status_taxonomy =
        tfsc_derived_rel_v3_status_taxonomy();


    if ( taxonomy_exists( $status_taxonomy ) ) {

        $tax_query[] = [
            'taxonomy' =>
                $status_taxonomy,

            'field' =>
                'slug',

            'terms' => [
                tfsc_derived_rel_v3_open_status(),
            ],

            'operator' =>
                'IN',

            'include_children' =>
                false,
        ];
    }


    $ids = get_posts( [
        'post_type' =>
            $post_type,

        'post_status' =>
            'publish',

        'posts_per_page' =>
            -1,

        'fields' =>
            'ids',

        'no_found_rows' =>
            true,

        'suppress_filters' =>
            false,

        'tax_query' =>
            $tax_query,
    ] );


    return $cache[ $key ] =
        array_values(
            array_unique(
                array_map(
                    'intval',
                    $ids
                )
            )
        );
}


/**
 * ============================================================
 * 7. TARGET TERMS UIT VACATURES
 * ============================================================
 */

function tfsc_derived_rel_v3_get_target_term_ids(
    $vacancy_ids,
    $taxonomy
) {

    if (
        ! $vacancy_ids ||
        ! taxonomy_exists( $taxonomy )
    ) {
        return [];
    }


    $ids = wp_get_object_terms(
        array_map(
            'intval',
            $vacancy_ids
        ),
        $taxonomy,
        [
            'fields' => 'ids',
        ]
    );


    if (
        is_wp_error( $ids ) ||
        ! $ids
    ) {
        return [];
    }


    $ids =
        array_map(
            'intval',
            $ids
        );


    if (
        is_taxonomy_hierarchical(
            $taxonomy
        )
    ) {

        foreach ( $ids as $id ) {

            $ids = array_merge(
                $ids,
                get_ancestors(
                    $id,
                    $taxonomy,
                    'taxonomy'
                )
            );
        }
    }


    return array_values(
        array_unique(
            array_filter(
                array_map(
                    'intval',
                    $ids
                )
            )
        )
    );
}


/**
 * ============================================================
 * 8. EXACT LEVEL
 * ============================================================
 */

function tfsc_derived_rel_v3_filter_level(
    $ids,
    $taxonomy,
    $level
) {

    if ( ! $ids ) {
        return [];
    }


    if (
        ! is_taxonomy_hierarchical(
            $taxonomy
        )
    ) {

        return 0 === (int) $level
            ? array_values(
                array_unique(
                    array_map(
                        'intval',
                        $ids
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
                    $ids
                )
            ),
            function( $id ) use (
                $taxonomy,
                $level
            ) {

                return count(
                    get_ancestors(
                        $id,
                        $taxonomy,
                        'taxonomy'
                    )
                ) === (int) $level;
            }
        )
    );
}


/**
 * ============================================================
 * 9. QUERY ID
 * ============================================================
 */

function tfsc_derived_rel_v3_parse_query_id(
    $query_id
) {

    if (
        ! preg_match(
            '/^relationship_level_([0-9]+)$/',
            (string) $query_id,
            $matches
        )
    ) {
        return false;
    }


    return [
        'level' =>
            (int) $matches[1],
    ];
}


/**
 * ============================================================
 * 10. ELEMENTOR TAXONOMY LOOP
 * ============================================================
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
            tfsc_derived_rel_v3_parse_query_id(
                $query_id
            );


        if ( ! $parsed ) {
            return $args;
        }


        /**
         * Elementor Source = target taxonomy.
         */
        $taxonomy =
            $args['taxonomy']
            ??
            $settings[
                'post_taxonomy_query_post_type'
            ]
            ??
            '';


        if ( is_array( $taxonomy ) ) {
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
            ! taxonomy_exists( $taxonomy ) ||
            $taxonomy ===
            tfsc_derived_rel_v3_status_taxonomy()
        ) {
            return $args;
        }


        /**
         * Alle actieve relationship selections.
         */
        $relationship_groups =
            tfsc_derived_rel_v3_relationship_groups();


        if ( ! $relationship_groups ) {

            $args['include'] = [ 0 ];

            return $args;
        }


        /**
         * Alleen vacatures die voldoen aan de volledige
         * relationship-combinatie.
         */
        $vacancy_ids =
            tfsc_derived_rel_v3_get_vacancy_ids(
                $relationship_groups
            );


        if ( ! $vacancy_ids ) {

            $args['include'] = [ 0 ];

            return $args;
        }


        /**
         * Terms van Elementor Source uit deze vacatures.
         */
        $target_ids =
            tfsc_derived_rel_v3_get_target_term_ids(
                $vacancy_ids,
                $taxonomy
            );


        /**
         * Exact level.
         */
        $target_ids =
            tfsc_derived_rel_v3_filter_level(
                $target_ids,
                $taxonomy,
                $parsed['level']
            );


        if ( ! $target_ids ) {

            $args['include'] = [ 0 ];

            return $args;
        }


        unset(
            $args['term_taxonomy_id']
        );


        $args['taxonomy'] =
            $taxonomy;

        $args['include'] =
            $target_ids;

        $args['hide_empty'] =
            false;

        $args['hierarchical'] =
            false;


        /**
         * Marker voor sorteerfix.
         */
        $args[
            'tfsc_derived_relationship_query'
        ] = 1;


        /**
         * Marker voor combo term-links.
         */
        $GLOBALS[
            'tfsc_derived_rel_v3_active_targets'
        ][ $taxonomy ] =
            true;


        return $args;
    },
    30,
    3
);


/**
 * ============================================================
 * 11. SORTERING
 * ============================================================
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
            is_admin() ||
            empty(
                $args[
                    'tfsc_derived_relationship_query'
                ]
            ) ||
            ! is_array( $terms ) ||
            ! $terms
        ) {
            return $terms;
        }


        if (
            isset( $args['orderby'] ) &&
            'name' !== strtolower(
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
            $order = 'ASC';
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


                return 'DESC' === $order
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
 * 12. TERMPAD
 * ============================================================
 */

function tfsc_derived_rel_v3_term_path(
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


    $ids =
        is_taxonomy_hierarchical(
            $term->taxonomy
        )
            ? array_reverse(
                get_ancestors(
                    $term->term_id,
                    $term->taxonomy,
                    'taxonomy'
                )
            )
            : [];


    $slugs = [];


    foreach ( $ids as $id ) {

        $ancestor =
            get_term(
                $id,
                $term->taxonomy
            );


        if (
            $ancestor instanceof WP_Term &&
            ! is_wp_error( $ancestor )
        ) {
            $slugs[] =
                $ancestor->slug;
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
 * 13. COMBO URL
 * ============================================================
 *
 * URL-logica bewust ongewijzigd behouden.
 */

function tfsc_derived_rel_v3_build_combo_url(
    $target_term
) {

    if (
        ! $target_term instanceof WP_Term
    ) {
        return false;
    }


    $groups =
        tfsc_derived_rel_v3_relationship_groups();


    if ( ! $groups ) {
        return false;
    }


    $parts = [
        tfsc_derived_rel_v3_combo_base(),
    ];


    /**
     * Alle actieve relationships toevoegen.
     */
    foreach (
        $groups
        as $taxonomy => $term_ids
    ) {

        foreach (
            $term_ids
            as $term_id
        ) {

            /**
             * Exact dezelfde target-term niet dubbel toevoegen.
             */
            if (
                $taxonomy ===
                $target_term->taxonomy &&
                (int) $term_id ===
                (int) $target_term->term_id
            ) {
                continue;
            }


            $term =
                get_term(
                    $term_id,
                    $taxonomy
                );


            if (
                ! $term instanceof WP_Term ||
                is_wp_error( $term )
            ) {
                continue;
            }


            $path =
                tfsc_derived_rel_v3_term_path(
                    $term
                );


            if ( ! $path ) {
                continue;
            }


            $parts[] =
                $taxonomy;

            $parts[] =
                $path;
        }
    }


    /**
     * Aangeklikte target term toevoegen.
     */
    $target_path =
        tfsc_derived_rel_v3_term_path(
            $target_term
        );


    if ( ! $target_path ) {
        return false;
    }


    $parts[] =
        $target_term->taxonomy;

    $parts[] =
        $target_path;


    return home_url(
        '/'
        .
        implode(
            '/',
            array_map(
                fn( $part ) =>
                    trim(
                        $part,
                        '/'
                    ),
                $parts
            )
        )
        .
        '/'
    );
}


/**
 * ============================================================
 * 14. TERM LINKS → COMBO ARCHIVES
 * ============================================================
 */

add_filter(
    'term_link',
    function(
        $termlink,
        $term,
        $taxonomy
    ) {

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
         * Alleen CPT/post-context.
         */
        $object =
            get_queried_object();


        if (
            ! $object instanceof WP_Post
        ) {
            return $termlink;
        }


        if (
            $taxonomy ===
            tfsc_derived_rel_v3_status_taxonomy()
        ) {
            return $termlink;
        }


        /**
         * Alleen taxonomies die daadwerkelijk door deze
         * Elementor relationship-loop geactiveerd zijn.
         */
        if (
            empty(
                $GLOBALS[
                    'tfsc_derived_rel_v3_active_targets'
                ][ $taxonomy ]
            )
        ) {
            return $termlink;
        }


        $url =
            tfsc_derived_rel_v3_build_combo_url(
                $term
            );


        return $url
            ?: $termlink;
    },
    30,
    3
);
