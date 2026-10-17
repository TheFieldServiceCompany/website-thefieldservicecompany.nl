<?php

/**
 * Taxonomy Base - Pods Archief Instellingen Fields [v6.2] - Generieke snippet voor cpt [CLONE]
 */
/**
 * ============================================================
 * TFSC - GENERIC ARCHIVE SETTINGS FIELDS
 * ============================================================
 *
 * Doel
 * ----
 * Deze snippet leest centrale archivevelden uit de Pods Settings Pod:
 *
 * pods_archief_instellingen
 *
 * en maakt deze beschikbaar als:
 *
 * - shortcode
 * - Elementor Dynamic Tag
 *
 *
 * ============================================================
 * HANDLEIDING
 * ============================================================
 *
 * 1. CPT'S TOEVOEGEN
 * ------------------------------------------------------------
 *
 * Iedere CPT krijgt hieronder een vaste "archive context".
 *
 * Voorbeeld:
 *
 * WordPress CPT:
 * beroepengids
 *
 * Archive context:
 * beroep
 *
 * Daardoor blijven bestaande velden werken:
 *
 * archive_cpt_beroep_intro
 * archive_cpt_beroep_description
 *
 *
 * Voor kennisbank:
 *
 * WordPress CPT:
 * kennisbankartikelen
 *
 * Archive context:
 * kennisbankartikel
 *
 * Velden:
 *
 * archive_cpt_kennisbankartikel_intro
 * archive_cpt_kennisbankartikel_description
 *
 *
 * Nieuwe CPT toevoegen?
 *
 * Voeg hem alleen toe in:
 *
 * tfsc_archive_settings_config()
 *
 * Bijvoorbeeld:
 *
 * 'nieuwsartikelen' => [
 *     'context' => 'nieuwsartikel',
 * ],
 *
 *
 * ============================================================
 * 2. TAXONOMY TOEVOEGEN
 * ============================================================
 *
 * Taxonomies hoeven NIET apart in deze PHP-code geregistreerd
 * te worden.
 *
 * Ze moeten alleen gekoppeld zijn aan de juiste WordPress CPT.
 *
 *
 * AANBEVOLEN NAAMGEVING:
 *
 * {context}_{taxonomy}
 *
 *
 * Voor beroepengids:
 *
 * beroep_functienaam
 * beroep_bedrijfssector
 * beroep_opleiding
 *
 *
 * Voor kennisbankartikelen:
 *
 * kennisbankartikel_onderwerp
 * kennisbankartikel_techniek
 * kennisbankartikel_sector
 *
 *
 * De prefix wordt automatisch verwijderd.
 *
 * Dus:
 *
 * kennisbankartikel_onderwerp
 *
 * wordt:
 *
 * onderwerp
 *
 *
 * ============================================================
 * 3. PODS VELDEN VOOR TAXONOMY ARCHIVES
 * ============================================================
 *
 * Structuur:
 *
 * archive_{context}_{taxonomy}_{field}
 *
 *
 * Voorbeeld beroepengids:
 *
 * taxonomy:
 * beroep_functienaam
 *
 * velden:
 *
 * archive_beroep_functienaam_intro
 * archive_beroep_functienaam_description
 *
 *
 * Voorbeeld kennisbank:
 *
 * taxonomy:
 * kennisbankartikel_onderwerp
 *
 * velden:
 *
 * archive_kennisbankartikel_onderwerp_intro
 * archive_kennisbankartikel_onderwerp_description
 *
 *
 * ============================================================
 * 4. PODS VELDEN VOOR CPT ARCHIVES
 * ============================================================
 *
 * Structuur:
 *
 * archive_cpt_{context}_{field}
 *
 *
 * Beroepengids:
 *
 * archive_cpt_beroep_intro
 * archive_cpt_beroep_description
 *
 *
 * Kennisbank:
 *
 * archive_cpt_kennisbankartikel_intro
 * archive_cpt_kennisbankartikel_description
 *
 *
 * ============================================================
 * 5. ELEMENTOR
 * ============================================================
 *
 * Dynamic Tag:
 *
 * TFSC Archive Settings Field
 *
 *
 * Bij Field vul je alleen in:
 *
 * intro
 *
 * of:
 *
 * description
 *
 *
 * Elementor hoeft dus niet te weten:
 *
 * - welke CPT actief is
 * - welke taxonomy actief is
 * - welke context gebruikt moet worden
 *
 *
 * ============================================================
 * 6. SHORTCODE
 * ============================================================
 *
 * [tfsc_archive_field field="intro"]
 *
 * [tfsc_archive_field field="description"]
 *
 *
 * ============================================================
 * 7. BELANGRIJK
 * ============================================================
 *
 * De Pods GROUP NAME wordt nergens gebruikt.
 *
 * Dus bijvoorbeeld:
 *
 * archive_beroep_taxonomies
 * archive_kennisbankartikelen
 *
 * zijn alleen organisatorische groepsnamen in Pods.
 *
 * Alleen de daadwerkelijke FIELD NAME is belangrijk.
 *
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
 * Centrale CPT → archive context mapping.
 *
 * Dit is de belangrijkste configuratie van deze snippet.
 */

