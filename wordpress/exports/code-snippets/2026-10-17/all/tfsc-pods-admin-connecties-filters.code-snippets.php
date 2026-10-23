<?php

/**
 *  TFSC - Pods Admin Connecties + Filters
 */
/**
 * TFSC - Pods Admin Connecties + Filters
 *
 * Voor:
 * Pods Admin > Edit Pods
 *
 * Functies:
 * - voegt kolom "Connecties" toe
 * - toont gekoppelde post types bij taxonomies
 * - zoekveld voor Label + Name
 * - filter op Type
 * - filter op Storage Type
 * - filter op Connectie
 * - filters kunnen gecombineerd worden
 * - resetknop
 *
 * Verandert niets aan Pods of de database.
 */

add_action( 'admin_footer', function() {

    /*
     * Alleen:
     * Pods Admin > Edit Pods
     */
    if (
        ! isset( $_GET['page'] ) ||
        'pods' !== sanitize_key( $_GET['page'] )
    ) {
        return;
    }


    /*
     * Niet uitvoeren wanneer een individuele Pod
     * wordt geopend/bewerkt.
     */
    if (
        isset( $_GET['action'] ) &&
        ! in_array(
            sanitize_key( $_GET['action'] ),
            [ '', 'manage' ],
            true
        )
    ) {
        return;
    }


    /*
     * ============================================
     * TAXONOMY → POST TYPE CONNECTIES OPHALEN
     * ============================================
     */

    $taxonomies = get_taxonomies(
        [],
        'objects'
    );

    $connections = [];


    foreach ( $taxonomies as $taxonomy ) {

        if (
            empty( $taxonomy->name ) ||
            empty( $taxonomy->object_type )
        ) {
            continue;
        }


        $labels = [];


        foreach ( $taxonomy->object_type as $post_type_name ) {

            $post_type = get_post_type_object(
                $post_type_name
            );


            if (
                $post_type &&
                ! empty( $post_type->labels->name )
            ) {

                $labels[] =
                    $post_type->labels->name;

            } else {

                $labels[] =
                    $post_type_name;
            }
        }


        $connections[ $taxonomy->name ] =
            array_values(
                array_unique( $labels )
            );
    }

    ?>

    <style>

        /*
         * ============================================
         * FILTERBALK
         * ============================================
         */

        #tfsc-pods-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;

            margin: 12px 0;
            padding: 12px;

            background: #fff;
            border: 1px solid #c3c4c7;

            box-sizing: border-box;
        }


        #tfsc-pods-filters input[type="search"] {
            min-width: 260px;
        }


        #tfsc-pods-filters select {
            min-width: 180px;
        }


        #tfsc-pods-filter-count {
            margin-left: 6px;

            color: #646970;
            font-size: 13px;
        }


        /*
         * Verborgen tabelrijen.
         */
        .tfsc-pods-hidden {
            display: none !important;
        }

    </style>


    <script>

    document.addEventListener(
        'DOMContentLoaded',
        function () {


            /*
             * PHP-connecties beschikbaar maken in JS.
             */
            const tfscConnections =
                <?php echo wp_json_encode( $connections ); ?>;



            /*
             * ============================================
             * PODS-TABEL ZOEKEN
             * ============================================
             */

            function findPodsTable() {

                const tables =
                    document.querySelectorAll(
                        '.wrap table'
                    );


                for (const table of tables) {

                    const headers =
                        Array.from(
                            table.querySelectorAll(
                                'thead th'
                            )
                        ).map(function(th) {

                            return th
                                .textContent
                                .trim()
                                .toLowerCase();

                        });


                    if (
                        headers.includes('label') &&
                        headers.includes('name') &&
                        headers.includes('type') &&
                        headers.includes('storage type')
                    ) {

                        return table;
                    }
                }


                return null;
            }



            const table =
                findPodsTable();


            if (!table) {

                console.log(
                    'TFSC Pods filter: Pods tabel niet gevonden.'
                );

                return;
            }



            /*
             * ============================================
             * CONNECTIES-KOLOM TOEVOEGEN
             * ============================================
             */

            function addConnectionsColumn() {


                const headerRow =
                    table.querySelector(
                        'thead tr'
                    );


                if (!headerRow) {
                    return;
                }


                /*
                 * Niet opnieuw toevoegen.
                 */
                if (
                    headerRow.querySelector(
                        '.tfsc-pods-connections-column'
                    )
                ) {
                    return;
                }


                const headerCells =
                    Array.from(
                        headerRow.querySelectorAll(
                            'th'
                        )
                    );


                const nameIndex =
                    headerCells.findIndex(
                        function(th) {

                            return th
                                .textContent
                                .trim()
                                .toLowerCase()
                                === 'name';
                        }
                    );


                if (nameIndex === -1) {
                    return;
                }



                /*
                 * Header.
                 */
                const newHeader =
                    document.createElement(
                        'th'
                    );


                newHeader.textContent =
                    'Connecties';


                newHeader.className =
                    'tfsc-pods-connections-column';


                headerRow.appendChild(
                    newHeader
                );



                /*
                 * Rijen vullen.
                 */
                const rows =
                    table.querySelectorAll(
                        'tbody tr'
                    );


                rows.forEach(
                    function(row) {


                        const cells =
                            row.querySelectorAll(
                                'td'
                            );


                        if (
                            ! cells.length ||
                            ! cells[nameIndex]
                        ) {
                            return;
                        }


                        const podName =
                            cells[nameIndex]
                                .textContent
                                .trim();


                        const newCell =
                            document.createElement(
                                'td'
                            );


                        newCell.className =
                            'tfsc-pods-connections-column';



                        if (
                            Object.prototype
                                .hasOwnProperty
                                .call(
                                    tfscConnections,
                                    podName
                                )
                            &&
                            tfscConnections[podName]
                                .length
                        ) {


                            newCell.textContent =
                                tfscConnections[podName]
                                    .join(', ');


                        } else {


                            newCell.textContent =
                                '—';
                        }


                        row.appendChild(
                            newCell
                        );
                    }
                );
            }



            /*
             * Eerst connecties toevoegen.
             */
            addConnectionsColumn();



            /*
             * ============================================
             * KOLOMNUMMERS BEPALEN
             * ============================================
             */

            const headers =
                Array.from(
                    table.querySelectorAll(
                        'thead th'
                    )
                );


            function getColumnIndex(name) {

                return headers.findIndex(
                    function(th) {

                        return th
                            .textContent
                            .trim()
                            .toLowerCase()
                            === name.toLowerCase();
                    }
                );
            }



            const columns = {

                label:
                    getColumnIndex(
                        'Label'
                    ),

                name:
                    getColumnIndex(
                        'Name'
                    ),

                type:
                    getColumnIndex(
                        'Type'
                    ),

                storage:
                    getColumnIndex(
                        'Storage Type'
                    ),

                connections:
                    getColumnIndex(
                        'Connecties'
                    )
            };



            /*
             * Veiligheidscontrole.
             */
            if (
                columns.label === -1 ||
                columns.name === -1 ||
                columns.type === -1 ||
                columns.storage === -1 ||
                columns.connections === -1
            ) {

                console.log(
                    'TFSC Pods filter: niet alle kolommen gevonden.',
                    columns
                );

                return;
            }



            const tbody =
                table.querySelector(
                    'tbody'
                );


            if (!tbody) {
                return;
            }


            const rows =
                Array.from(
                    tbody.querySelectorAll(
                        'tr'
                    )
                );


            if (!rows.length) {
                return;
            }



            /*
             * ============================================
             * HULPFUNCTIES
             * ============================================
             */

            function getCellText(
                row,
                index
            ) {


                const cells =
                    row.querySelectorAll(
                        'td'
                    );


                if (!cells[index]) {
                    return '';
                }


                return cells[index]
                    .textContent
                    .trim();
            }



            /*
             * Voor Type en Storage gebruiken we
             * gewoon de volledige celwaarde.
             */
            function getUniqueValues(index) {


                const values =
                    new Set();


                rows.forEach(
                    function(row) {


                        const value =
                            getCellText(
                                row,
                                index
                            );


                        if (
                            value &&
                            value !== '—' &&
                            value !== '-'
                        ) {

                            values.add(
                                value
                            );
                        }
                    }
                );


                return Array
                    .from(values)
                    .sort(
                        function(a, b) {

                            return a.localeCompare(
                                b,
                                undefined,
                                {
                                    numeric: true,
                                    sensitivity: 'base'
                                }
                            );
                        }
                    );
            }



            /*
             * Connecties kunnen meerdere waarden hebben:
             *
             * Beroepen, Vacatures
             *
             * Daarom splitsen we deze apart.
             */
            function getUniqueConnections() {


                const values =
                    new Set();


                rows.forEach(
                    function(row) {


                        const text =
                            getCellText(
                                row,
                                columns.connections
                            );


                        if (
                            !text ||
                            text === '—' ||
                            text === '-'
                        ) {
                            return;
                        }


                        text
                            .split(',')
                            .map(
                                function(value) {

                                    return value.trim();
                                }
                            )
                            .filter(Boolean)
                            .forEach(
                                function(value) {

                                    values.add(
                                        value
                                    );
                                }
                            );
                    }
                );


                return Array
                    .from(values)
                    .sort(
                        function(a, b) {

                            return a.localeCompare(
                                b,
                                undefined,
                                {
                                    numeric: true,
                                    sensitivity: 'base'
                                }
                            );
                        }
                    );
            }



            /*
             * ============================================
             * FILTERBALK MAKEN
             * ============================================
             */

            const filterBar =
                document.createElement(
                    'div'
                );


            filterBar.id =
                'tfsc-pods-filters';



            /*
             * Zoekveld
             */
            const searchInput =
                document.createElement(
                    'input'
                );


            searchInput.type =
                'search';


            searchInput.placeholder =
                'Zoek Label of Name…';



            /*
             * Type
             */
            const typeSelect =
                document.createElement(
                    'select'
                );



            /*
             * Storage
             */
            const storageSelect =
                document.createElement(
                    'select'
                );



            /*
             * Connecties
             */
            const connectionSelect =
                document.createElement(
                    'select'
                );



            /*
             * Reset
             */
            const resetButton =
                document.createElement(
                    'button'
                );


            resetButton.type =
                'button';


            resetButton.className =
                'button';


            resetButton.textContent =
                'Reset';



            /*
             * Teller
             */
            const countLabel =
                document.createElement(
                    'span'
                );


            countLabel.id =
                'tfsc-pods-filter-count';



            /*
             * ============================================
             * DROPDOWNS VULLEN
             * ============================================
             */

            function populateSelect(
                select,
                defaultText,
                values
            ) {


                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    '';


                option.textContent =
                    defaultText;


                select.appendChild(
                    option
                );



                values.forEach(
                    function(value) {


                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value =
                            value;


                        option.textContent =
                            value;


                        select.appendChild(
                            option
                        );
                    }
                );
            }



            populateSelect(
                typeSelect,
                'Alle types',
                getUniqueValues(
                    columns.type
                )
            );


            populateSelect(
                storageSelect,
                'Alle storage types',
                getUniqueValues(
                    columns.storage
                )
            );


            populateSelect(
                connectionSelect,
                'Alle connecties',
                getUniqueConnections()
            );



            /*
             * Elementen toevoegen.
             */
            filterBar.appendChild(
                searchInput
            );


            filterBar.appendChild(
                typeSelect
            );


            filterBar.appendChild(
                storageSelect
            );


            filterBar.appendChild(
                connectionSelect
            );


            filterBar.appendChild(
                resetButton
            );


            filterBar.appendChild(
                countLabel
            );



            /*
             * Direct boven de tabel plaatsen.
             */
            table.parentNode.insertBefore(
                filterBar,
                table
            );



            /*
             * ============================================
             * FILTERLOGICA
             * ============================================
             */

            function applyFilters() {


                const search =
                    searchInput
                        .value
                        .trim()
                        .toLowerCase();


                const selectedType =
                    typeSelect.value;


                const selectedStorage =
                    storageSelect.value;


                const selectedConnection =
                    connectionSelect.value;



                let visible =
                    0;



                rows.forEach(
                    function(row) {


                        const label =
                            getCellText(
                                row,
                                columns.label
                            );


                        const name =
                            getCellText(
                                row,
                                columns.name
                            );


                        const type =
                            getCellText(
                                row,
                                columns.type
                            );


                        const storage =
                            getCellText(
                                row,
                                columns.storage
                            );


                        const connectionText =
                            getCellText(
                                row,
                                columns.connections
                            );


                        /*
                         * Label + Name doorzoeken.
                         */
                        const searchable =
                            (
                                label +
                                ' ' +
                                name
                            )
                            .toLowerCase();



                        const matchesSearch =
                            !search ||
                            searchable.includes(
                                search
                            );



                        const matchesType =
                            !selectedType ||
                            type === selectedType;



                        const matchesStorage =
                            !selectedStorage ||
                            storage === selectedStorage;



                        /*
                         * Connecties apart splitsen zodat:
                         *
                         * "Beroepen, Vacatures"
                         *
                         * ook gevonden wordt wanneer alleen
                         * "Vacatures" geselecteerd wordt.
                         */
                        const rowConnections =
                            connectionText
                                .split(',')
                                .map(
                                    function(value) {

                                        return value.trim();
                                    }
                                );


                        const matchesConnection =
                            !selectedConnection ||
                            rowConnections.includes(
                                selectedConnection
                            );



                        /*
                         * AND-logica:
                         *
                         * alles moet overeenkomen.
                         */
                        const matches =
                            matchesSearch &&
                            matchesType &&
                            matchesStorage &&
                            matchesConnection;



                        row.classList.toggle(
                            'tfsc-pods-hidden',
                            !matches
                        );



                        if (matches) {
                            visible++;
                        }
                    }
                );



                countLabel.textContent =
                    visible +
                    ' van ' +
                    rows.length +
                    ' pods';
            }



            /*
             * ============================================
             * EVENTS
             * ============================================
             */

            searchInput.addEventListener(
                'input',
                applyFilters
            );


            typeSelect.addEventListener(
                'change',
                applyFilters
            );


            storageSelect.addEventListener(
                'change',
                applyFilters
            );


            connectionSelect.addEventListener(
                'change',
                applyFilters
            );



            /*
             * Reset.
             */
            resetButton.addEventListener(
                'click',
                function() {


                    searchInput.value =
                        '';


                    typeSelect.value =
                        '';


                    storageSelect.value =
                        '';


                    connectionSelect.value =
                        '';


                    applyFilters();


                    searchInput.focus();
                }
            );



            /*
             * Teller initialiseren.
             */
            applyFilters();

        }
    );

    </script>

    <?php

} );
