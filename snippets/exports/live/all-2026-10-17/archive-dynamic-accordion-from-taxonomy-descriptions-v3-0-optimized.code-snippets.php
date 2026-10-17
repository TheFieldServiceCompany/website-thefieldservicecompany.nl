<?php

/**
 * Archive - Dynamic Accordion from taxonomy descriptions [v3.0] - Optimized
 */
/**
 * TFSC - Dynamische Archive Description Accordion
 *
 * Shortcode:
 * [tfsc_archive_accordion]
 *
 * Titel:
 * pods_archive_title → termnaam
 *
 * Content:
 * term description
 */


/**
 * Eén accordion-item maken.
 */
function tfsc_get_archive_accordion_item( $term ) {

    if ( ! $term instanceof WP_Term ) {
        return false;
    }

    $description = trim(
        (string) term_description(
            $term->term_id,
            $term->taxonomy
        )
    );

    if ( '' === $description ) {
        return false;
    }

    $title = get_term_meta(
        $term->term_id,
        'pods_archive_title',
        true
    );

    $title = is_string( $title )
        ? trim( wp_strip_all_tags( $title ) )
        : '';

    if ( '' === $title ) {
        $title = trim( $term->name );
    }

    if ( '' === $title ) {
        return false;
    }

    return [
        'title'       => $title,
        'description' => $description,
        'term_id'     => (int) $term->term_id,
        'taxonomy'    => $term->taxonomy,
    ];
}


/**
 * Actieve archive-terms bepalen.
 */
function tfsc_get_archive_accordion_terms() {

    /**
     * Virtueel combo archive.
     */
    if (
        function_exists( 'tfsc_is_combo_archive' ) &&
        function_exists( 'tfsc_get_combo_archive' ) &&
        tfsc_is_combo_archive()
    ) {

        $combo = tfsc_get_combo_archive();

        if ( ! is_array( $combo ) ) {
            return [];
        }

        $terms = [];
        $seen  = [];

        foreach ( $combo as $item ) {

            $term = $item['term'] ?? null;

            if ( ! $term instanceof WP_Term ) {
                continue;
            }

            $key =
                $term->taxonomy
                . ':'
                . $term->term_id;

            if ( isset( $seen[ $key ] ) ) {
                continue;
            }

            $seen[ $key ] = true;
            $terms[] = $term;
        }

        return $terms;
    }

    /**
     * Native taxonomy archive.
     */
    if ( ! is_tax() ) {
        return [];
    }

    $term = get_queried_object();

    if ( ! $term instanceof WP_Term ) {
        return [];
    }

    $status_taxonomy =
        defined( 'TFSC_STATUS_TAXONOMY' )
            ? TFSC_STATUS_TAXONOMY
            : 'vac_status';

    if ( $term->taxonomy === $status_taxonomy ) {
        return [];
    }

    /**
     * Hoofd-combosnippet gebruiken indien beschikbaar.
     */
    if (
        function_exists( 'tfsc_combo_taxonomy_allowed' )
    ) {
        return tfsc_combo_taxonomy_allowed(
            $term->taxonomy
        )
            ? [ $term ]
            : [];
    }

    /**
     * Fallback: taxonomy moet aan vacature hangen.
     */
    $taxonomy =
        get_taxonomy(
            $term->taxonomy
        );

    $post_type =
        defined( 'TFSC_VACANCY_POST_TYPE' )
            ? TFSC_VACANCY_POST_TYPE
            : 'vacature';

    if (
        ! $taxonomy ||
        ! in_array(
            $post_type,
            (array) $taxonomy->object_type,
            true
        )
    ) {
        return [];
    }

    return [ $term ];
}


/**
 * Accordion-items ophalen.
 */
function tfsc_get_archive_accordion_items() {

    $items = [];

    foreach (
        tfsc_get_archive_accordion_terms()
        as $term
    ) {

        $item =
            tfsc_get_archive_accordion_item(
                $term
            );

        if ( $item ) {
            $items[] = $item;
        }
    }

    return $items;
}


/**
 * Shortcode.
 */
add_shortcode(
    'tfsc_archive_accordion',
    function() {

        $items =
            tfsc_get_archive_accordion_items();

        if ( ! $items ) {
            return '';
        }

        static $instance = 0;
        $instance++;

        $accordion_id =
            'tfsc-archive-accordion-'
            . $instance;

        ob_start();
        ?>

        <div
            id="<?php echo esc_attr( $accordion_id ); ?>"
            class="tfsc-archive-accordion"
        >

            <?php foreach ( $items as $index => $item ) :

                $item_id =
                    $accordion_id
                    . '-item-'
                    . $index;

                $button_id =
                    $item_id . '-button';

                $content_id =
                    $item_id . '-content';
            ?>

                <div
                    class="tfsc-archive-accordion__item"
                    data-taxonomy="<?php echo esc_attr( $item['taxonomy'] ); ?>"
                    data-term-id="<?php echo esc_attr( $item['term_id'] ); ?>"
                >

                    <button
                        type="button"
                        id="<?php echo esc_attr( $button_id ); ?>"
                        class="tfsc-archive-accordion__title"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr( $content_id ); ?>"
                    >
                        <span class="tfsc-archive-accordion__title-text">
                            <?php echo esc_html( $item['title'] ); ?>
                        </span>

                        <span
                            class="tfsc-archive-accordion__chevron"
                            aria-hidden="true"
                        ></span>
                    </button>

                    <div
                        id="<?php echo esc_attr( $content_id ); ?>"
                        class="tfsc-archive-accordion__content"
                        role="region"
                        aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
                        hidden
                    >
                        <div class="tfsc-archive-accordion__content-inner">
                            <?php
                            echo wp_kses_post(
                                $item['description']
                            );
                            ?>
                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <script>
        (function () {

            const accordion =
                document.getElementById(
                    <?php echo wp_json_encode( $accordion_id ); ?>
                );

            if (!accordion) {
                return;
            }

            accordion.addEventListener(
                'click',
                function (event) {

                    const button =
                        event.target.closest(
                            '.tfsc-archive-accordion__title'
                        );

                    if (
                        !button ||
                        !accordion.contains(button)
                    ) {
                        return;
                    }

                    const item =
                        button.closest(
                            '.tfsc-archive-accordion__item'
                        );

                    const content =
                        item?.querySelector(
                            '.tfsc-archive-accordion__content'
                        );

                    if (!content) {
                        return;
                    }

                    const open =
                        button.getAttribute(
                            'aria-expanded'
                        ) === 'true';

                    button.setAttribute(
                        'aria-expanded',
                        String(!open)
                    );

                    item.classList.toggle(
                        'tfsc-archive-accordion__item--open',
                        !open
                    );

                    content.hidden = open;
                }
            );

        })();
        </script>

        <?php
        return ob_get_clean();
    }
);


/**
 * Lege Elementor archive-info container verbergen.
 */
add_action(
    'wp_footer',
    function() {
        ?>
        <script>
        document.querySelectorAll(
            '.tfsc-archive-info'
        ).forEach(
            container =>
                container.classList.toggle(
                    'tfsc-archive-info-empty',
                    !container.querySelector(
                        '.tfsc-archive-accordion'
                    )
                )
        );
        </script>
        <?php
    }
);
