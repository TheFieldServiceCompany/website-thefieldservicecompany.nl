<?php

/**
 * CPT post term buttons - Shows active terms on a Post
 *
 * Active on BeroepengidsShortcode [tfsc_post_terms]
 */
/**
 * TFSC - Post Terms Buttons
 *
 * ============================================================
 * DOEL
 * ============================================================
 *
 * Toont de taxonomy terms die aan de huidige post / CPT
 * gekoppeld zijn als klikbare buttons.
 *
 * Werkt ook in Elementor Theme Builder previews wanneer
 * tfsc_derived_rel_v2_get_context_post_id() beschikbaar is.
 *
 *
 * ============================================================
 * SHORTCODE
 * ============================================================
 *
 * Alle publieke taxonomies met gekoppelde terms:
 *
 * [tfsc_post_terms]
 *
 * Eén taxonomy:
 *
 * [tfsc_post_terms taxonomy="kennisgebied"]
 *
 * Meerdere taxonomies:
 *
 * [tfsc_post_terms taxonomy="functienaam,kennisgebied,locatie"]
 *
 *
 * ============================================================
 * LINKS
 * ============================================================
 *
 * Iedere term linkt naar zijn normale native taxonomy URL.
 *
 * De TFSC combo-link omzetting wordt bewust tijdelijk
 * uitgeschakeld via:
 *
 * tfsc_force_native_term_link
 *
 *
 * ============================================================
 * OUTPUT
 * ============================================================
 *
 * .tfsc-post-terms
 * .tfsc-post-terms__item
 * .tfsc-post-terms__item--{taxonomy}
 */
/**
 * ============================================================
 * CSS
 * ============================================================
 *
 * Voeg onderstaande CSS toe in Elementor Custom CSS,
 * Site Settings of je eigen stylesheet:
 *
 *
 * .tfsc-post-terms {
 *     display: flex;
 *     flex-wrap: wrap;
 *     gap: 8px;
 * }
 *
 * .tfsc-post-terms__item {
 *     display: inline-flex;
 *     align-items: center;
 *     padding: 8px 14px;
 *     border: 1px solid currentColor;
 *     border-radius: 999px;
 *     text-decoration: none;
 * }
 *
 *
 * ============================================================
 * TAXONOMY-SPECIFIEKE STYLING
 * ============================================================
 *
 * Iedere button krijgt ook een class met de taxonomy name.
 *
 * Bijvoorbeeld:
 *
 * .tfsc-post-terms__item--kennisgebied
 * .tfsc-post-terms__item--functienaam
 * .tfsc-post-terms__item--locatie
 *
 * Daardoor kun je taxonomies afzonderlijk stylen.
 *
 * Voorbeeld:
 *
 * .tfsc-post-terms__item--kennisgebied {
 *     font-weight: 600;
 * }
 *
 *
 * ============================================================
 * HOVER
 * ============================================================
 *
 * Eventueel:
 *
 * .tfsc-post-terms__item:hover {
 *     text-decoration: none;
 * }
 */

add_shortcode(
    'tfsc_post_terms',
    function( $atts ) {

        $atts = shortcode_atts(
            [
                'taxonomy' => '',
            ],
            $atts,
            'tfsc_post_terms'
        );


        /**
         * ====================================================
         * HUIDIGE POST / ELEMENTOR PREVIEW
         * ====================================================
         */

        $post_id =
            function_exists(
                'tfsc_derived_rel_v2_get_context_post_id'
            )
                ? (int) tfsc_derived_rel_v2_get_context_post_id()
                : 0;

        if ( ! $post_id ) {

            $object =
                get_queried_object();

            $post_id =
                $object instanceof WP_Post
                    ? (int) $object->ID
                    : (int) get_the_ID();
        }

        if ( ! $post_id ) {
            return '';
        }

        $post_type =
            get_post_type(
                $post_id
            );

        if ( ! $post_type ) {
            return '';
        }


        /**
         * ====================================================
         * TAXONOMIES BEPALEN
         * ====================================================
         *
         * Zonder taxonomy="" worden automatisch alle publieke
         * taxonomies van het huidige post type gebruikt.
         */

        $taxonomies =
            $atts['taxonomy']
                ? array_filter(
                    array_map(
                        'sanitize_key',
                        preg_split(
                            '/\s*,\s*/',
                            $atts['taxonomy']
                        )
                    )
                )
                : get_object_taxonomies(
                    $post_type,
                    'names'
                );

        $taxonomies =
            array_filter(
                $taxonomies,
                function( $taxonomy ) use ( $post_type ) {

                    $object =
                        get_taxonomy(
                            $taxonomy
                        );

                    return (
                        $object &&
                        ! empty( $object->public ) &&
                        in_array(
                            $post_type,
                            (array) $object->object_type,
                            true
                        )
                    );
                }
            );

        if ( ! $taxonomies ) {
            return '';
        }


        /**
         * ====================================================
         * TERMS + BUTTONS
         * ====================================================
         *
         * Alleen terms die daadwerkelijk aan de huidige
         * post gekoppeld zijn worden weergegeven.
         */

        $buttons = [];

        foreach ( $taxonomies as $taxonomy ) {

            $terms =
                wp_get_post_terms(
                    $post_id,
                    $taxonomy
                );

            if (
                is_wp_error( $terms ) ||
                ! $terms
            ) {
                continue;
            }

            foreach ( $terms as $term ) {

                if ( ! $term instanceof WP_Term ) {
                    continue;
                }

                /**
                 * Native term URL forceren.
                 * Hierdoor maken andere TFSC term_link filters
                 * hier geen vacature-combo URL van.
                 */
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

                if ( is_wp_error( $url ) ) {
                    continue;
                }

                $buttons[] =
                    sprintf(
                        '<a class="tfsc-post-terms__item tfsc-post-terms__item--%1$s" href="%2$s">%3$s</a>',
                        esc_attr(
                            sanitize_html_class(
                                $taxonomy
                            )
                        ),
                        esc_url( $url ),
                        esc_html( $term->name )
                    );
            }
        }

        if ( ! $buttons ) {
            return '';
        }

        return
            '<div class="tfsc-post-terms">'
            . implode( '', $buttons )
            . '</div>';
    }
);
