<?php

/**
 * Base Tax Archive → Relationship Vacancy Terms via Archive Settings
 */
/**
 * TFSC - Base Archive → Vacancy Terms via Relationship Field
 *
 * ============================================================
 * DOEL
 * ============================================================
 *
 * Taxonomy base archive
 * → archive settings relationship field
 * → Pods fielddefinitie bepaalt target vacature-taxonomy
 * → optionele geselecteerde terms beperken de lijst
 * → alleen terms tonen die OPEN vacatures hebben
 *
 *
 * VELDNAAM
 * ============================================================
 *
 * archive_{context}_{taxonomy}_relationship
 *
 * Voorbeeld:
 *
 * huidig archive:
 * beroep_functienaam
 *
 * settings field:
 * archive_beroep_functienaam_relationship
 *
 * relationship field wijst in Pods naar taxonomy:
 * functienaam
 *
 *
 * QUERY IDS
 * ============================================================
 *
 * base_vacancy_terms_level_0
 * base_vacancy_terms_level_1
 * base_vacancy_terms_level_2
 */


/**
 * ============================================================
 * 1. CONFIG
 * ============================================================
 */

function tfsc_basevac_v3_vacancy_post_type() {
    return defined( 'TFSC_VACANCY_POST_TYPE' )
        ? TFSC_VACANCY_POST_TYPE
        : 'vacature';
}

function tfsc_basevac_v3_status_taxonomy() {
    return defined( 'TFSC_STATUS_TAXONOMY' )
        ? TFSC_STATUS_TAXONOMY
        : 'vac_status';
}

function tfsc_basevac_v3_open_status() {
    return defined( 'TFSC_OPEN_STATUS_SLUG' )
        ? TFSC_OPEN_STATUS_SLUG
        : 'open';
}

function tfsc_basevac_v3_settings_pod() {
    return 'pods_archief_instellingen';
}


/**
 * ============================================================
 * 2. HUIDIGE BASE TAXONOMY
 * ============================================================
 */

function tfsc_basevac_v3_base_taxonomy() {

    if (
        ! function_exists(
            'tfsc_get_current_taxonomy_archive'
        )
    ) {
        return '';
    }

    $taxonomy =
        tfsc_get_current_taxonomy_archive();

    if ( $taxonomy instanceof WP_Taxonomy ) {
        $taxonomy = $taxonomy->name;
    }

    return sanitize_key(
        (string) $taxonomy
    );
}


/**
 * ============================================================
 * 3. TAXONOMY NORMALISEREN
 * ============================================================
 *
 * beroep_functienaam
 * → functienaam
 */

function tfsc_basevac_v3_normalize_taxonomy(
    $taxonomy
) {

    $taxonomy =
        sanitize_key(
            $taxonomy
        );

    foreach (
        [
            'beroep_',
            'beroep-',
            'beroepengids_',
            'beroepengids-',
        ]
        as $prefix
    ) {

        if (
            0 === strpos(
                $taxonomy,
                $prefix
            )
        ) {
            return sanitize_key(
                substr(
                    $taxonomy,
                    strlen( $prefix )
                )
            );
        }
    }

    return $taxonomy;
}


/**
 * ============================================================
 * 4. RELATIONSHIP FIELD NAME
 * ============================================================
 *
 * archive_beroep_functienaam_relationship
 */

function tfsc_basevac_v3_relationship_field_name(
    $context = 'beroep'
) {

    $taxonomy =
        tfsc_basevac_v3_normalize_taxonomy(
            tfsc_basevac_v3_base_taxonomy()
        );

    if ( ! $taxonomy ) {
        return '';
    }

    return sanitize_key(
        'archive_'
        . sanitize_key( $context )
        . '_'
        . $taxonomy
        . '_relationship'
    );
}


/**
 * ============================================================
 * 5. SETTINGS POD
 * ============================================================
 */

function tfsc_basevac_v3_settings_object() {

    static $pod = null;

    if ( null !== $pod ) {
        return $pod;
    }

    if ( ! function_exists( 'pods' ) ) {
        return $pod = false;
    }

    $pod =
        pods(
            tfsc_basevac_v3_settings_pod()
        );

    return $pod ?: false;
}