function tfsc_archive_settings_config() {

    return [

        /**
         * Legacy structuur bewust behouden.
         *
         * CPT:
         * beroepengids
         *
         * Archive context:
         * beroep
         */

        'beroepengids' => [
            'context' => 'beroep',
        ],


        /**
         * Kennisbank.
         *
         * CPT:
         * kennisbankartikelen
         *
         * Archive context:
         * kennisbankartikel
         */

        'kennisbankartikelen' => [
            'context' => 'kennisbankartikel',
        ],


        /**
         * Nieuwe CPT later toevoegen:
         *
         * 'voorbeeldartikelen' => [
         *     'context' => 'voorbeeldartikel',
         * ],
         */
    ];
}


/**
 * ============================================================
 * 2. CONTEXT UIT CPT HALEN
 * ============================================================
 */

function tfsc_archive_settings_context_from_post_type(
    $post_type
) {

    $post_type =
        sanitize_key(
            $post_type
        );

    if ( ! $post_type ) {
        return '';
    }

    $config =
        tfsc_archive_settings_config();

    /**
     * CPT staat expliciet in configuratie.
     */

    if (
        isset(
            $config[ $post_type ]['context']
        )
    ) {

        return sanitize_key(
            $config[ $post_type ]['context']
        );
    }

    /**
     * Fallback:
     *
     * CPT slug zelf gebruiken.
     *
     * Hierdoor blijft de code ook werken voor CPT's
     * waarvoor nog geen afwijkende context nodig is.
     */

    return $post_type;
}


/**
 * ============================================================
 * 3. RAW TAXONOMY BEPALEN
 * ============================================================
 *
 * Ondersteunt:
 *
 * - TFSC taxonomy base archives
 * - normale WordPress taxonomy term archives
 */

