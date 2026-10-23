<?php

/**
 * Query Loop - Show max results and button "Show More" - [v4.0]
 *
 * Zet class op query: .tfsc_query_show-moreZet custom Attribute: data_visible-items|88 is een nummer wat je zelf kan bepalen
 */
/**
 * TFSC - Query Show More
 *
 * Elementor widget class:
 * tfsc_query_show-more
 *
 * Custom attribute:
 * data_visible-items|8
 *
 * Werking:
 * - start met data_visible-items
 * - "Meer tonen" toont telkens 20 extra items
 * - als alles zichtbaar is wordt knop "Minder tonen"
 * - "Minder tonen" reset naar originele hoeveelheid
 *   en scrollt terug naar bovenkant van de widget
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

            function init(scope) {

                const root = scope || document;
                const widgets = [];

                if (
                    root.classList?.contains(
                        'tfsc_query_show-more'
                    )
                ) {
                    widgets.push(root);
                }

                if (root.querySelectorAll) {
                    widgets.push(
                        ...root.querySelectorAll(
                            '.tfsc_query_show-more'
                        )
                    );
                }

                widgets.forEach(
                    function (widget) {

                        widget
                            .querySelector(
                                '.tfsc-query-show-more__wrapper'
                            )
                            ?.remove();

                        const initial =
                            parseInt(
                                widget.getAttribute(
                                    'data_visible-items'
                                ),
                                10
                            ) || 8;

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

                        let visibleCount =
                            Math.min(
                                initial,
                                items.length
                            );

                        function renderItems() {

                            items.forEach(
                                (item, index) => {
                                    item.style.display =
                                        index < visibleCount
                                            ? ''
                                            : 'none';
                                }
                            );
                        }

                        renderItems();

                        if (
                            items.length <= initial
                        ) {
                            return;
                        }

                        const wrapper =
                            document.createElement('div');

                        wrapper.className =
                            'tfsc-query-show-more__wrapper';

                        wrapper.innerHTML = `
                            <button
                                type="button"
                                class="tfsc-query-show-more__button"
                                aria-expanded="false"
                            >
                                <span class="tfsc-query-show-more__label">
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
                            wrapper.querySelector('button');

                        const label =
                            wrapper.querySelector(
                                '.tfsc-query-show-more__label'
                            );

                        button.addEventListener(
                            'click',
                            function () {

                                const allVisible =
                                    visibleCount >=
                                    items.length;

                                /**
                                 * Minder tonen:
                                 * terug naar startaantal.
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
                                        behavior: 'smooth',
                                        block: 'start',
                                    });

                                    return;
                                }

                                /**
                                 * Meer tonen:
                                 * telkens 20 extra.
                                 */
                                visibleCount =
                                    Math.min(
                                        visibleCount + STEP,
                                        items.length
                                    );

                                renderItems();

                                const nowAllVisible =
                                    visibleCount >=
                                    items.length;

                                button.setAttribute(
                                    'aria-expanded',
                                    String(nowAllVisible)
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


            document.addEventListener(
                'DOMContentLoaded',
                () => init(document)
            );


            window.addEventListener(
                'elementor/frontend/init',
                function () {

                    if (
                        typeof elementorFrontend ===
                        'undefined'
                    ) {
                        return;
                    }

                    elementorFrontend.hooks.addAction(
                        'frontend/element_ready/global',
                        function (scope) {

                            init(
                                scope?.[0] || scope
                            );
                        }
                    );
                }
            );


            new MutationObserver(
                mutations => {

                    mutations.forEach(
                        mutation => {

                            mutation.addedNodes.forEach(
                                node => {

                                    if (
                                        node.nodeType === 1
                                    ) {
                                        init(node);
                                    }
                                }
                            );
                        }
                    );
                }
            ).observe(
                document.documentElement,
                {
                    childList: true,
                    subtree: true,
                }
            );

        })();
        </script>

        <style>
        .tfsc_query_show-more .tfsc-query-show-more__wrapper {
            width: 100%;
        }

        .tfsc_query_show-more .tfsc-query-show-more__button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .tfsc_query_show-more .tfsc-query-show-more__icon {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-right: 2px solid currentColor;
            border-bottom: 2px solid currentColor;
            transform: rotate(45deg) translateY(-2px);
            transition: transform 0.2s ease;
        }

        .tfsc_query_show-more
        .tfsc-query-show-more__button--expanded
        .tfsc-query-show-more__icon {
            transform: rotate(225deg) translateY(-2px);
        }
        </style>
        <?php
    },
    100
);
