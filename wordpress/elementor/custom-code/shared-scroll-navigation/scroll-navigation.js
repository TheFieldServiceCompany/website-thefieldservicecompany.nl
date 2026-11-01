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
