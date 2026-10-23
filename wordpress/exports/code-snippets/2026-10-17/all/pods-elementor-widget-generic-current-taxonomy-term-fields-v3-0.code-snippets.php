<?php

/**
 * PODS / Elementor Widget - Generic Current Taxonomy Term Fields [v3.0] 
 */
/**
 * TFSC - Generic Current Taxonomy Term Fields
 *
 * ============================================================
 * DOEL
 * ============================================================
 *
 * Generieke laag voor custom fields op ALLE taxonomy terms.
 *
 * Werkt met:
 *
 * - normale taxonomy term archives
 * - Elementor Taxonomy Loop Grid / Loop Carousel
 * - Pods custom fields op taxonomy terms
 * - iedere taxonomy
 *
 * De taxonomy hoeft nergens handmatig geselecteerd te worden.
 *
 *
 * ============================================================
 * CONTEXT
 * ============================================================
 *
 * Op een normale term archive:
 *
 * get_queried_object()
 *
 * Op een Elementor Taxonomy Loop:
 *
 * $wp_query->loop_term
 *
 * Elementor zet tijdens iedere kaart de huidige term daarin.
 *
 * Voorbeeld:
 *
 * kaart 1 → Field Service Engineer
 * kaart 2 → Commissioning Engineer
 * kaart 3 → Service Engineer
 *
 * Hierdoor kan dezelfde Dynamic Tag per kaart automatisch
 * het Pods field van de juiste term uitlezen.
 *
 *
 * ============================================================
 * SHORTCODE
 * ============================================================
 *
 * Tekst:
 *
 * [tfsc_term_field field="intro_taxonomy"]
 *
 * Image URL:
 *
 * [tfsc_term_field field="archive_featured_image" output="url"]
 *
 *
 * ============================================================
 * ELEMENTOR
 * ============================================================
 *
 * TFSC Current Term Field
 * → voor tekst/custom fields
 *
 * TFSC Current Term Image
 * → voor image widgets/background images
 *
 *
 * Voorbeeld afbeelding:
 *
 * Background
 * → Dynamic
 * → TFSC Current Term Image
 * → archive_featured_image
 *
 *
 * Voorbeeld tekst:
 *
 * Heading/Text
 * → Dynamic
 * → TFSC Current Term Field
 * → intro_taxonomy
 */


/**
 * ============================================================
 * 1. CURRENT TERM CONTEXT
 * ============================================================
 */
function tfsc_current_term() {

    global $wp_query;

    /**
     * Elementor Taxonomy Loop.
     *
     * Elementor zet tijdens iedere loop-iteratie de huidige
     * taxonomy term in $wp_query->loop_term.
     */
    if (
        $wp_query instanceof WP_Query
        && isset( $wp_query->loop_term )
        && $wp_query->loop_term instanceof WP_Term
    ) {
        return $wp_query->loop_term;
    }

    /**
     * Normale taxonomy term archive.
     */
    $object = get_queried_object();

    if ( $object instanceof WP_Term ) {
        return $object;
    }

    return null;
}


/**
 * ============================================================
 * 2. CURRENT TERM FIELD
 * ============================================================
 *
 * Leest een Pods field van de huidige taxonomy-term.
 *
 * Werkt dus automatisch met:
 *
 * beroep_functienaam
 * beroep_kennisgebied
 * beroep_opleiding
 * enz.
 *
 * Fallback naar normale WordPress term-meta.
 */
function tfsc_current_term_field( $field ) {

    $term = tfsc_current_term();

    if ( ! $term || ! $field ) {
        return null;
    }

    $field = sanitize_key( $field );

    if ( function_exists( 'pods' ) ) {

        $pod = pods(
            $term->taxonomy,
            $term->term_id
        );

        if ( $pod && $pod->exists() ) {
            return $pod->field( $field );
        }
    }

    return get_term_meta(
        $term->term_id,
        $field,
        true
    );
}


/**
 * ============================================================
 * 3. IMAGE ATTACHMENT ID
 * ============================================================
 *
 * Zet mogelijke Pods File/Image returnwaarden om naar
 * één WordPress attachment ID.
 */
function tfsc_current_term_image_id( $field ) {

    $value = tfsc_current_term_field( $field );

    if ( empty( $value ) ) {
        return 0;
    }

    if ( is_numeric( $value ) ) {
        return (int) $value;
    }

    if ( is_array( $value ) ) {

        if (
            isset( $value['ID'] )
            || isset( $value['id'] )
            || isset( $value['pod_item_id'] )
        ) {
            return (int) (
                $value['ID']
                ?? $value['id']
                ?? $value['pod_item_id']
            );
        }

        /**
         * Pods kan een file/image field ook als lijst
         * van attachment records retourneren.
         */
        $first = reset( $value );

        if ( is_array( $first ) ) {
            return (int) (
                $first['ID']
                ?? $first['id']
                ?? $first['pod_item_id']
                ?? 0
            );
        }

        if ( is_object( $first ) ) {
            return (int) (
                $first->ID
                ?? $first->id
                ?? $first->pod_item_id
                ?? 0
            );
        }
    }

    if ( is_object( $value ) ) {

        return (int) (
            $value->ID
            ?? $value->id
            ?? $value->pod_item_id
            ?? 0
        );
    }

    return 0;
}


