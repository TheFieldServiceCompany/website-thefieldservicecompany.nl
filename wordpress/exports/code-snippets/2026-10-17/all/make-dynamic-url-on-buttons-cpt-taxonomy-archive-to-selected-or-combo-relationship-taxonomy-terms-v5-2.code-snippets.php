<?php

/**
 * Make dynamic url on buttons | CPT/Taxonomy archive to Selected or Combo Relationship Taxonomy Terms [V5.2]
 *
 * Beroepen: Maak een url naar vacatures die overeenkomen met de functienaam. 
 */
/**
 * TFSC - Vacature Button URL + Text
 *
 * Shortcodes:
 *
 * [vacature_button_url]
 * [vacature_button_text]
 *
 * ============================================================
 * WERKING
 * ============================================================
 *
 * 1. relationship_vacature_button_url gevuld?
 *
 *    → gebruik native taxonomy-term URL
 *    → buttontekst = naam van die term
 *
 * 2. relationship_vacature_button_url leeg?
 *
 *    CPT / post:
 *    → tfsc_derived_rel_v3_relationship_groups()
 *
 *    Taxonomy / term:
 *    → tfsc_taxrel_v4_relationship_groups()
 *
 *    → bouw één TFSC combo archive URL
 *    → buttontekst = "Bekijk vacatures"
 */


/**
 * ============================================================
 * 1. HUIDIGE POD
 * ============================================================
 */

function tfsc_vacature_button_v4_pod() {

    if ( ! function_exists( 'pods' ) ) {
        return false;
    }

    $object = get_queried_object();

    /**
     * Taxonomy-term.
     *
     * Gebruik indien mogelijk dezelfde resolver als
     * de taxonomy relationship-loop.
     */
    if (
        function_exists( 'tfsc_taxrel_v4_current_term' )
    ) {
        $term = tfsc_taxrel_v4_current_term();

        if ( $term instanceof WP_Term ) {

            $pod = pods(
                $term->taxonomy,
                (int) $term->term_id
            );

            return (
                $pod &&
                $pod->exists()
            )
                ? $pod
                : false;
        }
    }

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

    /**
     * CPT / post / Elementor preview.
     */
    if (
        function_exists(
            'tfsc_derived_rel_v3_get_context_post_id'
        )
    ) {
        $post_id = (int)
            tfsc_derived_rel_v3_get_context_post_id();

    } elseif (
        function_exists(
            'tfsc_derived_rel_v2_get_context_post_id'
        )
    ) {
        $post_id = (int)
            tfsc_derived_rel_v2_get_context_post_id();

    } elseif (
        $object instanceof WP_Post
    ) {
        $post_id = (int) $object->ID;

    } else {
        $post_id =
            (int) get_queried_object_id();
    }

    if ( ! $post_id ) {
        return false;
    }

    $post_type =
        get_post_type(
            $post_id
        );

    if (
        ! $post_type ||
        'elementor_library' === $post_type
    ) {
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
 * 2. EXPLICIETE BUTTON TERM
 * ============================================================
 *
 * relationship_vacature_button_url
 */

function tfsc_vacature_button_v4_explicit_term() {

    $pod =
        tfsc_vacature_button_v4_pod();

    if ( ! $pod ) {
        return false;
    }

    $value =
        $pod->field(
            'relationship_vacature_button_url'
        );

    if ( empty( $value ) ) {
        return false;
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

            $taxonomy =
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

            $taxonomy =
                sanitize_key(
                    $relation->taxonomy
                    ?? ''
                );
        }

        if (
            ! $term_id ||
            ! $taxonomy ||
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
            return $term;
        }
    }

    return false;
}


/**
 * ============================================================
 * 3. ACTIEVE RELATIONSHIP GROUPS
 * ============================================================
 *
 * Taxonomy-term:
 * → tfsc_taxrel_v4_relationship_groups()
 *
 * CPT / post:
 * → tfsc_derived_rel_v3_relationship_groups()
 */

function tfsc_vacature_button_v4_relationship_groups() {

    $object =
        get_queried_object();

    /**
     * Taxonomy context.
     */
    if (
        $object instanceof WP_Term ||
        (
            function_exists(
                'tfsc_taxrel_v4_current_term'
            ) &&
            tfsc_taxrel_v4_current_term()
            instanceof WP_Term
        )
    ) {

        return function_exists(
            'tfsc_taxrel_v4_relationship_groups'
        )
            ? tfsc_taxrel_v4_relationship_groups()
            : [];
    }

    /**
     * CPT / post context.
     */
    return function_exists(
        'tfsc_derived_rel_v3_relationship_groups'
    )
        ? tfsc_derived_rel_v3_relationship_groups()
        : [];
}


/**
 * ============================================================
 * 4. TERM PATH
 * ============================================================
 */

function tfsc_vacature_button_v4_term_path(
    $term
) {

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

    if ( ! $term instanceof WP_Term ) {
        return '';
    }

    return rawurlencode(
        $term->slug
    );
}


/**
 * ============================================================
 * 5. COMBO BASE
 * ============================================================
 */

function tfsc_vacature_button_v4_combo_base() {

    if ( defined( 'TFSC_COMBO_BASE' ) ) {
        return TFSC_COMBO_BASE;
    }

    return 'vacatures';
}


/**
 * ============================================================
 * 6. COMBO URL
 * ============================================================
 */

function tfsc_vacature_button_v4_combo_url() {

    $groups =
        tfsc_vacature_button_v4_relationship_groups();

    if ( ! $groups ) {
        return '';
    }

    $parts = [
        tfsc_vacature_button_v4_combo_base(),
    ];

    foreach (
        $groups
        as $taxonomy => $term_ids
    ) {

        foreach (
            $term_ids
            as $term_id
        ) {

            $term =
                get_term(
                    (int) $term_id,
                    $taxonomy
                );

            if (
                ! $term instanceof WP_Term ||
                is_wp_error( $term )
            ) {
                continue;
            }

            $path =
                tfsc_vacature_button_v4_term_path(
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

    if ( count( $parts ) <= 1 ) {
        return '';
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
 * 7. BUTTON URL
 * ============================================================
 *
 * [vacature_button_url]
 */

add_shortcode(
    'vacature_button_url',
    function() {

        /**
         * Expliciete selectie heeft altijd voorrang.
         */
        $term =
            tfsc_vacature_button_v4_explicit_term();

        if ( $term ) {

            $GLOBALS[
                'tfsc_force_native_term_link'
            ] = true;

            $url =
                get_term_link(
                    $term
                );

            unset(
                $GLOBALS[
                    'tfsc_force_native_term_link'
                ]
            );

            return is_wp_error( $url )
                ? ''
                : esc_url( $url );
        }

        /**
         * Geen expliciete selectie:
         * combo van alle actieve relationships.
         */
        $url =
            tfsc_vacature_button_v4_combo_url();

        return $url
            ? esc_url( $url )
            : '';
    }
);


/**
 * ============================================================
 * 8. BUTTON TEXT
 * ============================================================
 *
 * [vacature_button_text]
 *
 * Expliciete term:
 * → naam van de term
 *
 * Combo:
 * → Bekijk vacatures
 */

add_shortcode(
    'vacature_button_text',
    function() {

        $term =
            tfsc_vacature_button_v4_explicit_term();

        if ( $term ) {
            return esc_html(
                $term->name
            );
        }

        return tfsc_vacature_button_v4_relationship_groups()
            ? 'Bekijk vacatures'
            : '';
    }
);
