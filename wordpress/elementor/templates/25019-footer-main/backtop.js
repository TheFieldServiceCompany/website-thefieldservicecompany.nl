/* TFSC central back-to-top: long pages only, after one viewport of scrolling. */
(function () {
    'use strict';
    function init() {
        const widget = document.querySelector('.tfsc-back-to-top');
        if (!widget || widget.dataset.tfscBacktopReady) return;
        const link = widget.querySelector('a');
        if (!link) return;
        const editor = document.body.classList.contains('elementor-editor-active') ||
            document.querySelector('.elementor-edit-mode') ||
            (window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode());
        if (editor) { widget.classList.add('is-visible'); return; }
        widget.dataset.tfscBacktopReady = '1';
        const excluded = /^\/(?:formulier(?:[\/-]|$)|bedankt(?:[\/-]|$)|bedankpagina(?:[\/-]|$)|thank-you(?:[\/-]|$))/i.test(window.location.pathname);
        let visible = false;
        let pending = false;
        let previousBottom = '';
        widget.inert = true;

        function displayed(element) {
            const style = window.getComputedStyle(element);
            return style.display !== 'none' && style.visibility !== 'hidden' && element.getBoundingClientRect().height > 0;
        }
        function update() {
            pending = false;
            const viewport = window.innerHeight;
            const pageHeight = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight);
            const modalOpen = Array.from(document.querySelectorAll('.elementor-popup-modal, .e-off-canvas[aria-hidden="false"], dialog[open]')).some(displayed);
            const enoughScroll = window.scrollY >= (visible ? Math.max(0, viewport - 24) : viewport);
            const next = !excluded && !modalOpen && pageHeight > 2 * viewport && enoughScroll;
            if (next !== visible) {
                visible = next;
                widget.classList.toggle('is-visible', visible);
                widget.inert = !visible;
            }
            let bottom = 24;
            const applyBar = document.querySelector('.elementor-26022 [data-id="772040fb"]');
            if (applyBar && displayed(applyBar)) {
                const bounds = applyBar.getBoundingClientRect();
                if (bounds.top < viewport && bounds.bottom >= viewport - 2) bottom = Math.max(bottom, viewport - bounds.top + 16);
            }
            const value = bottom + 'px';
            if (value !== previousBottom) {
                widget.style.setProperty('--tfsc-backtop-bottom', value);
                previousBottom = value;
            }
        }
        function schedule() {
            if (!pending) { pending = true; window.requestAnimationFrame(update); }
        }
        link.addEventListener('click', function (event) {
            event.preventDefault();
            const destination = document.querySelector('#tfsc-main-content, main, h1');
            if (destination) {
                const previousTabindex = destination.getAttribute('tabindex');
                destination.setAttribute('tabindex', '-1');
                destination.addEventListener('blur', function () {
                    if (previousTabindex === null) destination.removeAttribute('tabindex');
                    else destination.setAttribute('tabindex', previousTabindex);
                }, { once: true });
                destination.focus({ preventScroll: true });
            }
            const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reduced ? 'instant' : 'smooth' });
        });
        window.addEventListener('scroll', schedule, { passive: true });
        window.addEventListener('resize', schedule, { passive: true });
        window.addEventListener('orientationchange', schedule);
        window.addEventListener('pageshow', schedule);
        if (window.ResizeObserver) {
            const observer = new ResizeObserver(schedule);
            observer.observe(document.body);
        }
        if (window.MutationObserver) {
            const observer = new MutationObserver(function (records) {
                if (records.some(function (record) {
                    return record.type === 'childList' || record.target.matches('.elementor-popup-modal, .e-off-canvas, dialog');
                })) schedule();
            });
            observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['aria-hidden', 'open', 'class', 'style'] });
        }
        update();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
}());
