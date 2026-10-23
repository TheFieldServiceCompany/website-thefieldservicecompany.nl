<?php

/**
 * Taxonomy Base - Pods Archief Instellingen Fields [v6] - Generieke snippet voor cpt
 */
/**
 * TFSC - Generic Archive Settings Fields
 *
 * Pods:
 * pods_archief_instellingen
 *
 * TAXONOMY BASE ARCHIVE:
 * archive_{context}_{taxonomy}_{field}
 *
 * CPT ARCHIVE:
 * archive_cpt_{context}_{field}
 *
 * Voorbeelden:
 *
 * archive_beroep_functienaam_intro
 * archive_beroep_functienaam_description
 *
 * archive_cpt_beroep_intro
 * archive_cpt_beroep_description
 *
 * archive_cpt_kennisbank_intro
 * archive_cpt_kennisbank_description
 */


/**
 * ============================================================
 * 1. CONFIG
 * ============================================================
 */

function tfsc_archive_settings_pod_name() {
    return 'pods_archief_instellingen';
}


/**
 * ============================================================
 * 2. CPT → CONTEXT
 * ============================================================
 *
 * Voeg hier alleen uitzonderingen toe waarbij
 * CPT slug en gewenste context niet gelijk zijn.
 */

function tfsc_archive_settings_context_map() {

    return [
        'beroepengids' => 'beroep',

        /*
         * Voorbeeld:
         *
         * Als je echte CPT slug "kennisbankartikelen" is,
         * maar je Pods velden "kennisbank" gebruiken:
         *
         * 'kennisbankartikelen' => 'kennisbank',
         */
    ];
}


/**
 * ============================================================
 * 3. CONTEXT UIT CPT
 * ============================================================
 */

function tfsc_archive_settings_context_from_post_type( $post_type ) {

    $post_type = sanitize_key( $post_type );

    if ( ! $post_type ) {
        return '';
    }

    $map = tfsc_archive_settings_context_map();

    if ( isset( $map[ $post_type ] ) ) {
        return sanitize_key( $map[ $post_type ] );
    }

    return $post_type;
}


/**
 * ============================================================
 * 4. TAXONOMY BASE OPHALEN
 * ============================================================
 *
 * BELANGRIJK:
 *
 * Gebruikt jouw bestaande:
 *
 * tfsc_get_current_taxonomy_archive()
 *
 * Dit is dus NIET hetzelfde als is_tax().
 */

function tfsc_archive_settings_raw_taxonomy() {

    if (
        function_exists(
            'tfsc_get_current_taxonomy_archive'
        )
    ) {

        $taxonomy =
            tfsc_get_current_taxonomy_archive();

        if (
            is_object( $taxonomy ) &&
            ! empty( $taxonomy->name )
        ) {
            $taxonomy = $taxonomy->name;
        }

        if ( $taxonomy ) {
            return sanitize_key( $taxonomy );
        }
    }


    /**
     * Fallback voor normale WordPress term archives.
     */

    if (
        is_tax() ||
        is_category() ||
        is_tag()
    ) {

        $object = get_queried_object();

        if (
            $object &&
            ! empty( $object->taxonomy )
        ) {
            return sanitize_key(
                $object->taxonomy
            );
        }
    }

    return '';
}


/**
 * ============================================================
 * 5. CPT VAN TAXONOMY BEPALEN
 * ============================================================
 */