/**
 * ============================================================
 * 4. GENERIEKE SHORTCODE
 * ============================================================
 *
 * Tekst:
 *
 * [tfsc_term_field field="intro_taxonomy"]
 *
 * Afbeelding URL:
 *
 * [tfsc_term_field
 *     field="archive_featured_image"
 *     output="url"
 * ]
 */
add_shortcode(
    'tfsc_term_field',
    function( $atts ) {

        $atts = shortcode_atts(
            [
                'field'  => '',
                'output' => 'text',
                'size'   => 'full',
            ],
            $atts,
            'tfsc_term_field'
        );

        $field = sanitize_key( $atts['field'] );

        if ( ! $field ) {
            return '';
        }

        if ( $atts['output'] === 'url' ) {

            $id = tfsc_current_term_image_id( $field );

            if ( ! $id ) {
                return '';
            }

            $url = wp_get_attachment_image_url(
                $id,
                sanitize_key( $atts['size'] )
            );

            return $url
                ? esc_url( $url )
                : '';
        }

        $value = tfsc_current_term_field( $field );

        if (
            is_array( $value )
            || is_object( $value )
        ) {
            return '';
        }

        return wp_kses_post(
            (string) $value
        );
    }
);


/**
 * ============================================================
 * 5. ELEMENTOR DYNAMIC TAGS
 * ============================================================
 */
add_action(
    'elementor/dynamic_tags/register',
    function( $dynamic_tags ) {

        if (
            ! class_exists(
                '\Elementor\Core\DynamicTags\Data_Tag'
            )
        ) {
            return;
        }


        /**
         * ----------------------------------------------------
         * TFSC CURRENT TERM FIELD
         * ----------------------------------------------------
         *
         * Voor tekstvelden.
         */
        if (
            ! class_exists(
                'TFSC_Current_Term_Field_Tag'
            )
        ) {

            class TFSC_Current_Term_Field_Tag
                extends \Elementor\Core\DynamicTags\Tag {

                public function get_name() {
                    return 'tfsc-current-term-field';
                }

                public function get_title() {
                    return 'TFSC Current Term Field';
                }

                public function get_group() {
                    return 'site';
                }

                public function get_categories() {

                    return [
                        \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY,
                    ];
                }

                protected function register_controls() {

                    $this->add_control(
                        'field',
                        [
                            'label'       => 'Term field',
                            'type'        =>
                                \Elementor\Controls_Manager::TEXT,
                            'placeholder' =>
                                'intro_taxonomy',
                        ]
                    );
                }

                public function render() {

                    $field = sanitize_key(
                        $this->get_settings( 'field' )
                    );

                    if ( ! $field ) {
                        return;
                    }

                    $value =
                        tfsc_current_term_field(
                            $field
                        );

                    if (
                        is_array( $value )
                        || is_object( $value )
                    ) {
                        return;
                    }

                    echo wp_kses_post(
                        (string) $value
                    );
                }
            }

            $dynamic_tags->register(
                new TFSC_Current_Term_Field_Tag()
            );
        }


        /**
         * ----------------------------------------------------
         * TFSC CURRENT TERM IMAGE
         * ----------------------------------------------------
         *
         * Voor:
         *
         * - Image widget
         * - Container background
         * - andere Elementor image controls
         */
        if (
            ! class_exists(
                'TFSC_Current_Term_Image_Tag'
            )
        ) {

            class TFSC_Current_Term_Image_Tag
                extends \Elementor\Core\DynamicTags\Data_Tag {

                public function get_name() {
                    return 'tfsc-current-term-image';
                }

                public function get_title() {
                    return 'TFSC Current Term Image';
                }

                public function get_group() {
                    return 'site';
                }

                public function get_categories() {

                    return [
                        \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY,
                    ];
                }

                protected function register_controls() {

                    $this->add_control(
                        'field',
                        [
                            'label'       => 'Term field',
                            'type'        =>
                                \Elementor\Controls_Manager::TEXT,
                            'default'     =>
                                'archive_featured_image',
                            'placeholder' =>
                                'archive_featured_image',
                        ]
                    );
                }

                public function get_value(
                    array $options = []
                ) {

                    $field = sanitize_key(
                        $this->get_settings( 'field' )
                    );

                    if ( ! $field ) {
                        return [];
                    }

                    $id =
                        tfsc_current_term_image_id(
                            $field
                        );

                    if ( ! $id ) {
                        return [];
                    }

                    $url =
                        wp_get_attachment_image_url(
                            $id,
                            'full'
                        );

                    if ( ! $url ) {
                        return [];
                    }

                    return [
                        'id'  => $id,
                        'url' => $url,
                    ];
                }
            }

            $dynamic_tags->register(
                new TFSC_Current_Term_Image_Tag()
            );
        }
    }
);
