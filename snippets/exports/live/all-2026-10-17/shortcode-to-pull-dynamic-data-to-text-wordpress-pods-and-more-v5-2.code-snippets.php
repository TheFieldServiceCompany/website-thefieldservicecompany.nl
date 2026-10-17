<?php

/**
 * Shortcode to pull dynamic data to text Wordpress, PODS, and more [v5.2]
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

    if ( $object instanceof WP_Post ) {
        return (int) $object->ID;
    }

    return (int) get_the_ID();
}


/**
 * ============================================================
 * 2. CONTEXT POD
 * ============================================================
 */

function tfsc_text_context_pod() {

    if ( ! function_exists( 'pods' ) ) {
        return false;
    }

    $object =
        get_queried_object();

    /**
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

    /**
     * Post / CPT / Elementor preview.
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

    if ( $taxonomy instanceof WP_Taxonomy ) {
        return $taxonomy;
    }

    if ( is_string( $taxonomy ) ) {

        $taxonomy =
            get_taxonomy(
                sanitize_key( $taxonomy )
            );

        return $taxonomy instanceof WP_Taxonomy
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

    if ( '' === $text ) {
        return '';
    }

    switch (
        sanitize_key(
            $case
        )
    ) {

        case 'lower':

            return mb_strtolower(
                $text,
                'UTF-8'
            );


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


        default:

            return $text;
    }
}


/**
 * ============================================================
 * 5. [post_title]
 * ============================================================
 */

add_shortcode(
    'post_title',
    function( $atts ) {

        $atts = shortcode_atts(
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

        if ( '' === $title ) {
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
 */

add_shortcode(
    'archive_title',
    function( $atts ) {

        $atts = shortcode_atts(
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
                        ! is_wp_error( $ancestor )
                    ) {
                        $parents[] =
                            wp_strip_all_tags(
                                $ancestor->name
                            );
                    }
                }
            }

            $taxonomy_object =
                get_taxonomy(
                    $taxonomy
                );

            $label =
                $taxonomy_object
                && ! empty(
                    $taxonomy_object
                        ->labels
                        ->singular_name
                )
                    ? wp_strip_all_tags(
                        $taxonomy_object
                            ->labels
                            ->singular_name
                    )
                    : '';

            $full =
                implode(
                    (string) $atts['separator'],
                    array_merge(
                        $parents,
                        [ $current ]
                    )
                );

            switch ( $mode ) {

                case 'parents':

                    $output =
                        implode(
                            (string) $atts['separator'],
                            $parents
                        );

                    break;

                case 'full':

                    $output =
                        $full;

                    break;

                case 'label_current':

                    $output =
                        $label
                            ? $label
                                . (string) $atts['label_separator']
                                . $current
                            : $current;

                    break;

                case 'label_full':

                    $output =
                        $label
                            ? $label
                                . (string) $atts['label_separator']
                                . $full
                            : $full;

                    break;

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

            if ( ! $taxonomy_object ) {
                return '';
            }

            switch ( $mode ) {

                case 'taxonomy_plural':

                    $output =
                        $taxonomy_object
                            ->labels
                            ->name
                        ?? $taxonomy_object->label
                        ?? '';

                    break;

                case 'taxonomy':
                default:

                    $output =
                        $taxonomy_object
                            ->labels
                            ->singular_name
                        ?? $taxonomy_object->label
                        ?? '';
            }

            $output =
                wp_strip_all_tags(
                    (string) $output
                );
        }

        if ( '' === $output ) {
            return '';
        }

        $output =
            tfsc_text_case(
                $output,
                $atts['case']
            );

        return
            (string) $atts['before']
            . $output
            . (string) $atts['after'];
    }
);


/**
 * ============================================================
 * 7. [tfsc_pods]
 * ============================================================
 */

add_shortcode(
    'tfsc_pods',
    function( $atts ) {

        $atts = shortcode_atts(
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

        $field =
            sanitize_key(
                $atts['field']
            );

        if ( ! $field ) {
            return '';
        }

        $pod =
            tfsc_text_context_pod();

        if ( ! $pod ) {
            return '';
        }

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
         * Normaal tekstveld.
         */
        if (
            ! is_array( $value ) &&
            ! is_object( $value )
        ) {

            $output =
                wp_strip_all_tags(
                    (string) $value
                );

        } else {

            /**
             * Eén relationship normaliseren.
             */
            if (
                is_object( $value ) ||
                (
                    is_array( $value ) &&
                    (
                        isset( $value['name'] ) ||
                        isset( $value['post_title'] ) ||
                        isset( $value['title'] )
                    )
                )
            ) {
                $value = [ $value ];
            }

            $names = [];

            foreach (
                (array) $value
                as $relation
            ) {

                if ( is_array( $relation ) ) {

                    $name =
                        $relation['name']
                        ?? $relation['post_title']
                        ?? $relation['title']
                        ?? '';

                } elseif ( is_object( $relation ) ) {

                    $name =
                        $relation->name
                        ?? $relation->post_title
                        ?? $relation->title
                        ?? '';

                } else {

                    $name = '';
                }

                $name =
                    wp_strip_all_tags(
                        (string) $name
                    );

                if ( '' !== $name ) {
                    $names[] = $name;
                }
            }

            $output =
                implode(
                    (string) $atts['separator'],
                    array_values(
                        array_unique(
                            $names
                        )
                    )
                );
        }

        if ( '' === $output ) {
            return '';
        }

        $output =
            tfsc_text_case(
                $output,
                $atts['case']
            );

        return
            (string) $atts['before']
            . $output
            . (string) $atts['after'];
    }
);