function tfsc_archive_settings_post_type_from_taxonomy(
    $taxonomy
) {

    $taxonomy = sanitize_key( $taxonomy );

    if ( ! $taxonomy ) {
        return '';
    }


    /**
     * --------------------------------------------------------
     * Eerst prefix gebruiken.
     *
     * Dit is betrouwbaarder dan zomaar het eerste
     * gekoppelde object_type pakken.
     * --------------------------------------------------------
     */

    $context_map =
        tfsc_archive_settings_context_map();


    /**
     * Controleer mappings.
     *
     * beroep_ → beroepengids
     * beroepengids_ → beroepengids
     */

    foreach (
        $context_map as $post_type => $context
    ) {

        $prefixes = [
            $context . '_',
            $context . '-',
            $post_type . '_',
            $post_type . '-',
        ];

        foreach ( $prefixes as $prefix ) {

            if (
                0 === strpos(
                    $taxonomy,
                    $prefix
                )
            ) {
                return sanitize_key(
                    $post_type
                );
            }
        }
    }


    /**
     * --------------------------------------------------------
     * Daarna kijken naar geregistreerde object types.
     * --------------------------------------------------------
     */

    $taxonomy_object =
        get_taxonomy( $taxonomy );

    if (
        ! $taxonomy_object ||
        empty( $taxonomy_object->object_type )
    ) {
        return '';
    }

    $post_types =
        array_values(
            array_filter(
                (array)
                $taxonomy_object->object_type
            )
        );

    if ( empty( $post_types ) ) {
        return '';
    }

    return sanitize_key(
        $post_types[0]
    );
}


/**
 * ============================================================
 * 6. HUIDIGE CPT
 * ============================================================
 */

function tfsc_archive_settings_post_type() {

    /**
     * --------------------------------------------------------
     * CPT ARCHIVE
     * --------------------------------------------------------
     */

    if ( is_post_type_archive() ) {

        $post_type =
            get_query_var( 'post_type' );

        if ( is_array( $post_type ) ) {
            $post_type = reset( $post_type );
        }

        if ( $post_type ) {
            return sanitize_key(
                $post_type
            );
        }


        /**
         * Fallback.
         */

        $object =
            get_queried_object();

        if (
            $object &&
            ! empty( $object->name )
        ) {
            return sanitize_key(
                $object->name
            );
        }
    }


    /**
     * --------------------------------------------------------
     * TAXONOMY BASE / TERM ARCHIVE
     * --------------------------------------------------------
     */

    $taxonomy =
        tfsc_archive_settings_raw_taxonomy();

    if ( $taxonomy ) {

        return
            tfsc_archive_settings_post_type_from_taxonomy(
                $taxonomy
            );
    }


    return '';
}


/**
 * ============================================================
 * 7. CONTEXT
 * ============================================================
 */

function tfsc_archive_settings_context() {

    $post_type =
        tfsc_archive_settings_post_type();

    if ( ! $post_type ) {
        return '';
    }

    return
        tfsc_archive_settings_context_from_post_type(
            $post_type
        );
}


/**
 * ============================================================
 * 8. TAXONOMY NORMALISEREN
 * ============================================================
 *
 * beroep_functienaam
 * → functienaam
 *
 * beroepengids_functienaam
 * → functienaam
 *
 * vacature_bedrijfssector
 * → bedrijfssector
 */

