<?php

/**
 * Pods admin page filter
 */
/**
 * Pods Admin - extra filters op Manage Pods tabel
 *
 * Voegt toe:
 * - Zoekveld voor Label + Name
 * - Type filter
 * - Storage Type filter
 * - Connection filter
 * - Reset knop
 *
 * Filters werken gecombineerd met AND-logica.
 * Alles gebeurt alleen client-side in de Pods adminlijst.
 */

add_action('admin_footer', function () {

    // Alleen in wp-admin.
    if (!is_admin()) {
        return;
    }

    // Alleen op Pods Admin pagina's.
    if (
        empty($_GET['page']) ||
        strpos((string) $_GET['page'], 'pods') === false
    ) {
        return;
    }

    ?>
    <style>
        #tfsc-pods-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin: 14px 0 12px;
            padding: 12px;
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
        }

        #tfsc-pods-filters input[type="search"],
        #tfsc-pods-filters select {
            min-width: 180px;
            height: 32px;
        }

        #tfsc-pods-filters input[type="search"] {
            min-width: 240px;
        }

        #tfsc-pods-filter-count {
            margin-left: 5px;
            color: #646970;
            font-size: 13px;
        }

        .tfsc-pods-hidden {
            display: none !important;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
         * Zoek de Manage Pods tabel.
         * We bepalen hem op basis van de kolomnamen zodat de snippet
         * zo min mogelijk afhankelijk is van Pods classes/IDs.
         */
        const tables = Array.from(document.querySelectorAll('table'));

        let podsTable = null;

        for (const table of tables) {
            const headers = Array.from(
                table.querySelectorAll('thead th')
            ).map(th => th.textContent.trim().toLowerCase());

            if (
                headers.includes('label') &&
                headers.includes('name') &&
                headers.includes('type') &&
                headers.includes('storage type') &&
                headers.includes('connections')
            ) {
                podsTable = table;
                break;
            }
        }

        if (!podsTable) {
            return;
        }

        const headers = Array.from(podsTable.querySelectorAll('thead th'));

        const getColumnIndex = function (name) {
            return headers.findIndex(
                th => th.textContent.trim().toLowerCase() === name.toLowerCase()
            );
        };

        const columnIndex = {
            label: getColumnIndex('Label'),
            name: getColumnIndex('Name'),
            type: getColumnIndex('Type'),
            storage: getColumnIndex('Storage Type'),
            connections: getColumnIndex('Connections')
        };

        if (
            columnIndex.label === -1 ||
            columnIndex.name === -1 ||
            columnIndex.type === -1 ||
            columnIndex.storage === -1 ||
            columnIndex.connections === -1
        ) {
            return;
        }

        const tbody = podsTable.querySelector('tbody');

        if (!tbody) {
            return;
        }

        const rows = Array.from(tbody.querySelectorAll('tr'));

        if (!rows.length) {
            return;
        }

        /*
         * Lees tekst uit een specifieke kolom.
         */
        const getCellText = function (row, index) {
            const cells = row.querySelectorAll('td');

            if (!cells[index]) {
                return '';
            }

            return cells[index].textContent.trim();
        };

        /*
         * Unieke waarden verzamelen voor dropdowns.
         */
        const getUniqueValues = function (index) {
            const values = new Set();

            rows.forEach(function (row) {
                const value = getCellText(row, index);

                if (
                    value &&
                    value !== '-' &&
                    value !== '—'
                ) {
                    values.add(value);
                }
            });

            return Array.from(values).sort(function (a, b) {
                return a.localeCompare(b, undefined, {
                    numeric: true,
                    sensitivity: 'base'
                });
            });
        };

        /*
         * Filterbalk bouwen.
         */
        const filterBar = document.createElement('div');
        filterBar.id = 'tfsc-pods-filters';

        const searchInput = document.createElement('input');
        searchInput.type = 'search';
        searchInput.id = 'tfsc-pods-search';
        searchInput.placeholder = 'Zoek Label of Name…';

        const typeSelect = document.createElement('select');
        typeSelect.id = 'tfsc-pods-type';

        const storageSelect = document.createElement('select');
        storageSelect.id = 'tfsc-pods-storage';

        const connectionSelect = document.createElement('select');
        connectionSelect.id = 'tfsc-pods-connection';

        const resetButton = document.createElement('button');
        resetButton.type = 'button';
        resetButton.className = 'button';
        resetButton.textContent = 'Reset';

        const countLabel = document.createElement('span');
        countLabel.id = 'tfsc-pods-filter-count';

        /*
         * Dropdown vullen.
         */
        const populateSelect = function (select, label, values) {
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = label;

            select.appendChild(defaultOption);

            values.forEach(function (value) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                select.appendChild(option);
            });
        };

        populateSelect(
            typeSelect,
            'Alle types',
            getUniqueValues(columnIndex.type)
        );

        populateSelect(
            storageSelect,
            'Alle storage types',
            getUniqueValues(columnIndex.storage)
        );

        populateSelect(
            connectionSelect,
            'Alle connections',
            getUniqueValues(columnIndex.connections)
        );

        filterBar.appendChild(searchInput);
        filterBar.appendChild(typeSelect);
        filterBar.appendChild(storageSelect);
        filterBar.appendChild(connectionSelect);
        filterBar.appendChild(resetButton);
        filterBar.appendChild(countLabel);

        /*
         * Filterbalk direct boven de tabel zetten.
         */
        podsTable.parentNode.insertBefore(filterBar, podsTable);

        /*
         * Filterlogica.
         *
         * Alle actieve filters moeten tegelijk overeenkomen:
         * search AND type AND storage AND connection
         */
        const applyFilters = function () {

            const searchValue = searchInput.value
                .trim()
                .toLowerCase();

            const typeValue = typeSelect.value;
            const storageValue = storageSelect.value;
            const connectionValue = connectionSelect.value;

            let visibleCount = 0;

            rows.forEach(function (row) {

                const label = getCellText(
                    row,
                    columnIndex.label
                );

                const name = getCellText(
                    row,
                    columnIndex.name
                );

                const type = getCellText(
                    row,
                    columnIndex.type
                );

                const storage = getCellText(
                    row,
                    columnIndex.storage
                );

                const connection = getCellText(
                    row,
                    columnIndex.connections
                );

                const searchText = (
                    label + ' ' + name
                ).toLowerCase();

                const matchesSearch =
                    !searchValue ||
                    searchText.includes(searchValue);

                const matchesType =
                    !typeValue ||
                    type === typeValue;

                const matchesStorage =
                    !storageValue ||
                    storage === storageValue;

                const matchesConnection =
                    !connectionValue ||
                    connection === connectionValue;

                const visible =
                    matchesSearch &&
                    matchesType &&
                    matchesStorage &&
                    matchesConnection;

                row.classList.toggle(
                    'tfsc-pods-hidden',
                    !visible
                );

                if (visible) {
                    visibleCount++;
                }
            });

            countLabel.textContent =
                visibleCount + ' van ' + rows.length + ' pods';
        };

        /*
         * Live filtering.
         */
        searchInput.addEventListener('input', applyFilters);
        typeSelect.addEventListener('change', applyFilters);
        storageSelect.addEventListener('change', applyFilters);
        connectionSelect.addEventListener('change', applyFilters);

        /*
         * Reset.
         */
        resetButton.addEventListener('click', function () {
            searchInput.value = '';
            typeSelect.value = '';
            storageSelect.value = '';
            connectionSelect.value = '';

            applyFilters();
            searchInput.focus();
        });

        /*
         * Initiële teller.
         */
        applyFilters();
    });
    </script>
    <?php
});
