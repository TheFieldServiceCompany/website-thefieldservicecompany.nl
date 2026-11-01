<?php
/**
 * v3.3: vacatureblokken vallen bij nul matches terug op de oorspronkelijke Elementor-selectie.
 * Ontbrekende Pods-relaties gebruiken dezelfde terugval; andere Sources blijven ongewijzigd.
 * v3.2: termarchief-vacatures delen de actieve terugval met de vacatureknop.
 * Geen match: open vacatures; overige Sources en Elementor-filters blijven intact.
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
 *   tfsc_derived_rel_v3_get_context_post_id() om de echte
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
            'tfsc_derived_rel_v3_get_context_post_id'
        )
    ) {
        $post_id =
            (int)
            tfsc_derived_rel_v3_get_context_post_id();

    } elseif (
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
 */

/**
 * Pas relaties toe, tenzij de vacatureselectie daardoor helemaal leeg wordt.
 * Controleer de eerste pagina: een te hoog paginanummer is geen nul-match.
 * Alle oorspronkelijke Elementor-voorwaarden blijven in beide gevallen staan.
 */
function tfsc_relposts_v3_set_relationship_query( $query, $tax_query, $vacancy_source ) {
    if ( $vacancy_source ) {
        $args = $query->query_vars;
        $args['tax_query'] = $tax_query;
        $args['posts_per_page'] = 1;
        $args['paged'] = 1;
        $args['page'] = 1;
        $args['offset'] = 0;
        $args['fields'] = 'ids';
        $args['no_found_rows'] = true;
        $args['_tfsc_relposts_probe'] = true;
        $probe = new WP_Query( $args );
        if ( ! $probe->have_posts() ) {
            return;
        }
    }
    $query->set( 'tax_query', $tax_query );
}

add_action(
    'elementor/query/relationship_posts',
    function( $query ) {

        // Elementor kan de custom-queryhook ook voor de controlequery aanroepen.
        if ( $query->get( '_tfsc_relposts_probe' ) ) {
            return;
        }

        $vacancy_source = array_values( (array) $query->get( 'post_type' ) ) === [ 'vacature' ];

        // Termarchief + uitsluitend vacature-source: dezelfde selectie als de knop.
        // Bestaande Elementor-filters blijven als extra AND-groep behouden.
        if ( get_queried_object() instanceof WP_Term &&
             array_values((array) $query->get('post_type')) === ['vacature'] &&
             function_exists('tfsc_taxrel_v4_effective_relationship_groups') ) {
            $groups = tfsc_taxrel_v4_effective_relationship_groups();
            $status = tfsc_taxrel_v4_status_taxonomy();
            if ( ! taxonomy_exists($status) ) { $query->set('post__in', [0]); return; }
            $selection = ['relation' => 'AND', [
                'taxonomy' => $status, 'field' => 'slug',
                'terms' => [tfsc_taxrel_v4_open_status()], 'include_children' => false,
            ]];
            foreach ( $groups as $taxonomy => $ids ) {
                $method = function_exists('tfsc_get_combo_taxonomy_match_method')
                    ? tfsc_get_combo_taxonomy_match_method($taxonomy)
                    : ('kennisgebied' === $taxonomy ? 'AND' : 'OR');
                $selection[] = ['taxonomy' => $taxonomy, 'field' => 'term_id',
                    'terms' => $ids, 'include_children' => false,
                    'operator' => 'AND' === $method && count($ids) > 1 ? 'AND' : 'IN'];
            }
            $existing = $query->get('tax_query');
            $query->set('tax_query', $existing ? ['relation' => 'AND', $existing, $selection] : $selection);
            return;
        }

        $pod =
            tfsc_relposts_v3_get_pod();

        if ( ! $pod ) {
            if ( ! $vacancy_source ) {
                $query->set( 'post__in', [ 0 ] );
            }
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
            if ( ! $vacancy_source ) {
                $query->set( 'post__in', [ 0 ] );
            }
            return;
        }

        $fields =
            $pod->fields();

        if ( ! is_array( $fields ) || ! $fields ) {
            if ( ! $vacancy_source ) {
                $query->set( 'post__in', [ 0 ] );
            }
            return;
        }

        /*
         * Relationship terms groeperen per taxonomy.
         */
        $groups = [];

        foreach ( $fields as $field_name => $definition ) {

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
         * vacature-source behoudt de algemene Elementor-selectie.
         * Andere Sources tonen bewust geen posts.
         */
        if ( ! $groups ) {
            if ( ! $vacancy_source ) {
                $query->set( 'post__in', [ 0 ] );
            }
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
         * Elementor kan al tax_query-voorwaarden bevatten, bijvoorbeeld
         * vac_status = Ingevuld uitsluiten. Die mogen niet worden vervangen.
         * De relationshipvoorwaarden worden daarom als extra AND-groep
         * naast de bestaande Elementor-query geplaatst.
         */
        $existing_tax_query = $query->get( 'tax_query' );

        if (
            is_array( $existing_tax_query ) &&
            ! empty( $existing_tax_query )
        ) {
            $combined_tax_query = [
                'relation' => 'AND',
                $existing_tax_query,
                $relationship_query,
            ];
        } else {
            $combined_tax_query = $relationship_query;
        }

        tfsc_relposts_v3_set_relationship_query(
            $query,
            $combined_tax_query,
            $vacancy_source
        );
    },
    20,
    1
);