function tfsc_archive_settings_normalize_taxonomy(
    $taxonomy
) {

    $taxonomy =
        sanitize_key( $taxonomy );

    if ( ! $taxonomy ) {
        return '';
    }

    $post_type =
        tfsc_archive_settings_post_type();

    $context =
        tfsc_archive_settings_context();

    $prefixes = [];


    if ( $post_type ) {

        $prefixes[] =
            $post_type . '_';

        $prefixes[] =
            $post_type . '-';
    }


    if ( $context ) {

        $prefixes[] =
            $context . '_';

        $prefixes[] =
            $context . '-';
    }


    $prefixes =
        array_unique( $prefixes );


    usort(
        $prefixes,
        function( $a, $b ) {

            return
                strlen( $b )
                <=>
                strlen( $a );
        }
    );


    foreach ( $prefixes as $prefix ) {

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
 * 9. HUIDIGE GENORMALISEERDE TAXONOMY
 * ============================================================
 */

function tfsc_archive_settings_taxonomy() {

    $taxonomy =
        tfsc_archive_settings_raw_taxonomy();

    if ( ! $taxonomy ) {
        return '';
    }

    return
        tfsc_archive_settings_normalize_taxonomy(
            $taxonomy
        );
}


/**
 * ============================================================
 * 10. PODS VELDNAAM
 * ============================================================
 */

function tfsc_archive_settings_field_name(
    $field
) {

    $field =
        sanitize_key( $field );

    if ( ! $field ) {
        return '';
    }


    $context =
        tfsc_archive_settings_context();

    if ( ! $context ) {
        return '';
    }


    /**
     * --------------------------------------------------------
     * TAXONOMY BASE / TERM ARCHIVE
     * --------------------------------------------------------
     */

    $taxonomy =
        tfsc_archive_settings_taxonomy();

    if ( $taxonomy ) {

        return sanitize_key(
            'archive_'
            . $context
            . '_'
            . $taxonomy
            . '_'
            . $field
        );
    }


    /**
     * --------------------------------------------------------
     * CPT ARCHIVE
     * --------------------------------------------------------
     */

    if ( is_post_type_archive() ) {

        return sanitize_key(
            'archive_cpt_'
            . $context
            . '_'
            . $field
        );
    }


    return '';
}


/**
 * ============================================================
 * 11. PODS VALUE
 * ============================================================
 */

function tfsc_archive_settings_value(
    $field
) {

    if ( ! function_exists( 'pods' ) ) {
        return '';
    }


    $field_name =
        tfsc_archive_settings_field_name(
            $field
        );

    if ( ! $field_name ) {
        return '';
    }


    $pod =
        pods(
            tfsc_archive_settings_pod_name()
        );

    if ( ! $pod ) {
        return '';
    }


    $value =
        $pod->field(
            $field_name
        );


    if (
        null === $value ||
        false === $value
    ) {
        return '';
    }


    if (
        ! is_array( $value ) &&
        ! is_object( $value )
    ) {

        $value =
            do_shortcode(
                wpautop(
                    (string)
                    $value
                )
            );

        $value =
            wp_kses_post(
                $value
            );
    }


    return $value;
}


/**
 * ============================================================
 * 12. SHORTCODE
 * ============================================================
 *
 * [tfsc_archive_field field="intro"]
 *
 * [tfsc_archive_field field="description"]
 */

add_shortcode(
    'tfsc_archive_field',
    function( $atts ) {

        $atts =
            shortcode_atts(
                [
                    'field' => '',
                ],
                $atts,
                'tfsc_archive_field'
            );


        $value =
            tfsc_archive_settings_value(
                $atts['field']
            );


        if (
            is_array( $value ) ||
            is_object( $value )
        ) {
            return '';
        }


        return (string) $value;
    }
);


/**
 * ============================================================
 * 13. ELEMENTOR DYNAMIC TAG
 * ============================================================
 */

add_action(
    'elementor/dynamic_tags/register',
    function( $dynamic_tags ) {

        if (
            ! class_exists(
                '\Elementor\Core\DynamicTags\Tag'
            )
        ) {
            return;
        }


        class TFSC_Archive_Settings_Field_Tag
            extends
            \Elementor\Core\DynamicTags\Tag {

            public function get_name() {

                return
                    'tfsc-archive-settings-field';
            }


            public function get_title() {

                return
                    'TFSC Archive Settings Field';
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
                        'label' =>
                            'Field',

                        'type' =>
                            \Elementor\Controls_Manager::TEXT,

                        'placeholder' =>
                            'intro',
                    ]
                );
            }


            public function render() {

                $field =
                    $this->get_settings(
                        'field'
                    );


                if ( ! $field ) {
                    return;
                }


                $value =
                    tfsc_archive_settings_value(
                        $field
                    );


                if (
                    is_array( $value ) ||
                    is_object( $value )
                ) {
                    return;
                }


                echo (string) $value;
            }
        }


        $dynamic_tags->register(
            new TFSC_Archive_Settings_Field_Tag()
        );
    }
);
