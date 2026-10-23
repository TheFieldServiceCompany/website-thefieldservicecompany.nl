<?php

/**
 * Shortcode to pull dynamic data to text [v6.0]
 *
 * Use shortcodes to show post_title, archive_title, and strip them how you like. See manual inside snippet. Added functionality: Case Sensitive: Normal, Sentence (Capital first letter), LowerExamples: [post_title case='sentence']             [archive_title case='lower']           [tfsc_pods field="relationship_functienaam" case='sentence']Werkt ook met tax base archives: [archive_title mode="taxonomy"]  of meervoud [archive_title mode="taxonomy_plural"]
 */
/**
 * TFSC - Dynamische tekst shortcodes
 *
 * Shortcodes:
 *
 * [post_title]
 * [archive_title]
 * [tfsc_pods field="..."]
 *
 * ============================================================
 * CASE
 * ============================================================
 *
 * case="normal"
 * case="lower"
 * case="sentence"
 *
 *
 * ============================================================
 * [archive_title] MODES
 * ============================================================
 *
 * Term archives:
 *
 * current
 * parents
 * full
 * label_current
 * label_full
 *
 * Taxonomy base archives:
 *
 * taxonomy
 * taxonomy_plural
 *
 *
 * ============================================================
 * PODS OUTPUT
 * ============================================================
 *
 * [tfsc_pods] gebruikt voor normale Pods velden:
 *
 *     $pod->display()
 *
 * in plaats van:
 *
 *     $pod->field()
 *
 * Hierdoor worden de OUTPUT SETTINGS van het Pods veld gebruikt.
 *
 * Dit is met name belangrijk voor:
 *
 * - WYSIWYG velden
 * - wpautop
 * - HTML sanitizing
 * - shortcodes
 * - wptexturize
 * - convert_chars
 *
 * Relationship velden worden apart verwerkt omdat display()
 * daar niet altijd de gewenste losse namen oplevert.
 *
 * BELANGRIJK:
 *
 * case="lower" en case="sentence" worden NIET toegepast op
 * HTML/WYSIWYG output. Daarmee zouden namelijk ook HTML tags
 * en attributen kunnen worden aangepast.
 */


/**
 * ============================================================
 * 1. CONTEXT POST ID
 * ============================================================
 */

function tfsc_text_context_post_id() {

    if (
        function_exists(
            'tfsc_derived_rel_v3_get_context_post_id'
        )
    ) {

        $id =
            tfsc_derived_rel_v3_get_context_post_id();

        if ( $id ) {
            return (int) $id;
        }
    }


    if (
        function_exists(
            'tfsc_derived_rel_v2_get_context_post_id'
        )
    ) {

        $id =
            tfsc_derived_rel_v2_get_context_post_id();

        if ( $id ) {
            return (int) $id;
        }
    }


    $object =
        get_queried_object();


    if (
        $object instanceof WP_Post
    ) {

        return (int)
            $object->ID;
    }


    return (int)
        get_the_ID();
}


/**
 * ============================================================
 * 2. CONTEXT POD
 * ============================================================
 */