/**
 * ============================================================
 * 6. TARGET TAXONOMY UIT PODS FIELDDEFINITIE
 * ============================================================
 *
 * De inhoud van het relationshipveld is NIET leidend.
 *
 * De Pods fielddefinitie bepaalt naar welke taxonomy
 * het relationshipveld verwijst.
 */

function tfsc_basevac_v3_target_taxonomy(
    $context = 'beroep'
) {

    static $cache = [];

    $field_name =
        tfsc_basevac_v3_relationship_field_name(
            $context
        );

    if ( ! $field_name ) {
        return '';
    }

    if ( isset( $cache[ $field_name ] ) ) {
        return $cache[ $field_name ];
    }

    $pod =
        tfsc_basevac_v3_settings_object();

    if ( ! $pod ) {
        return $cache[ $field_name ] = '';
    }

    $fields =
        $pod->fields();

    if (
        ! is_array( $fields ) ||
        empty( $fields[ $field_name ] )
    ) {
        return $cache[ $field_name ] = '';
    }

    $field =
        $fields[ $field_name ];

    /**
     * Pods relationship fields bevatten doorgaans:
     *
     * pick_object = taxonomy
     * pick_val    = functienaam
     */
    $pick_object =
        sanitize_key(
            $field['pick_object']
            ?? ''
        );

    $pick_val =
        sanitize_key(
            $field['pick_val']
            ?? ''
        );

    if (
        'taxonomy' === $pick_object &&
        $pick_val &&
        taxonomy_exists( $pick_val )
    ) {
        return $cache[ $field_name ] =
            $pick_val;
    }

    /**
     * Extra fallback voor afwijkende Pods arrays.
     */
    foreach (
        [
            'taxonomy',
            'pick_val',
            'object',
            'name',
        ]
        as $key
    ) {

        $candidate =
            sanitize_key(
                $field[ $key ]
                ?? ''
            );

        if (
            $candidate &&
            taxonomy_exists( $candidate )
        ) {
            return $cache[ $field_name ] =
                $candidate;
        }
    }

    return $cache[ $field_name ] = '';
}


/**
 * ============================================================
 * 7. GESELECTEERDE RELATIONSHIP TERMS
 * ============================================================
 *
 * Relationship leeg:
 * → geen beperking
 *
 * Relationship gevuld:
 * → alleen geselecteerde terms meenemen
 */

