<?php

/**
 * TAXONOMIES → Relationship Taxonomies Pods  → [V5.0]
 *
 * Voor op de taxonomy archives, de gekoppelde taxonomies and terms. Maak een relationship_taxonomy field op een pods taxonomy, selecteer de term op de gelinkte taxonomy zelf onder cpt&gt;taxonomyGebruik opmaak code: archive_relationship_level_{nummer}Voorbeeld: archive_relationship_level_0
 */
/**
 * TFSC - Taxonomy Archive Derived Relationship Terms
 * Version 4
 *
 * ============================================================
 * DOEL
 * ============================================================
 *
 * Op een taxonomy-term:
 *
 * huidige term
 * → alle ingevulde relationship_* velden op die term
 * → relevante OPEN vacatures
 * → terms uit Elementor Source taxonomy
 * → Elementor Taxonomy Loop
 *
 * Verschillende taxonomies = AND
 * Meerdere terms binnen dezelfde taxonomy = OR / IN
 *
 * relationship_vacature_button_url wordt bewust NIET gebruikt
 * voor de vacaturequery. Dat veld blijft een expliciete
 * vacaturebutton-override.
 *
 *
 * ELEMENTOR QUERY IDS
 * ============================================================
 *
 * archive_relationship_level_0
 * archive_relationship_level_1
 * archive_relationship_level_2
 *
 * De Elementor Source bepaalt de target taxonomy.
 */


/**
 * ============================================================
 * 1. CONFIG
 * ============================================================
 */

function tfsc_taxrel_v4_vacancy_post_type() {
    return defined( 'TFSC_VACANCY_POST_TYPE' )
        ? TFSC_VACANCY_POST_TYPE
        : 'vacature';
}

function tfsc_taxrel_v4_status_taxonomy() {
    return defined( 'TFSC_STATUS_TAXONOMY' )
        ? TFSC_STATUS_TAXONOMY
        : 'vac_status';
}

function tfsc_taxrel_v4_open_status() {
    return defined( 'TFSC_OPEN_STATUS_SLUG' )
        ? TFSC_OPEN_STATUS_SLUG
        : 'open';
}

function tfsc_taxrel_v4_combo_base() {
    return defined( 'TFSC_COMBO_BASE' )
        ? TFSC_COMBO_BASE
        : 'vacatures';
}


/**
 * ============================================================
 * 2. HUIDIGE TAXONOMY TERM
 * ============================================================
 *
 * Elementor Theme Builder editorfix bewust behouden.
 */

function tfsc_taxrel_v4_current_term() {

    static $cached = null;

    if ( null !== $cached ) {
        return $cached;
    }

    $object = get_queried_object();

    /**
     * Normaal taxonomy archive / preview iframe.
     */
    if (
        $object instanceof WP_Term &&
        ! empty( $object->term_id ) &&
        ! empty( $object->taxonomy ) &&
        taxonomy_exists( $object->taxonomy )
    ) {

        if ( is_user_logged_in() ) {
            set_transient(
                'tfsc_taxrel_v4_preview_' . get_current_user_id(),
                [
                    'term_id'  => (int) $object->term_id,
                    'taxonomy' => $object->taxonomy,
                ],
                5 * MINUTE_IN_SECONDS
            );
        }

        return $cached = $object;
    }

    /**
     * Elementor admin-render.
     */
    if (
        is_admin() &&
        $object instanceof WP_Post &&
        is_user_logged_in()
    ) {

        $preview = get_transient(
            'tfsc_taxrel_v4_preview_' . get_current_user_id()
        );

        if (
            ! is_array( $preview ) ||
            empty( $preview['term_id'] ) ||
            empty( $preview['taxonomy'] ) ||
            ! taxonomy_exists( $preview['taxonomy'] )
        ) {
            return $cached = false;
        }

        $settings = get_post_meta(
            $object->ID,
            '_elementor_page_settings',
            true
        );

        $preview_type =
            is_array( $settings )
                ? ( $settings['preview_type'] ?? '' )
                : '';

        if (
            $preview_type !==
            'taxonomy/' . $preview['taxonomy']
        ) {
            return $cached = false;
        }

        $term = get_term(
            (int) $preview['term_id'],
            $preview['taxonomy']
        );

        if (
            $term instanceof WP_Term &&
            ! is_wp_error( $term )
        ) {
            return $cached = $term;
        }
    }

    return $cached = false;
}


