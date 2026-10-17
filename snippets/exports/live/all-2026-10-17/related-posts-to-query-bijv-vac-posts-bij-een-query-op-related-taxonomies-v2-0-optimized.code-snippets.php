<?php

/**
 * Related Posts to Query - bijv Vac Posts bij een Query op related Taxonomies. [v2.0] - Optimized
 *
 * query id: related_posts
 */
/**
 * TFSC - Elementor Posts filteren via Pods taxonomy relationships
 *
 * Query ID:
 * relationship_posts
 *
 * Doel:
 * Filtert een Elementor Posts/Loop Grid automatisch op basis van de
 * Pods relationshipvelden van de huidige post, CPT of taxonomy-term.
 *
 * Belangrijk:
 * - RAW Pods relationshipdata is leidend.
 * - Verschillende taxonomies = AND.
 * - Meerdere terms binnen dezelfde taxonomy = IN / OR.
 * - Elementor Source blijft leidend.
 * - relationship_vacature_button_url wordt bewust genegeerd.
 * - Werkt ook in Elementor Theme Builder preview voor posts/CPT's.
 */


/**
 * ============================================================
 * CONTEXT + POD
 * ============================================================
 *
 * Bepaalt het Pods-object van de huidige context:
 *
 * - taxonomy archive → Pod van de huidige term;
 * - post / CPT → Pod van de huidige post;
 * - Elementor Theme Builder → gebruikt indien beschikbaar
 *   tfsc_derived_rel_v2_get_context_post_id() om de echte
 *   preview-post te vinden.
 */

function tfsc_relposts_v3_get_pod() {

    if ( ! function_exists( 'pods' ) ) {
        return false;
    }

    $object = get_queried_object();

    /*
     * Taxonomy term.
     */
    if ( $object instanceof WP_Term ) {

        $pod = pods(
            $object->taxonomy,
            (int) $object->term_id
        );

        return (
            $pod &&
            $pod->exists()
        )
            ? $pod
            : false;
    }

    /*
     * Post / CPT / Elementor preview.
     */
    $post_id = 0;

    if (
        function_exists(
            'tfsc_derived_rel_v2_get_context_post_id'
        )
    ) {
        $post_id =
            (int)
            tfsc_derived_rel_v2_get_context_post_id();
    }

    if (
        ! $post_id &&
        $object instanceof WP_Post &&
        'elementor_library'
        !==
        get_post_type( $object->ID )
    ) {
        $post_id =
            (int) $object->ID;
    }

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

    return (
        $pod &&
        $pod->exists()
    )
        ? $pod
        : false;
}


/**
 * ============================================================
 * RAW RELATIONSHIP NORMALISEREN
 * ============================================================
 *
 * Pods kan relationshipdata als array of object teruggeven.
 * Deze functie maakt daar steeds dit formaat van:
 *
 * [
 *     [
 *         'taxonomy' => 'functie-inhoud',
 *         'term_id'  => 220,
 *     ],
 * ]
 *
 * De taxonomy wordt dus NIET afgeleid uit de fieldnaam.
 * De daadwerkelijke RAW relationshipdata bepaalt welke taxonomy
 * en term gebruikt worden.
 */