function tfsc_basevac_v3_selected_term_ids(
    $taxonomy,
    $context = 'beroep'
) {

    $pod =
        tfsc_basevac_v3_settings_object();

    $field_name =
        tfsc_basevac_v3_relationship_field_name(
            $context
        );

    if (
        ! $pod ||
        ! $field_name ||
        ! $taxonomy
    ) {
        return [];
    }

    $value =
        $pod->field(
            $field_name
        );

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

    $ids = [];

    foreach (
        (array) $value
        as $relation
    ) {

        $term_id = 0;
        $rel_taxonomy = '';

        if ( is_array( $relation ) ) {

            $term_id = (int) (
                $relation['term_id']
                ?? $relation['pod_item_id']
                ?? $relation['id']
                ?? $relation['ID']
                ?? 0
            );

            $rel_taxonomy =
                sanitize_key(
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

            $rel_taxonomy =
                sanitize_key(
                    $relation->taxonomy
                    ?? ''
                );

        } elseif ( is_numeric( $relation ) ) {

            $term_id =
                (int) $relation;
        }

        if (
            $rel_taxonomy &&
            $rel_taxonomy !== $taxonomy
        ) {
            continue;
        }

        if ( ! $term_id ) {
            continue;
        }

        $term =
            get_term(
                $term_id,
                $taxonomy
            );

        if (
            $term instanceof WP_Term &&
            ! is_wp_error( $term )
        ) {
            $ids[] =
                $term_id;
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
 * 8. OPEN VACATURE IDS
 * ============================================================
 */

function tfsc_basevac_v3_open_vacancy_ids() {

    static $ids = null;

    if ( null !== $ids ) {
        return $ids;
    }

    $tax_query = [];

    $status_taxonomy =
        tfsc_basevac_v3_status_taxonomy();

    if (
        taxonomy_exists(
            $status_taxonomy
        )
    ) {
        $tax_query[] = [
            'taxonomy'         => $status_taxonomy,
            'field'            => 'slug',
            'terms'            => [
                tfsc_basevac_v3_open_status(),
            ],
            'operator'         => 'IN',
            'include_children' => false,
        ];
    }

    $ids = get_posts( [
        'post_type'      =>
            tfsc_basevac_v3_vacancy_post_type(),

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

    return $ids =
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
 * 9. TERMS UIT OPEN VACATURES
 * ============================================================
 */

function tfsc_basevac_v3_available_term_ids(
    $taxonomy
) {

    $vacancy_ids =
        tfsc_basevac_v3_open_vacancy_ids();

    if (
        ! $vacancy_ids ||
        ! taxonomy_exists( $taxonomy )
    ) {
        return [];
    }

    $ids =
        wp_get_object_terms(
            $vacancy_ids,
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

    /**
     * Ancestors meenemen zodat levels correct werken.
     */
    if (
        is_taxonomy_hierarchical(
            $taxonomy
        )
    ) {

        foreach (
            $ids
            as $term_id
        ) {

            $ids =
                array_merge(
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
 * 10. OPTIONELE SETTINGS SELECTIE TOEPASSEN
 * ============================================================
 */

function tfsc_basevac_v3_term_ids(
    $taxonomy,
    $context = 'beroep'
) {

    $available =
        tfsc_basevac_v3_available_term_ids(
            $taxonomy
        );

    if ( ! $available ) {
        return [];
    }

    $selected =
        tfsc_basevac_v3_selected_term_ids(
            $taxonomy,
            $context
        );

    /**
     * Niets geselecteerd:
     * alle beschikbare terms tonen.
     */
    if ( ! $selected ) {
        return $available;
    }

    /**
     * Wel geselecteerd:
     * alleen geselecteerde terms die ook vacatures hebben.
     */
    return array_values(
        array_intersect(
            $available,
            $selected
        )
    );
}


/**
 * ============================================================
 * 11. EXACT LEVEL
 * ============================================================
 */

function tfsc_basevac_v3_filter_level(
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
            ? $ids
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
 * 12. QUERY ID
 * ============================================================
 */

function tfsc_basevac_v3_parse_query_id(
    $query_id
) {

    if (
        ! preg_match(
            '/^base_vacancy_terms_level_([0-9]+)$/',
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
 * 13. ELEMENTOR TAXONOMY LOOP
 * ============================================================
 *
 * Elementor Source wordt volledig door deze snippet bepaald.
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
            tfsc_basevac_v3_parse_query_id(
                $query_id
            );

        if ( false === $level ) {
            return $args;
        }

        $taxonomy =
            tfsc_basevac_v3_target_taxonomy(
                'beroep'
            );

        if (
            ! $taxonomy ||
            $taxonomy ===
            tfsc_basevac_v3_status_taxonomy()
        ) {
            $args['include'] = [ 0 ];
            return $args;
        }

        $term_ids =
            tfsc_basevac_v3_term_ids(
                $taxonomy,
                'beroep'
            );

        $term_ids =
            tfsc_basevac_v3_filter_level(
                $term_ids,
                $taxonomy,
                $level
            );

        if ( ! $term_ids ) {
            $args['include'] = [ 0 ];
            return $args;
        }

        unset(
            $args['term_taxonomy_id']
        );

        $args['taxonomy']     = $taxonomy;
        $args['include']      = $term_ids;
        $args['hide_empty']   = false;
        $args['hierarchical'] = false;

        /**
         * Marker voor alfabetische sortering.
         */
        $args['tfsc_basevac_v3'] =
            1;

        return $args;
    },
    35,
    3
);


/**
 * ============================================================
 * 14. ALFABETISCHE SORTERING
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
            empty(
                $args['tfsc_basevac_v3']
            ) ||
            ! is_array( $terms ) ||
            ! $terms
        ) {
            return $terms;
        }

        usort(
            $terms,
            function( $a, $b ) {

                if (
                    ! $a instanceof WP_Term ||
                    ! $b instanceof WP_Term
                ) {
                    return 0;
                }

                return strnatcasecmp(
                    remove_accents(
                        $a->name
                    ),
                    remove_accents(
                        $b->name
                    )
                );
            }
        );

        return $terms;
    },
    9999,
    4
);
