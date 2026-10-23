<?php

/**
 * Query Loop - Show max results and button "Show More" - [v4.1]
 *
 * Zet class op query: .tfsc_query_show-moreZet custom Attribute: data_visible-items|88 is een nummer wat je zelf kan bepalen
 */
/**
 * TFSC - Query Show More
 * Version 1.1 - Stable Item Heights
 *
 * Elementor widget class:
 * tfsc_query_show-more
 *
 * Custom attribute:
 * data_visible-items|8
 *
 * Werking:
 * - start met data_visible-items
 * - "Meer" toont telkens 20 extra items
 * - als alles zichtbaar is wordt knop "Minder"
 * - "Minder" reset naar originele hoeveelheid
 * - voorkomt dat Elementor Grid/Flex items steeds hoger maakt
 */

add_action(
    'wp_footer',
    function() {

        if ( is_admin() ) {
            return;
        }

        ?>
        <script>
        (function () {

            const STEP = 20;


            /**
             * ========================================================
             * INIT
             * ========================================================
             */

            function init(scope) {

                const root =
                    scope || document;

                const widgets = [];


                /**
                 * Scope zelf kan de widget zijn.
                 */
                if (
                    root.classList?.contains(
                        'tfsc_query_show-more'
                    )
                ) {
                    widgets.push(root);
                }


                /**
                 * Of widgets kunnen binnen scope zitten.
                 */
                if (root.querySelectorAll) {

                    widgets.push(
                        ...root.querySelectorAll(
                            '.tfsc_query_show-more'
                        )
                    );
                }


                widgets.forEach(
                    function(widget) {

                        /**
                         * Voorkom opnieuw initialiseren.
                         */
                        if (
                            widget.dataset
                                .tfscShowMoreInitialized
                            === '1'
                        ) {
                            return;
                        }


                        const loop =
                            widget.querySelector(
                                ':scope > .elementor-widget-container .elementor-loop-container'
                            );


                        if (!loop) {
                            return;
                        }


                        const items =
                            Array.from(
                                loop.querySelectorAll(
                                    ':scope > .e-loop-item'
                                )
                            );


                        if (!items.length) {
                            return;
                        }


                        widget.dataset
                            .tfscShowMoreInitialized =
                            '1';


                        /**
                         * Startaantal.
                         */
                        const initial =
                            parseInt(
                                widget.getAttribute(
                                    'data_visible-items'
                                ),
                                10
                            ) || 8;


                        let visibleCount =
                            Math.min(
                                initial,
                                items.length
                            );


                        /**
                         * ====================================================
                         * ITEMS RENDEREN
                         * ====================================================
                         *
                         * Eigen class gebruiken in plaats van inline display.
                         */

                        function renderItems() {

                            items.forEach(
                                function(
                                    item,
                                    index
                                ) {

                                    const visible =
                                        index <
                                        visibleCount;


                                    item.classList.toggle(
                                        'tfsc-query-show-more__item--hidden',
                                        !visible
                                    );


                                    item.setAttribute(
                                        'aria-hidden',
                                        visible
                                            ? 'false'
                                            : 'true'
                                    );
                                }
                            );
                        }


                        renderItems();


                        /**
                         * Geen knop nodig.
                         */
                        if (
                            items.length <=
                            initial
                        ) {
                            return;
                        }


                        /**
                         * ====================================================
                         * BUTTON
                         * ====================================================
                         */

                        const wrapper =
                            document.createElement(
                                'div'
                            );


                        wrapper.className =
                            'tfsc-query-show-more__wrapper';


                        wrapper.innerHTML = `
                            <button
                                type="button"
                                class="tfsc-query-show-more__button"
                                aria-expanded="false"
                            >
                                <span
                                    class="tfsc-query-show-more__label"
                                >
                                    Meer
                                </span>

                                <span
                                    class="tfsc-query-show-more__icon"
                                    aria-hidden="true"
                                ></span>
                            </button>
                        `;


                        loop.insertAdjacentElement(
                            'afterend',
                            wrapper
                        );


                        const button =
                            wrapper.querySelector(
                                '.tfsc-query-show-more__button'
                            );


                        const label =
                            wrapper.querySelector(
                                '.tfsc-query-show-more__label'
                            );


                        /**
                         * ====================================================
                         * CLICK
                         * ====================================================
                         */

                        button.addEventListener(
                            'click',
                            function() {

                                const allVisible =
                                    visibleCount >=
                                    items.length;


                                /**
                                 * ============================================
                                 * MINDER
                                 * ============================================
                                 */

                                if (allVisible) {

                                    visibleCount =
                                        Math.min(
                                            initial,
                                            items.length
                                        );


                                    renderItems();


                                    button.setAttribute(
                                        'aria-expanded',
                                        'false'
                                    );


                                    button.classList.remove(
                                        'tfsc-query-show-more__button--expanded'
                                    );


                                    label.textContent =
                                        'Meer';


                                    widget.scrollIntoView({
                                        behavior:
                                            'smooth',

                                        block:
                                            'start',
                                    });


                                    return;
                                }


                                /**
                                 * ============================================
                                 * MEER
                                 * ============================================
                                 */

                                visibleCount =
                                    Math.min(
                                        visibleCount +
                                        STEP,
                                        items.length
                                    );


                                renderItems();


                                const nowAllVisible =
                                    visibleCount >=
                                    items.length;


                                button.setAttribute(
                                    'aria-expanded',
                                    String(
                                        nowAllVisible
                                    )
                                );


                                button.classList.toggle(
                                    'tfsc-query-show-more__button--expanded',
                                    nowAllVisible
                                );


                                label.textContent =
                                    nowAllVisible
                                        ? 'Minder'
                                        : 'Meer';
                            }
                        );
                    }
                );
            }


            /**
             * ========================================================
             * NORMALE PAGINALOAD
             * ========================================================
             */

            document.addEventListener(
                'DOMContentLoaded',
                function() {

                    init(document);
                }
            );


            /**
             * ========================================================
             * ELEMENTOR
             * ========================================================
             */

            window.addEventListener(
                'elementor/frontend/init',
                function() {

                    if (
                        typeof elementorFrontend ===
                        'undefined'
                    ) {
                        return;
                    }


                    elementorFrontend.hooks.addAction(
                        'frontend/element_ready/global',
                        function(scope) {

                            init(
                                scope?.[0] ||
                                scope
                            );
                        }
                    );
                }
            );


            /**
             * ========================================================
             * AJAX / DYNAMISCH TOEGEVOEGDE CONTENT
             * ========================================================
             */

            new MutationObserver(
                function(mutations) {

                    mutations.forEach(
                        function(mutation) {

                            mutation.addedNodes.forEach(
                                function(node) {

                                    if (
                                        node.nodeType !==
                                        1
                                    ) {
                                        return;
                                    }


                                    init(node);
                                }
                            );
                        }
                    );
                }
            ).observe(
                document.documentElement,
                {
                    childList:
                        true,

                    subtree:
                        true,
                }
            );

        })();
        </script>


        <style>

        /**
         * ============================================================
         * LOOP HEIGHT / GRID FIX
         * ============================================================
         *
         * Belangrijk:
         *
         * voorkom dat Elementor/CSS Grid vrije verticale ruimte
         * over de grid-rijen verdeelt wanneer meer items zichtbaar
         * worden.
         */

        .tfsc_query_show-more
        .elementor-loop-container {
            align-content: start;
        }


        /**
         * Items zelf niet verticaal laten uitrekken.
         */
        .tfsc_query_show-more
        .elementor-loop-container
        > .e-loop-item {
            align-self: start;
            min-height: 0;
        }


        /**
         * Verborgen items volledig uit layout verwijderen.
         */
        .tfsc_query_show-more
        .elementor-loop-container
        > .e-loop-item.tfsc-query-show-more__item--hidden {
            display: none !important;
        }


        /**
         * ============================================================
         * BUTTON WRAPPER
         * ============================================================
         */

        .tfsc_query_show-more
        .tfsc-query-show-more__wrapper {
            width: 100%;
        }


        /**
         * ============================================================
         * BUTTON
         * ============================================================
         */

        .tfsc_query_show-more
        .tfsc-query-show-more__button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }


        /**
         * ============================================================
         * ICON
         * ============================================================
         */

        .tfsc_query_show-more
        .tfsc-query-show-more__icon {
            display: inline-block;

            width: 8px;
            height: 8px;

            border-right:
                2px solid currentColor;

            border-bottom:
                2px solid currentColor;

            transform:
                rotate(45deg)
                translateY(-2px);

            transition:
                transform 0.2s ease;
        }


        .tfsc_query_show-more
        .tfsc-query-show-more__button--expanded
        .tfsc-query-show-more__icon {
            transform:
                rotate(225deg)
                translateY(-2px);
        }

        </style>
        <?php
    },
    100
);