function tfsc_relposts_v3_normalize_relationship(
    $value
) {

    if ( empty( $value ) ) {
        return [];
    }

    /*
     * Eén relationship normaliseren naar een lijst.
     */
    if (
        is_object( $value ) ||
        (
            is_array( $value ) &&
            (
                isset( $value['term_id'] ) ||
                isset( $value['taxonomy'] )
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

        if ( is_array( $relation ) ) {

            $taxonomy =
                sanitize_key(
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

            $taxonomy =
                sanitize_key(
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

        /*
         * Alleen echte, bestaande taxonomy-terms accepteren.
         */
        if (
            ! $taxonomy ||
            ! $term_id ||
            ! taxonomy_exists( $taxonomy )
        ) {
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
 * TAXONOMY HOORT BIJ ELEMENTOR SOURCE?
 * ============================================================
 *
 * Een relationship wordt alleen gebruikt als de betreffende
 * taxonomy gekoppeld is aan het post type dat Elementor als
 * Source gebruikt.
 *
 * Voorbeeld:
 *
 * Elementor Source = vacature
 * relationship → functie-inhoud
 *
 * Dan wordt die relationship alleen gebruikt wanneer
 * functie-inhoud daadwerkelijk aan vacature gekoppeld is.
 */

function tfsc_relposts_v3_taxonomy_matches_source(
    $taxonomy,
    $post_types
) {

    $object =
        get_taxonomy(
            $taxonomy
        );

    if ( ! $object ) {
        return false;
    }

    return (bool) array_intersect(
        (array) $post_types,
        (array) $object->object_type
    );
}


/**
 * ============================================================
 * ELEMENTOR QUERY
 * ============================================================
 *
 * Query ID in Elementor:
 *
 * relationship_posts
 *
 * Werking:
 *
 * relationship_functienaam
 * → Field Service Engineer
 *
 * relationship_functie-inhoud
 * → Service
 * → Inbedrijfstellen
 *
 * wordt:
 *
 * functienaam = Field Service Engineer
 *
 * AND
 *
 * functie-inhoud IN (Service, Inbedrijfstellen)
 *
 * Bestaande Elementor tax_query blijft behouden en wordt met
 * deze relationship-query gecombineerd via AND.
 */

add_action(
    'elementor/query/relationship_posts',
    function( $query ) {

        $pod =
            tfsc_relposts_v3_get_pod();

        if ( ! $pod ) {
            $query->set( 'post__in', [ 0 ] );
            return;
        }

        /*
         * Elementor Source.
         */
        $post_types =
            array_values(
                array_filter(
                    (array) $query->get( 'post_type' )
                )
            );

        if ( ! $post_types ) {
            $query->set( 'post__in', [ 0 ] );
            return;
        }

        $fields =
            $pod->fields();

        if ( ! is_array( $fields ) || ! $fields ) {
            $query->set( 'post__in', [ 0 ] );
            return;
        }

        /*
         * Relationship terms groeperen per taxonomy.
         *
         * Bijvoorbeeld:
         *
         * [
         *     'functienaam'    => [100],
         *     'functie-inhoud' => [220, 221],
         * ]
         */
        $groups = [];

        foreach ( $fields as $field_name => $definition ) {

            /*
             * Alleen relationship_* fields.
             *
             * relationship_vacature_button_url wordt uitgesloten,
             * omdat dat field alleen de vacaturebutton bepaalt.
             */
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

            /*
             * RAW Pods relationship uitlezen.
             *
             * Bewust geen traversal zoals:
             * $pod->field( $field_name . '.term_id' )
             */
            $relationships =
                tfsc_relposts_v3_normalize_relationship(
                    $pod->field(
                        $field_name
                    )
                );

            foreach (
                $relationships
                as $relationship
            ) {

                $taxonomy =
                    $relationship['taxonomy'];

                /*
                 * Alleen gebruiken als taxonomy bij de
                 * Elementor Source hoort.
                 */
                if (
                    ! tfsc_relposts_v3_taxonomy_matches_source(
                        $taxonomy,
                        $post_types
                    )
                ) {
                    continue;
                }

                $groups[ $taxonomy ][] =
                    (int) $relationship['term_id'];
            }
        }

        /*
         * Dubbele en ongeldige IDs verwijderen.
         */
        foreach (
            $groups
            as $taxonomy => $ids
        ) {

            $ids =
                array_values(
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
                $groups[ $taxonomy ] = $ids;
            } else {
                unset( $groups[ $taxonomy ] );
            }
        }

        /*
         * Geen bruikbare relationships:
         * bewust geen posts tonen.
         */
        if ( ! $groups ) {
            $query->set( 'post__in', [ 0 ] );
            return;
        }

        /*
         * Verschillende taxonomies = AND.
         * Meerdere terms binnen taxonomy = IN.
         */
        $relationship_query = [
            'relation' => 'AND',
        ];

        foreach (
            $groups
            as $taxonomy => $ids
        ) {

            $relationship_query[] = [
                'taxonomy'         => $taxonomy,
                'field'            => 'term_id',
                'terms'            => $ids,
                'operator'         => 'IN',
                'include_children' => true,
            ];
        }

        /*
         * Bestaande Elementor tax_query bewaren.
         */
        $query->set(
    'tax_query',
    $relationship_query
);
    },
    20,
    1
);