function tfsc_archive_settings_raw_taxonomy() {

    /**
     * --------------------------------------------------------
     * TFSC TAXONOMY BASE ARCHIVE
     * --------------------------------------------------------
     *
     * Deze functie bestaat al binnen TFSC en bepaalt
     * bijvoorbeeld:
     *
     * beroep_functienaam
     *
     * wanneer we ons op de functienaam-base bevinden.
     */

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
            $taxonomy =
                $taxonomy->name;
        }

        if ( $taxonomy ) {

            return sanitize_key(
                $taxonomy
            );
        }
    }


    /**
     * --------------------------------------------------------
     * NORMAAL WORDPRESS TERM ARCHIVE
     * --------------------------------------------------------
     */

    if (
        is_tax() ||
        is_category() ||
        is_tag()
    ) {

        $object =
            get_queried_object();

        if (
            $object &&
            ! empty(
                $object->taxonomy
            )
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
 * 4. CPT VAN EEN TAXONOMY BEPALEN
 * ============================================================
 */

function tfsc_archive_settings_post_type_from_taxonomy(
    $taxonomy
) {

    $taxonomy =
        sanitize_key(
            $taxonomy
        );

    if ( ! $taxonomy ) {
        return '';
    }


    $config =
        tfsc_archive_settings_config();


    /**
     * --------------------------------------------------------
     * EERST CONTEXT / CPT PREFIX CONTROLEREN
     * --------------------------------------------------------
     *
     * Dit ondersteunt bijvoorbeeld:
     *
     * beroep_functienaam
     *
     * en:
     *
     * kennisbankartikel_onderwerp
     */

    foreach (
        $config
        as $post_type => $settings
    ) {

        $context =
            ! empty(
                $settings['context']
            )
                ? sanitize_key(
                    $settings['context']
                )
                : sanitize_key(
                    $post_type
                );


        $prefixes = [

            $context . '_',
            $context . '-',

            $post_type . '_',
            $post_type . '-',
        ];


        foreach (
            $prefixes
            as $prefix
        ) {

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
     * FALLBACK VIA WORDPRESS TAXONOMY REGISTRATIE
     * --------------------------------------------------------
     */

    $taxonomy_object =
        get_taxonomy(
            $taxonomy
        );

    if (
        ! $taxonomy_object ||
        empty(
            $taxonomy_object->object_type
        )
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


    if (
        empty(
            $post_types
        )
    ) {
        return '';
    }


    /**
     * Als taxonomy aan meerdere CPT's hangt,
     * proberen we eerst een CPT uit onze configuratie.
     */

    foreach (
        $post_types
        as $post_type
    ) {

        if (
            isset(
                $config[ $post_type ]
            )
        ) {

            return sanitize_key(
                $post_type
            );
        }
    }


    /**
     * Anders eerste geregistreerde CPT.
     */

    return sanitize_key(
        $post_types[0]
    );
}


/**
 * ============================================================
 * 5. HUIDIGE CPT BEPALEN
 * ============================================================
 */

function tfsc_archive_settings_post_type() {

    /**
     * --------------------------------------------------------
     * CPT ARCHIVE
     * --------------------------------------------------------
     */

    if (
        is_post_type_archive()
    ) {

        $post_type =
            get_query_var(
                'post_type'
            );


        if (
            is_array(
                $post_type
            )
        ) {

            $post_type =
                reset(
                    $post_type
                );
        }


        if ( $post_type ) {

            return sanitize_key(
                $post_type
            );
        }


        /**
         * Fallback via queried object.
         */

        $object =
            get_queried_object();


        if (
            $object &&
            ! empty(
                $object->name
            )
        ) {

            return sanitize_key(
                $object->name
            );
        }
    }


    /**
     * --------------------------------------------------------
     * TAXONOMY ARCHIVE
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
 * 6. HUIDIGE ARCHIVE CONTEXT
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
 * 7. TAXONOMY NORMALISEREN
 * ============================================================
 *
 * beroep_functienaam
 * → functienaam
 *
 * beroepengids_functienaam
 * → functienaam
 *
 * kennisbankartikel_onderwerp
 * → onderwerp
 *
 * kennisbankartikelen_onderwerp
 * → onderwerp
 */

function tfsc_archive_settings_normalize_taxonomy(
    $taxonomy
) {

    $taxonomy =
        sanitize_key(
            $taxonomy
        );


    if ( ! $taxonomy ) {
        return '';
    }


    $post_type =
        tfsc_archive_settings_post_type();


    $context =
        tfsc_archive_settings_context();


    $prefixes = [];


    /**
     * CPT prefix.
     */

    if ( $post_type ) {

        $prefixes[] =
            $post_type . '_';

        $prefixes[] =
            $post_type . '-';
    }


    /**
     * Archive context prefix.
     */

    if ( $context ) {

        $prefixes[] =
            $context . '_';

        $prefixes[] =
            $context . '-';
    }


    $prefixes =
        array_unique(
            $prefixes
        );


    /**
     * Langste prefix eerst.
     */

    usort(
        $prefixes,
        function(
            $a,
            $b
        ) {

            return
                strlen( $b )
                <=>
                strlen( $a );
        }
    );


    foreach (
        $prefixes
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
                    strlen(
                        $prefix
                    )
                )
            );
        }
    }


    /**
     * Geen prefix gevonden.
     *
     * Dan taxonomy slug ongewijzigd gebruiken.
     */

    return $taxonomy;
}


/**
 * ============================================================
 * 8. HUIDIGE GENORMALISEERDE TAXONOMY
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
 * 9. PODS FIELD NAME OPBOUWEN
 * ============================================================
 */

function tfsc_archive_settings_field_name(
    $field
) {

    $field =
        sanitize_key(
            $field
        );


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
     * TAXONOMY ARCHIVE
     * --------------------------------------------------------
     *
     * archive_{context}_{taxonomy}_{field}
     */

    $taxonomy =
        tfsc_archive_settings_taxonomy();


    /**
     * --------------------------------------------------------
     * LEGACY PODS FIELD ALIAS
     * --------------------------------------------------------
     *
     * De actieve WordPress-taxonomie heet:
     *
     * beroep_opleidingsniveau
     *
     * De bestaande centrale Pods-velden gebruiken echter:
     *
     * archive_beroep_opleiding_intro
     * archive_beroep_opleiding_description
     *
     * Daarom wordt uitsluitend voor het opbouwen van de Pods-
     * veldnaam `opleidingsniveau` vertaald naar `opleiding`.
     * De taxonomie, URL's en termkoppelingen zelf veranderen niet.
     */

    $taxonomy_field_aliases = [
        'opleidingsniveau' => 'opleiding',
    ];


    if (
        isset(
            $taxonomy_field_aliases[ $taxonomy ]
        )
    ) {

        $taxonomy =
            sanitize_key(
                $taxonomy_field_aliases[ $taxonomy ]
            );
    }


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
     *
     * archive_cpt_{context}_{field}
     */

    if (
        is_post_type_archive()
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
 * 10. PODS VALUE OPHALEN
 * ============================================================
 */

function tfsc_archive_settings_value(
    $field
) {

    if (
        ! function_exists(
            'pods'
        )
    ) {
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


    /**
     * Tekst / WYSIWYG formatteren.
     */

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
 * 11. SHORTCODE
 * ============================================================
 *
 * [tfsc_archive_field field="intro"]
 *
 * [tfsc_archive_field field="description"]
 */

add_shortcode(
    'tfsc_archive_field',
    function(
        $atts
    ) {

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


        return (string)
            $value;
    }
);


/**
 * ============================================================
 * 12. ELEMENTOR DYNAMIC TAG
 * ============================================================
 *
 * Dynamic Tag:
 *
 * TFSC Archive Settings Field
 *
 * Field:
 *
 * intro
 *
 * of:
 *
 * description
 */

add_action(
    'elementor/dynamic_tags/register',
    function(
        $dynamic_tags
    ) {

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

                    \Elementor\Modules\DynamicTags\Module
                        ::TEXT_CATEGORY,

                ];
            }


            protected function register_controls() {

                $this->add_control(
                    'field',
                    [

                        'label' =>
                            'Field',

                        'type' =>
                            \Elementor\Controls_Manager
                                ::TEXT,

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


                echo (string)
                    $value;
            }
        }


        $dynamic_tags->register(

            new TFSC_Archive_Settings_Field_Tag()

        );
    }
);
