<?php

/**
 * Taxonomy Base - Pods Archief Instellingen Fields [v5.2]
 */
/**
 * TFSC - Archive Settings Fields
 *
 * Leest generieke velden uit:
 *
 * pods_archief_instellingen
 *
 * ============================================================
 * TAXONOMY BASE ARCHIVE
 * ============================================================
 *
 * archive_{context}_{taxonomy}_{field}
 *
 * Voorbeeld:
 *
 * beroep_functienaam
 * + context beroep
 * + field description
 *
 * → archive_beroep_functienaam_description
 *
 *
 * ============================================================
 * CPT ARCHIVE
 * ============================================================
 *
 * archive_cpt_{context}_{field}
 *
 * Voorbeeld:
 *
 * CPT archive beroepengids
 * + context beroep
 * + field description
 *
 * → archive_cpt_beroep_description
 *
 *
 * ELEMENTOR
 * ============================================================
 *
 * Dynamic Tag:
 * TFSC Archive Settings Field
 *
 * Context:
 * beroep
 *
 * Field:
 * description
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
 * 2. TAXONOMY NORMALISEREN
 * ============================================================
 *
 * beroep_functienaam
 * → functienaam
 */

function tfsc_archive_settings_normalize_taxonomy(
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
 * 3. HUIDIGE TAXONOMY BASE
 * ============================================================
 */

function tfsc_archive_settings_taxonomy() {

    if (
        ! function_exists(
            'tfsc_get_current_taxonomy_archive'
        )
    ) {
        return '';
    }

    $taxonomy =
        tfsc_get_current_taxonomy_archive();

    if (
        is_object( $taxonomy ) &&
        ! empty( $taxonomy->name )
    ) {
        $taxonomy =
            $taxonomy->name;
    }

    if ( ! $taxonomy ) {
        return '';
    }

    return tfsc_archive_settings_normalize_taxonomy(
        $taxonomy
    );
}


/**
 * ============================================================
 * 4. CPT ARCHIVE CONTEXT
 * ============================================================
 *
 * Geeft alleen true terug op een echt post type archive.
 */

function tfsc_archive_settings_is_cpt_archive() {

    return is_post_type_archive();
}


/**
 * ============================================================
 * 5. VELDNAAM OPBOUWEN
 * ============================================================
 *
 * Taxonomy base:
 * archive_{context}_{taxonomy}_{field}
 *
 * CPT archive:
 * archive_cpt_{context}_{field}
 */

function tfsc_archive_settings_field_name(
    $context,
    $field
) {

    $context =
        sanitize_key(
            $context
        );

    $field =
        sanitize_key(
            $field
        );

    if (
        ! $context ||
        ! $field
    ) {
        return '';
    }

    /**
     * Taxonomy base archive heeft voorrang.
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
     * CPT archive.
     */
    if (
        tfsc_archive_settings_is_cpt_archive()
    ) {

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
 * 6. PODS SETTINGS VALUE
 * ============================================================
 */

function tfsc_archive_settings_value(
    $context,
    $field
) {

    if ( ! function_exists( 'pods' ) ) {
        return '';
    }

    $field_name =
        tfsc_archive_settings_field_name(
            $context,
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

    /**
     * Alleen tekstuele waarden formatteren.
     *
     * wpautop:
     * Zet lege regels om naar <p> en enkele regeleinden waar
     * nodig naar <br>.
     *
     * do_shortcode:
     * Ondersteunt shortcodes in WYSIWYG velden.
     *
     * wp_kses_post:
     * Laat veilige WordPress HTML door.
     */
    if (
        ! is_array( $value ) &&
        ! is_object( $value )
    ) {

        $value =
            do_shortcode(
                wpautop(
                    (string) $value
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
 * 7. SHORTCODE
 * ============================================================
 *
 * [tfsc_archive_field
 *     context="beroep"
 *     field="description"]
 */

add_shortcode(
    'tfsc_archive_field',
    function( $atts ) {

        $atts = shortcode_atts(
            [
                'context' => 'beroep',
                'field'   => '',
            ],
            $atts,
            'tfsc_archive_field'
        );

        $value =
            tfsc_archive_settings_value(
                $atts['context'],
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
 * 8. ELEMENTOR DYNAMIC TAG
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
            extends \Elementor\Core\DynamicTags\Tag {

            public function get_name() {
                return 'tfsc-archive-settings-field';
            }

            public function get_title() {
                return 'TFSC Archive Settings Field';
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
                    'context',
                    [
                        'label'   => 'Context',
                        'type'    =>
                            \Elementor\Controls_Manager::TEXT,
                        'default' => 'beroep',
                    ]
                );

                $this->add_control(
                    'field',
                    [
                        'label' =>
                            'Field',

                        'type' =>
                            \Elementor\Controls_Manager::TEXT,

                        'placeholder' =>
                            'description',
                    ]
                );
            }

            public function render() {

                $value =
                    tfsc_archive_settings_value(
                        $this->get_settings(
                            'context'
                        ),
                        $this->get_settings(
                            'field'
                        )
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