/**
 * ============================================================
 * 3. HUIDIGE TERM POD
 * ============================================================
 */

function tfsc_taxrel_v4_current_pod() {

    static $resolved = false;
    static $pod = false;

    if ( $resolved ) {
        return $pod;
    }

    $resolved = true;

    if ( ! function_exists( 'pods' ) ) {
        return false;
    }

    $term = tfsc_taxrel_v4_current_term();

    if ( ! $term ) {
        return false;
    }

    $pod = pods(
        $term->taxonomy,
        (int) $term->term_id
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
 * 4. RELATIONSHIP DATA NORMALISEREN
 * ============================================================
 */

function tfsc_taxrel_v4_normalize_relationship(
    $value,
    $fallback_taxonomy = ''
) {

    $groups = [];

    if ( empty( $value ) ) {
        return [];
    }

    if (
        is_numeric( $value ) ||
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

    foreach ( (array) $value as $relation ) {

        $term_id  = 0;
        $taxonomy = '';

        if ( is_array( $relation ) ) {

            $term_id = (int) (
                $relation['term_id']
                ?? $relation['pod_item_id']
                ?? $relation['id']
                ?? $relation['ID']
                ?? 0
            );

            $taxonomy = sanitize_key(
                $relation['taxonomy']
                ?? ''
            );

        } elseif ( is_object( $relation ) ) {

            $term_id = (int) (
                $relation->term_id
                ?? $relation->pod_item_id
                ?? $relation->id
                ?? $relation->ID
                ?? 0
            );

            $taxonomy = sanitize_key(
                $relation->taxonomy
                ?? ''
            );

        } elseif ( is_numeric( $relation ) ) {

            $term_id = (int) $relation;
        }

        if ( ! $taxonomy ) {
            $taxonomy = sanitize_key(
                $fallback_taxonomy
            );
        }

        if (
            ! $term_id ||
            ! $taxonomy ||
            ! taxonomy_exists( $taxonomy )
        ) {
            continue;
        }

        $term = get_term(
            $term_id,
            $taxonomy
        );

        if (
            ! $term instanceof WP_Term ||
            is_wp_error( $term )
        ) {
            continue;
        }

        $groups[ $taxonomy ][] =
            (int) $term_id;
    }

    foreach ( $groups as $taxonomy => $ids ) {
        $groups[ $taxonomy ] = array_values(
            array_unique(
                array_map(
                    'intval',
                    $ids
                )
            )
        );
    }

    return array_filter( $groups );
}


/**
 * ============================================================
 * 5. ALLE ACTIEVE RELATIONSHIPS OP DE TERM
 * ============================================================
 *
 * Scant alle relationship_* velden.
 *
 * relationship_vacature_button_url wordt uitgesloten.
 */

function tfsc_taxrel_v4_relationship_groups() {

    static $cache = null;

    if ( null !== $cache ) {
        return $cache;
    }

    $cache = [];

    $pod = tfsc_taxrel_v4_current_pod();

    if ( ! $pod ) {
        return [];
    }

    $fields = $pod->fields();

    if ( ! is_array( $fields ) ) {
        return [];
    }

    foreach ( $fields as $field_name => $field_data ) {

        if (
            0 !== strpos(
                $field_name,
                'relationship_'
            ) ||
            'relationship_vacature_button_url'
            === $field_name
        ) {
            continue;
        }

        $value = $pod->field(
            $field_name
        );

        if ( empty( $value ) ) {
            continue;
        }

        $fallback_taxonomy =
            sanitize_key(
                substr(
                    $field_name,
                    strlen( 'relationship_' )
                )
            );

        $groups =
            tfsc_taxrel_v4_normalize_relationship(
                $value,
                $fallback_taxonomy
            );

        foreach (
            $groups
            as $taxonomy => $ids
        ) {
            $cache[ $taxonomy ] = array_merge(
                $cache[ $taxonomy ] ?? [],
                $ids
            );
        }
    }

    foreach (
        $cache
        as $taxonomy => $ids
    ) {
        $cache[ $taxonomy ] =
            array_values(
                array_unique(
                    array_map(
                        'intval',
                        $ids
                    )
                )
            );
    }

    return array_filter( $cache );
}


/**
 * ============================================================
 * 6. OPEN VACATURES
 * ============================================================
 */

function tfsc_taxrel_v4_vacancy_ids(
    $groups
) {

    static $cache = [];

    if ( empty( $groups ) ) {
        return [];
    }

    $cache_key =
        md5(
            serialize( $groups )
        );

    if ( isset( $cache[ $cache_key ] ) ) {
        return $cache[ $cache_key ];
    }

    $tax_query = [
        'relation' => 'AND',
    ];

    /**
     * Verschillende taxonomies = AND.
     * Binnen dezelfde taxonomy = IN / OR.
     */
    foreach (
        $groups
        as $taxonomy => $ids
    ) {

        if (
            ! taxonomy_exists( $taxonomy ) ||
            ! $ids
        ) {
            continue;
        }

        $tax_query[] = [
            'taxonomy'         => $taxonomy,
            'field'            => 'term_id',
            'terms'            => $ids,
            'operator'         => 'IN',
            'include_children' => false,
        ];
    }

    if ( count( $tax_query ) <= 1 ) {
        return [];
    }

    $status_taxonomy =
        tfsc_taxrel_v4_status_taxonomy();

    if ( taxonomy_exists( $status_taxonomy ) ) {
        $tax_query[] = [
            'taxonomy'         => $status_taxonomy,
            'field'            => 'slug',
            'terms'            => [
                tfsc_taxrel_v4_open_status(),
            ],
            'operator'         => 'IN',
            'include_children' => false,
        ];
    }

    $ids = get_posts( [
        'post_type'      =>
            tfsc_taxrel_v4_vacancy_post_type(),

        'post_status'    =>
            'publish',

        'posts_per_page' =>
            -1,

        'fields'         =>
            'ids',

        'no_found_rows'  =>
            true,

        'tax_query'      =>
            $tax_query,
    ] );

    return $cache[ $cache_key ] =
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
 * 7. TARGET TERMS
 * ============================================================
 */

function tfsc_taxrel_v4_target_terms(
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

    $ids = array_map(
        'intval',
        $ids
    );

    if (
        is_taxonomy_hierarchical(
            $taxonomy
        )
    ) {

        foreach ( $ids as $term_id ) {

            $ids = array_merge(
                $ids,
                get_ancestors(
                    $term_id,
                    $taxonomy,
                    'taxonomy'
                )
            );
        }
    }

    return array_values(
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
 * 8. EXACT LEVEL
 * ============================================================
 *
 * 0 = top-level
 * 1 = één ancestor
 * 2 = twee ancestors
 */

function tfsc_taxrel_v4_filter_level(
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
            function( $term_id ) use (
                $taxonomy,
                $level
            ) {

                return count(
                    get_ancestors(
                        $term_id,
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
 *
 * archive_relationship_level_0
 * archive_relationship_level_1
 * archive_relationship_level_2
 */

function tfsc_taxrel_v4_parse_query_id(
    $query_id
) {

    if (
        ! preg_match(
            '/^archive_relationship_level_([0-9]+)$/',
            (string) $query_id,
            $matches
        )
    ) {
        return false;
    }

    return (int) $matches[1];
}


/**
 * ============================================================
 * 10. ELEMENTOR TAXONOMY LOOP
 * ============================================================
 *
 * Target taxonomy komt volledig uit Elementor Source.
 *
 * Priority 40 bewust behouden.
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
            $settings['post_taxonomy_term_taxonomy_id']
            ??
            '';

        $level =
            tfsc_taxrel_v4_parse_query_id(
                $query_id
            );

        if (
            false === $level ||
            ! tfsc_taxrel_v4_current_term()
        ) {
            return $args;
        }

        /**
         * Elementor Source bepaalt target taxonomy.
         */
        $taxonomy =
            $args['taxonomy']
            ??
            $settings['post_taxonomy_query_post_type']
            ??
            '';

        if ( is_array( $taxonomy ) ) {
            $taxonomy =
                reset( $taxonomy );
        }

        $taxonomy =
            sanitize_key(
                $taxonomy
            );

        if (
            ! $taxonomy ||
            ! taxonomy_exists( $taxonomy ) ||
            $taxonomy ===
            tfsc_taxrel_v4_status_taxonomy()
        ) {
            return $args;
        }

        $groups =
            tfsc_taxrel_v4_relationship_groups();

        $vacancy_ids =
            tfsc_taxrel_v4_vacancy_ids(
                $groups
            );

        $target_ids =
            tfsc_taxrel_v4_target_terms(
                $vacancy_ids,
                $taxonomy
            );

        $target_ids =
            tfsc_taxrel_v4_filter_level(
                $target_ids,
                $taxonomy,
                $level
            );

        if ( ! $target_ids ) {
            $args['include'] = [ 0 ];
            return $args;
        }

        unset(
            $args['term_taxonomy_id']
        );

        $args['taxonomy']     = $taxonomy;
        $args['include']      = $target_ids;
        $args['hide_empty']   = false;
        $args['hierarchical'] = false;

        /**
         * Marker voor sortering.
         */
        $args['tfsc_taxrel_v4'] = 1;

        /**
         * Alleen deze target-taxonomy krijgt combo-links.
         */
        $GLOBALS[
            'tfsc_taxrel_v4_active_targets'
        ][ $taxonomy ] = true;

        return $args;
    },
    40,
    3
);


/**
 * ============================================================
 * 11. SORTERING OP NAAM
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
            empty( $args['tfsc_taxrel_v4'] ) ||
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

        usort(
            $terms,
            function( $a, $b ) use ( $order ) {

                if (
                    ! $a instanceof WP_Term ||
                    ! $b instanceof WP_Term
                ) {
                    return 0;
                }

                $compare =
                    strnatcasecmp(
                        remove_accents( $a->name ),
                        remove_accents( $b->name )
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
 * 12. TERM PATH
 * ============================================================
 */

function tfsc_taxrel_v4_term_path(
    $term
) {

    if ( ! $term instanceof WP_Term ) {
        return '';
    }

    $slugs = [];

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
                $ancestor instanceof WP_Term &&
                ! is_wp_error( $ancestor )
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
 * 13. COMBO URL
 * ============================================================
 *
 * Alle actieve relationships
 * +
 * aangeklikte target term.
 */

function tfsc_taxrel_v4_combo_url(
    $target_term = null
) {

    $groups =
        tfsc_taxrel_v4_relationship_groups();

    if ( ! $groups ) {
        return false;
    }

    $parts = [
        tfsc_taxrel_v4_combo_base(),
    ];

    foreach (
        $groups
        as $taxonomy => $ids
    ) {

        foreach ( $ids as $term_id ) {

            /**
             * Exact dezelfde target term niet dubbel opnemen.
             */
            if (
                $target_term instanceof WP_Term &&
                $taxonomy === $target_term->taxonomy &&
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

            $parts[] =
                $taxonomy;

            $parts[] =
                tfsc_taxrel_v4_term_path(
                    $term
                );
        }
    }

    if (
        $target_term instanceof WP_Term
    ) {
        $parts[] =
            $target_term->taxonomy;

        $parts[] =
            tfsc_taxrel_v4_term_path(
                $target_term
            );
    }

    return home_url(
        '/'
        .
        implode(
            '/',
            array_map(
                static fn( $part ) =>
                    trim( $part, '/' ),
                $parts
            )
        )
        .
        '/'
    );
}


/**
 * ============================================================
 * 14. TERM LINKS → COMBO URL
 * ============================================================
 *
 * Native term-link bypass blijft gerespecteerd.
 *
 * Priority 40 bewust behouden.
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
            ! tfsc_taxrel_v4_current_term()
        ) {
            return $termlink;
        }

        if (
            empty(
                $GLOBALS[
                    'tfsc_taxrel_v4_active_targets'
                ][ $taxonomy ]
            )
        ) {
            return $termlink;
        }

        $url =
            tfsc_taxrel_v4_combo_url(
                $term
            );

        return $url
            ?: $termlink;
    },
    40,
    3
);
