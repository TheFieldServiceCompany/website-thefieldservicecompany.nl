(function ($) {
    'use strict';
    if (!window.tfscMF) { return; }

    function validate(root) {
        var valid = true;
        root.find('.tfsc-mf-error').text('');
        root.find('input').each(function () {
            if (!this.checkValidity()) { this.reportValidity(); valid = false; return false; }
        });
        if (!valid) { return false; }
        ['width', 'height', 'size'].forEach(function (key) {
            var min = root.find('[data-mf="' + key + '_min"]').val();
            var max = root.find('[data-mf="' + key + '_max"]').val();
            if (min !== '' && max !== '' && Number(min) > Number(max)) {
                valid = false;
                root.find('.tfsc-mf-error').text('De ondergrens mag niet hoger zijn dan de bovengrens.');
            }
        });
        return valid;
    }

    // List view: leave every other form input (including HappyFiles) intact.
    $('.tfsc-mf-list').each(function () {
        var bar = $(this).closest('.wp-filter');
        if (bar.length) { $(this).appendTo(bar).addClass('tfsc-mf-list-row'); }
    });
    $(document).on('click', '.tfsc-mf-list .tfsc-mf-apply', function (event) {
        if (!validate($(this).closest('.tfsc-mf-list'))) { event.preventDefault(); }
    });
    $(document).on('click', '.tfsc-mf-list .tfsc-mf-reset', function () {
        var root = $(this).closest('.tfsc-mf-list');
        root.find('[data-mf]').val('');
        root.find('.tfsc-mf-apply').trigger('click');
    });

    // Administrator-only indexing, resumable in batches with a visible progress message.
    $('#tfsc-mf-index-start').on('click', function () {
        var button = $(this), state = tfscMF.state || {}, restart = !!state.ready;
        button.prop('disabled', true);
        function batch() {
            $.ajax({
                url: window.ajaxurl, method: 'POST', dataType: 'json',
                data: { action: 'tfsc_mf_index', nonce: tfscMF.nonce, restart: restart ? '1' : '' }
            }).done(function (response) {
                if (!response.success) { fail(response.data); return; }
                restart = false;
                tfscMF.state = response.data;
                $('#tfsc-mf-index-status').text(response.data.ready ? 'De index is klaar. Je kunt nu de filters gebruiken.' : response.data.processed + ' van ongeveer ' + response.data.total + ' bestanden verwerkt…');
                if (!response.data.ready) { window.setTimeout(batch, 100); }
                else { button.prop('disabled', false).text('Index opnieuw opbouwen'); }
            }).fail(function (xhr) { fail(xhr.responseJSON && xhr.responseJSON.data); });
        }
        function fail(data) {
            $('#tfsc-mf-index-status').text(data && data.message ? data.message : 'Indexeren onderbroken. Klik opnieuw om te hervatten.');
            button.prop('disabled', false).text('Index starten / hervatten');
        }
        batch();
    });

    if (!tfscMF.ready || !window.wp || !wp.media || !wp.media.view.AttachmentsBrowser) { return; }

    var FilterView = wp.media.View.extend({
        className: 'tfsc-mf-toolbar-item',
        events: { 'click .tfsc-mf-toggle': 'toggle' },
        initialize: function (options) {
            this.props = options.props;
            this.uid = _.uniqueId('tfsc-mf-');
            this.listenTo(this.props, 'change:tfsc_mf', this.updateButton);
            this.handlePosition = this.position.bind(this);
            this.handleOutside = this.outside.bind(this);
            this.handleKeys = this.keys.bind(this);
        },
        render: function () {
            this.$el.html('<button type="button" class="button tfsc-mf-toggle" aria-expanded="false" aria-controls="' + this.uid + '">Extra filters</button>');
            if (!this.panel) {
                this.panel = $('<div>', { id: this.uid, class: 'tfsc-mf-popover', role: 'dialog', 'aria-label': 'Extra mediafilters', tabindex: '-1', hidden: true }).html(tfscMF.controls).appendTo(document.body);
                this.panel.prepend('<button type="button" class="tfsc-mf-close button-link" aria-label="Filters sluiten">Sluiten ×</button>');
                this.panel.on('click', '.tfsc-mf-apply', this.apply.bind(this));
                this.panel.on('click', '.tfsc-mf-reset', this.reset.bind(this));
                this.panel.on('click', '.tfsc-mf-close', this.close.bind(this));
                this.panel.on('keydown', 'input', function (event) { if (event.key === 'Enter') { event.preventDefault(); this.apply(); } }.bind(this));
                $(document).on('pointerdown', this.handleOutside).on('keydown', this.handleKeys);
                $(window).on('resize scroll', this.handlePosition);
            }
            this.updateButton();
            return this;
        },
        updateButton: function () {
            var values = this.props.get('tfsc_mf') || {};
            this.$('.tfsc-mf-toggle').text(Object.keys(values).length ? 'Extra filters (actief)' : 'Extra filters');
        },
        toggle: function () {
            if (!this.panel.prop('hidden')) { this.close(); return; }
            var values = this.props.get('tfsc_mf') || {};
            this.panel.find('[data-mf]').each(function () { $(this).val(values[$(this).data('mf')] === undefined ? '' : values[$(this).data('mf')]); });
            this.panel.find('.tfsc-mf-error').text('');
            this.panel.prop('hidden', false);
            this.$('.tfsc-mf-toggle').attr('aria-expanded', 'true');
            this.position();
            this.panel.find('input').first().trigger('focus');
        },
        position: function () {
            if (!this.panel || this.panel.prop('hidden') || !this.el.isConnected) { return; }
            var rect = this.el.getBoundingClientRect();
            var width = Math.min(640, window.innerWidth - 24);
            this.panel.css({ width: width, left: Math.max(12, Math.min(rect.left, window.innerWidth - width - 12)), top: Math.min(rect.bottom + 8, Math.max(12, window.innerHeight - this.panel.outerHeight() - 12)) });
        },
        close: function (focus) {
            if (!this.panel) { return; }
            this.panel.prop('hidden', true);
            this.$('.tfsc-mf-toggle').attr('aria-expanded', 'false');
            if (focus !== false) { this.$('.tfsc-mf-toggle').trigger('focus'); }
        },
        outside: function (event) {
            if (!this.panel.prop('hidden') && !this.panel[0].contains(event.target) && !this.el.contains(event.target)) { this.close(false); }
        },
        keys: function (event) {
            if (this.panel.prop('hidden')) { return; }
            if (event.key === 'Escape') { event.preventDefault(); event.stopImmediatePropagation(); this.close(); }
            if (event.key === 'Tab') {
                var items = this.panel.find('button, input, select').filter(':visible'), first = items[0], last = items[items.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        },
        apply: function () {
            if (!validate(this.panel)) { return; }
            var values = {};
            this.panel.find('[data-mf]').each(function () { var value = $(this).val().trim(); if (value !== '') { values[$(this).data('mf')] = value; } });
            // One property update; retain taxonomy/folder, search, type, date and ordering.
            this.props.set('tfsc_mf', Object.keys(values).length ? values : null);
            this.close();
        },
        reset: function () {
            this.props.set('tfsc_mf', null);
            this.close();
        },
        dispose: function () {
            $(document).off('pointerdown', this.handleOutside).off('keydown', this.handleKeys);
            $(window).off('resize scroll', this.handlePosition);
            if (this.panel) { this.panel.remove(); }
            return wp.media.View.prototype.dispose.apply(this, arguments);
        }
    });

    // Compose with the existing method, including extensions installed by HappyFiles.
    var original = wp.media.view.AttachmentsBrowser.prototype.createToolbar;
    wp.media.view.AttachmentsBrowser.prototype.createToolbar = function () {
        var result = original.apply(this, arguments);
        if (this.collection && this.collection.props && (this.options.filters || this.collection.props.get('query'))) {
            this.toolbar.set('tfscMediaFilters', new FilterView({ controller: this.controller, props: this.collection.props, priority: -5 }));
        }
        return result;
    };
})(jQuery);
