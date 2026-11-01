<?php
/**
 * TFSC scroll navigation v1.0.0 — shared header 17275 + vacancy panel 26022.
 * LIVE. Hooks: wp_head 90 and wp_footer 90. Requires the existing Elementor sticky handlers.
 * Rollback: deactivate this snippet and purge the page cache. Original template data is unchanged.
 */
add_action( 'wp_head', function () {
    echo <<<'TFSC_SCROLL_CSS'
<style id="tfsc-scroll-navigation-css">
/* Activate only after the controller found the shared header and excluded the editor. */
html.tfsc-scroll-navigation .elementor-17275 [data-id="5b8468be"] {
    transition: translate 200ms ease;
    will-change: translate;
}
html.tfsc-scroll-header-hidden .elementor-17275 [data-id="5b8468be"] {
    translate: 0 calc(-100% - 8px);
    pointer-events: none;
}
html.tfsc-scroll-navigation .elementor-26022 [data-id="9d55293"].elementor-sticky--active {
    top: var(--tfsc-navigation-top) !important;
    transition: top 200ms ease;
}
html.tfsc-scroll-navigation .elementor-menu-anchor,
html.tfsc-scroll-navigation [id^="elementor-toc__heading-anchor-"],
html.tfsc-scroll-navigation .toc :is(h2, h3, h4)[id],
html.tfsc-scroll-navigation .elementor-widget-heading[id],
html.tfsc-scroll-navigation #scroll-top {
    scroll-margin-top: var(--tfsc-anchor-top) !important;
}
@media (prefers-reduced-motion: reduce) {
    html.tfsc-scroll-navigation .elementor-17275 [data-id="5b8468be"],
    html.tfsc-scroll-navigation .elementor-26022 [data-id="9d55293"].elementor-sticky--active {
        transition: none;
    }
}
</style>
TFSC_SCROLL_CSS;
}, 90 );
add_action( 'wp_footer', function () {
    echo <<<'TFSC_SCROLL_JS'
<script id="tfsc-scroll-navigation-js" data-no-optimize="1" data-cfasync="false">
/* TFSC shared header + vacancy panel, v1.0.0. No changes to Elementor's sticky lifecycle. */
(function () {
    'use strict';
    function init() {
        const root = document.documentElement;
        const header = document.querySelector('.elementor-17275 [data-id="5b8468be"]');
        const editor = document.body.classList.contains('elementor-editor-active') ||
            document.querySelector('.elementor-edit-mode') ||
            (window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode());
        if (!header || editor || root.classList.contains('tfsc-scroll-navigation')) return;

        const panel = document.querySelector('.elementor-26022 [data-id="9d55293"]');
        const panelWrapper = panel && panel.closest('.scroll-sticky-wrapper');
        const adminBar = document.getElementById('wpadminbar');
        let hidden = false;
        let previousY = Math.max(0, window.scrollY);
        let turnY = previousY;
        let direction = 0;
        let queued = false;
        let anchorLockUntil = 0;

        function setHidden(value) {
            if (value === hidden) return;
            hidden = value;
            root.classList.toggle('tfsc-scroll-header-hidden', hidden);
            header.inert = hidden;
        }

        function update() {
            queued = false;
            const y = Math.max(0, Math.min(window.scrollY, Math.max(0, root.scrollHeight - window.innerHeight)));
            const delta = y - previousY;
            const nextDirection = Math.sign(delta);
            if (nextDirection && nextDirection !== direction) {
                direction = nextDirection;
                turnY = previousY;
            }
            const headerHeight = header.getBoundingClientRect().height;
            const adminHeight = adminBar ? Math.max(0, adminBar.getBoundingClientRect().bottom) : 0;
            const menuOpen = header.querySelector('[aria-expanded="true"], .elementor-menu-toggle.elementor-active');
            const focused = header.contains(document.activeElement) && document.activeElement.matches(':focus-visible');
            const active = header.classList.contains('elementor-sticky--active');

            if (y < Math.max(120, headerHeight * 2) || !active || menuOpen || focused || performance.now() < anchorLockUntil) {
                setHidden(false);
            } else if (Math.abs(y - turnY) >= 12) {
                setHidden(direction > 0);
            }

            const top = adminHeight + (hidden ? 0 : headerHeight);
            root.style.setProperty('--tfsc-navigation-top', top + 'px');
            const panelHeight = panel && panelWrapper.classList.contains('is-visible') &&
                panel.classList.contains('elementor-sticky--active') ? panel.getBoundingClientRect().height : 0;
            root.style.setProperty('--tfsc-anchor-top', (top + panelHeight + 20) + 'px');
            previousY = y;
        }

        function schedule() {
            if (!queued) {
                queued = true;
                window.requestAnimationFrame(update);
            }
        }

        root.classList.add('tfsc-scroll-navigation');
        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', schedule, { passive: true });
        window.addEventListener('orientationchange', schedule);
        header.addEventListener('focusin', function () { setHidden(false); update(); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Tab') {
                anchorLockUntil = performance.now() + 300;
                setHidden(false);
                update();
            }
        });

        // Show the header before the browser calculates an anchor destination.
        // This also prevents an upward anchor jump from revealing a header over its target.
        document.addEventListener('click', function (event) {
            const link = event.target.closest && event.target.closest('a[href]');
            if (!link || event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            let url;
            try { url = new URL(link.href, window.location.href); } catch (error) { return; }
            if (!url.hash || url.origin !== window.location.origin || url.pathname !== window.location.pathname) return;
            let target;
            try { target = document.getElementById(decodeURIComponent(url.hash.slice(1))); } catch (error) { return; }
            if (!target) return;
            anchorLockUntil = performance.now() + 1200;
            setHidden(false);
            update();
        }, true);

        if (window.ResizeObserver) {
            const observer = new ResizeObserver(schedule);
            observer.observe(header);
            if (panel) observer.observe(panel);
            if (adminBar) observer.observe(adminBar);
        }
        update();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
}());
</script>
TFSC_SCROLL_JS;
}, 90 );