function tfsc_text_context_pod() {

    if (
        ! function_exists(
            'pods'
        )
    ) {
        return false;
    }


    $object =
        get_queried_object();


    /**
     * --------------------------------------------------------
     * TAXONOMY TERM
     * --------------------------------------------------------
     */

    if (
        $object instanceof WP_Term
    ) {

        $pod =
            pods(
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
     * --------------------------------------------------------
     * POST / CPT / ELEMENTOR PREVIEW
     * --------------------------------------------------------
     */

    $post_id =
        tfsc_text_context_post_id();


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


    $pod =
        pods(
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
 * 3. TAXONOMY BASE OBJECT
 * ============================================================
 */

function tfsc_text_taxonomy_archive_object() {

    if (
        ! function_exists(
            'tfsc_get_current_taxonomy_archive'
        )
    ) {
        return false;
    }


    $taxonomy =
        tfsc_get_current_taxonomy_archive();


    if (
        $taxonomy instanceof WP_Taxonomy
    ) {

        return $taxonomy;
    }


    if (
        is_string(
            $taxonomy
        )
    ) {

        $taxonomy =
            get_taxonomy(
                sanitize_key(
                    $taxonomy
                )
            );


        return (
            $taxonomy instanceof WP_Taxonomy
        )
            ? $taxonomy
            : false;
    }


    return false;
}


/**
 * ============================================================
 * 4. TEXT CASE
 * ============================================================
 */

function tfsc_text_case(
    $text,
    $case = 'normal'
) {

    $text =
        (string) $text;


    if (
        '' === $text
    ) {
        return '';
    }


    switch (
        sanitize_key(
            $case
        )
    ) {

        /**
         * ----------------------------------------------------
         * LOWERCASE
         * ----------------------------------------------------
         */

        case 'lower':

            return
                mb_strtolower(
                    $text,
                    'UTF-8'
                );


        /**
         * ----------------------------------------------------
         * SENTENCE CASE
         * ----------------------------------------------------
         */

        case 'sentence':

            $text =
                mb_strtolower(
                    $text,
                    'UTF-8'
                );


            return

                mb_strtoupper(
                    mb_substr(
                        $text,
                        0,
                        1,
                        'UTF-8'
                    ),
                    'UTF-8'
                )

                .

                mb_substr(
                    $text,
                    1,
                    null,
                    'UTF-8'
                );


        /**
         * ----------------------------------------------------
         * NORMAL
         * ----------------------------------------------------
         */

        default:

            return $text;
    }
}


/**
 * ============================================================
 * 5. [post_title]
 * ============================================================
 *
 * Voorbeelden:
 *
 * [post_title]
 *
 * [post_title case="lower"]
 *
 * [post_title before="Vacature: "]
 */

add_shortcode(
    'post_title',
    function( $atts ) {

        $atts =
            shortcode_atts(
                [
                    'before' => '',
                    'after'  => '',
                    'case'   => 'normal',
                ],
                $atts,
                'post_title'
            );


        $post_id =
            tfsc_text_context_post_id();


        if ( ! $post_id ) {
            return '';
        }


        $title =
            wp_strip_all_tags(
                get_the_title(
                    $post_id
                )
            );


        if (
            '' === $title
        ) {
            return '';
        }


        $title =
            tfsc_text_case(
                $title,
                $atts['case']
            );


        return
            (string) $atts['before']
            . $title
            . (string) $atts['after'];
    }
);


/**
 * ============================================================
 * 6. [archive_title]
 * ============================================================
 *
 * TERM ARCHIVE MODES
 *
 * [archive_title]
 * [archive_title mode="current"]
 * [archive_title mode="parents"]
 * [archive_title mode="full"]
 * [archive_title mode="label_current"]
 * [archive_title mode="label_full"]
 *
 *
 * TAXONOMY BASE ARCHIVE MODES
 *
 * [archive_title mode="taxonomy"]
 * [archive_title mode="taxonomy_plural"]
 */

add_shortcode(
    'archive_title',
    function( $atts ) {

        $atts =
            shortcode_atts(
                [
                    'mode'            => 'current',
                    'separator'       => ' › ',
                    'label_separator' => ': ',
                    'before'          => '',
                    'after'           => '',
                    'case'            => 'normal',
                ],
                $atts,
                'archive_title'
            );


        $mode =
            sanitize_key(
                $atts['mode']
            );


        $object =
            get_queried_object();


        /**
         * ====================================================
         * TAXONOMY TERM ARCHIVE
         * ====================================================
         */

        if (
            $object instanceof WP_Term &&
            taxonomy_exists(
                $object->taxonomy
            )
        ) {

            $taxonomy =
                $object->taxonomy;


            $current =
                wp_strip_all_tags(
                    $object->name
                );


            $parents = [];


            /**
             * Hiërarchische parents verzamelen.
             */

            if (
                is_taxonomy_hierarchical(
                    $taxonomy
                )
            ) {

                foreach (
                    array_reverse(
                        get_ancestors(
                            $object->term_id,
                            $taxonomy,
                            'taxonomy'
                        )
                    )
                    as $ancestor_id
                ) {

                    $ancestor =
                        get_term(
                            $ancestor_id,
                            $taxonomy
                        );


                    if (
                        $ancestor instanceof WP_Term &&
                        ! is_wp_error(
                            $ancestor
                        )
                    ) {

                        $parents[] =
                            wp_strip_all_tags(
                                $ancestor->name
                            );
                    }
                }
            }


            /**
             * Taxonomy label.
             */

            $taxonomy_object =
                get_taxonomy(
                    $taxonomy
                );


            $label =
                (
                    $taxonomy_object &&
                    ! empty(
                        $taxonomy_object
                            ->labels
                            ->singular_name
                    )
                )
                    ? wp_strip_all_tags(
                        $taxonomy_object
                            ->labels
                            ->singular_name
                    )
                    : '';


            /**
             * Volledige parentstructuur.
             */

            $full =
                implode(
                    (string)
                    $atts['separator'],
                    array_merge(
                        $parents,
                        [ $current ]
                    )
                );


            switch (
                $mode
            ) {

                /**
                 * Parents zonder current term.
                 */

                case 'parents':

                    $output =
                        implode(
                            (string)
                            $atts['separator'],
                            $parents
                        );

                    break;


                /**
                 * Parents + current term.
                 */

                case 'full':

                    $output =
                        $full;

                    break;


                /**
                 * Taxonomy label + current term.
                 */

                case 'label_current':

                    $output =
                        $label
                            ? $label
                                . (string)
                                $atts['label_separator']
                                . $current
                            : $current;

                    break;


                /**
                 * Taxonomy label + volledige hiërarchie.
                 */

                case 'label_full':

                    $output =
                        $label
                            ? $label
                                . (string)
                                $atts['label_separator']
                                . $full
                            : $full;

                    break;


                /**
                 * Alleen huidige term.
                 */

                default:

                    $output =
                        $current;
            }


        /**
         * ====================================================
         * TAXONOMY BASE ARCHIVE
         * ====================================================
         */

        } else {

            $taxonomy_object =
                tfsc_text_taxonomy_archive_object();


            if (
                ! $taxonomy_object
            ) {
                return '';
            }


            switch (
                $mode
            ) {

                /**
                 * Meervoud taxonomy label.
                 */

                case 'taxonomy_plural':

                    $output =
                        $taxonomy_object
                            ->labels
                            ->name
                        ??
                        $taxonomy_object
                            ->label
                        ??
                        '';

                    break;


                /**
                 * Enkelvoud taxonomy label.
                 */

                case 'taxonomy':
                default:

                    $output =
                        $taxonomy_object
                            ->labels
                            ->singular_name
                        ??
                        $taxonomy_object
                            ->label
                        ??
                        '';
            }


            $output =
                wp_strip_all_tags(
                    (string)
                    $output
                );
        }


        if (
            '' === $output
        ) {
            return '';
        }


        $output =
            tfsc_text_case(
                $output,
                $atts['case']
            );


        return
            (string)
            $atts['before']
            . $output
            . (string)
            $atts['after'];
    }
);


/**
 * ============================================================
 * 7. [tfsc_pods]
 * ============================================================
 *
 * Voorbeelden:
 *
 * [tfsc_pods field="intro"]
 *
 * [tfsc_pods field="functie"]
 *
 * [tfsc_pods field="sector" separator=", "]
 *
 * [tfsc_pods field="plaats" case="lower"]
 *
 *
 * ============================================================
 * BELANGRIJK VOOR WYSIWYG
 * ============================================================
 *
 * Voor scalar / tekstvelden wordt:
 *
 * $pod->display()
 *
 * gebruikt.
 *
 * Hierdoor gebruikt Pods zelf de ingestelde Output Options
 * van het veld.
 *
 * Dus bijvoorbeeld bij WYSIWYG:
 *
 * - wpautop
 * - sanitize HTML
 * - wptexturize
 * - convert_chars
 * - shortcodes
 *
 * hoeven hier NIET opnieuw door PHP uitgevoerd te worden.
 */

add_shortcode(
    'tfsc_pods',
    function( $atts ) {

        $atts =
            shortcode_atts(
                [
                    'field'     => '',
                    'before'    => '',
                    'after'     => '',
                    'separator' => ', ',
                    'case'      => 'normal',
                ],
                $atts,
                'tfsc_pods'
            );


        /**
         * ----------------------------------------------------
         * FIELD
         * ----------------------------------------------------
         */

        $field =
            sanitize_key(
                $atts['field']
            );


        if (
            ! $field
        ) {
            return '';
        }


        /**
         * ----------------------------------------------------
         * POD CONTEXT
         * ----------------------------------------------------
         */

        $pod =
            tfsc_text_context_pod();


        if (
            ! $pod
        ) {
            return '';
        }


        /**
         * ----------------------------------------------------
         * RAW VALUE
         * ----------------------------------------------------
         *
         * field() gebruiken we hier alleen om te bepalen
         * of het om een scalar of relationship/array gaat.
         */

        $value =
            $pod->field(
                $field
            );


        if (
            null === $value ||
            false === $value ||
            '' === $value
        ) {
            return '';
        }


        /**
         * ====================================================
         * SCALAR / TEKST / WYSIWYG
         * ====================================================
         */

        if (
            ! is_array(
                $value
            ) &&
            ! is_object(
                $value
            )
        ) {

            /**
             * Pods verzorgt zelf de frontend output.
             *
             * Voor WYSIWYG worden hierdoor de Output Options
             * van het veld gerespecteerd.
             */

            $output =
                $pod->display(
                    $field
                );


            if (
                null === $output ||
                false === $output
            ) {
                return '';
            }


            $output =
                (string)
                $output;


            if (
                '' === trim(
                    $output
                )
            ) {
                return '';
            }


            /**
             * ------------------------------------------------
             * CASE
             * ------------------------------------------------
             *
             * Alleen toepassen wanneer de output geen HTML
             * bevat.
             *
             * Dit voorkomt dat WYSIWYG HTML kapot wordt
             * gemaakt door strtolower/sentence case.
             */

            if (
                $output ===
                wp_strip_all_tags(
                    $output
                )
            ) {

                $output =
                    tfsc_text_case(
                        $output,
                        $atts['case']
                    );
            }


        /**
         * ====================================================
         * RELATIONSHIP / ARRAY / OBJECT
         * ====================================================
         */

        } else {

            /**
             * Eén relationship normaliseren naar array.
             */

            if (
                is_object(
                    $value
                )
                ||
                (
                    is_array(
                        $value
                    )
                    &&
                    (
                        isset(
                            $value['name']
                        )
                        ||
                        isset(
                            $value['post_title']
                        )
                        ||
                        isset(
                            $value['title']
                        )
                    )
                )
            ) {

                $value =
                    [ $value ];
            }


            $names = [];


            foreach (
                (array)
                $value
                as $relation
            ) {

                /**
                 * --------------------------------------------
                 * ARRAY RELATIONSHIP
                 * --------------------------------------------
                 */

                if (
                    is_array(
                        $relation
                    )
                ) {

                    $name =
                        $relation['name']
                        ??
                        $relation['post_title']
                        ??
                        $relation['title']
                        ??
                        '';


                /**
                 * --------------------------------------------
                 * OBJECT RELATIONSHIP
                 * --------------------------------------------
                 */

                } elseif (
                    is_object(
                        $relation
                    )
                ) {

                    $name =
                        $relation->name
                        ??
                        $relation->post_title
                        ??
                        $relation->title
                        ??
                        '';


                /**
                 * --------------------------------------------
                 * ONBEKEND
                 * --------------------------------------------
                 */

                } else {

                    $name = '';
                }


                $name =
                    wp_strip_all_tags(
                        (string)
                        $name
                    );


                if (
                    '' !== $name
                ) {

                    $names[] =
                        $name;
                }
            }


            /**
             * Dubbelen verwijderen en samenvoegen.
             */

            $names =
                array_values(
                    array_unique(
                        $names
                    )
                );


            $output =
                implode(
                    (string)
                    $atts['separator'],
                    $names
                );


            if (
                '' === $output
            ) {
                return '';
            }


            /**
             * Relationships zijn gewone tekst,
             * dus case kan hier veilig worden toegepast.
             */

            $output =
                tfsc_text_case(
                    $output,
                    $atts['case']
                );
        }


        /**
         * ====================================================
         * OUTPUT
         * ====================================================
         */

        return
            (string)
            $atts['before']
            . $output
            . (string)
            $atts['after'];
    }
);
