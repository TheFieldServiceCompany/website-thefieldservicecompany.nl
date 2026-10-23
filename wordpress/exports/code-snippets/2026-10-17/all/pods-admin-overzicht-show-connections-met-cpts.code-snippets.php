<?php

/**
 * Pods Admin overzicht - Show connections met cpt's
 */
/**
 * TFSC - Connecties tonen in Pods Admin > Edit Pods
 *
 * DOEL
 * ============================================================
 * In:
 *
 *   Pods Admin > Edit Pods
 *
 * toont Pods standaard kolommen zoals:
 *
 *   Label | Name | Type | Storage Type | Groups | Fields
 *
 * Maar bij een Custom Taxonomy zie je daar niet direct aan welk
 * post type / CPT die taxonomy gekoppeld is.
 *
 * Deze snippet voegt daarom een extra kolom toe:
 *
 *   Connecties
 *
 * Voorbeelden:
 *
 *   Functie-Inhoud - Beroep
 *   → Beroepen
 *
 *   Functie-Inhoud - Vacature
 *   → Vacatures
 *
 * Als een taxonomy aan meerdere post types gekoppeld is:
 *
 *   Beroepen, Vacatures
 *
 *
 * HOE HET WERKT
 * ============================================================
 * WordPress bewaart bij iedere geregistreerde taxonomy de
 * gekoppelde post types in:
 *
 *   $taxonomy->object_type
 *
 * De PHP-code leest deze informatie rechtstreeks uit WordPress.
 *
 * Pods gebruikt voor "Edit Pods" geen normale WordPress
 * WP_List_Table-kolommen. Daarom voegen we de extra kolom alleen
 * visueel toe aan precies dit adminscherm.
 *
 *
 * BELANGRIJK
 * ============================================================
 * - Verandert GEEN Pods-instellingen.
 * - Verandert GEEN taxonomy-connecties.
 * - Verandert GEEN CPT's.
 * - Alleen zichtbaar in wp-admin.
 * - Werkt automatisch met alle geregistreerde taxonomies.
 * - Nieuwe of gewijzigde Pods-connecties worden automatisch
 *   weergegeven nadat de pagina opnieuw geladen wordt.
 * - Custom Post Type labels worden gebruikt in plaats van alleen
 *   technische namen.
 *
 *
 * CODE SNIPPETS
 * ============================================================
 * Deze snippet mag ingesteld worden op:
 *
 *   Only run in administration area
 */


add_action( 'admin_footer', function() {

    /*
     * Alleen uitvoeren op:
     *
     * Pods Admin > Edit Pods
     *
     * URL is normaal:
     * /wp-admin/admin.php?page=pods
     */
    if (
        ! isset( $_GET['page'] ) ||
        'pods' !== sanitize_key( $_GET['page'] )
    ) {
        return;
    }

    /*
     * Niet uitvoeren wanneer één individuele Pod wordt bewerkt.
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
     * Alle geregistreerde taxonomies ophalen.
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


        /*
         * Ieder gekoppeld post type omzetten naar
         * een leesbaar WordPress-label.
         */
        foreach ( $taxonomy->object_type as $post_type_name ) {

            $post_type = get_post_type_object(
                $post_type_name
            );

            if (
                $post_type &&
                ! empty( $post_type->labels->name )
            ) {
                $labels[] = $post_type->labels->name;
            } else {
                $labels[] = $post_type_name;
            }
        }


        $connections[ $taxonomy->name ] =
            array_values(
                array_unique( $labels )
            );
    }

    ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const connections =
            <?php echo wp_json_encode( $connections ); ?>;


        function addTfscConnectionsColumn() {

            /*
             * Pods-overzichtstabel zoeken.
             */
            const tables = document.querySelectorAll(
                '.wrap table'
            );

            let table = null;


            tables.forEach(function(candidate) {

                const headers =
                    Array.from(
                        candidate.querySelectorAll('thead th')
                    ).map(function(th) {
                        return th.textContent.trim();
                    });


                /*
                 * Controleren of dit daadwerkelijk de
                 * Edit Pods tabel is.
                 */
                if (
                    headers.includes('Label') &&
                    headers.includes('Name') &&
                    headers.includes('Type') &&
                    headers.includes('Storage Type')
                ) {
                    table = candidate;
                }
            });


            if (!table) {
                return;
            }


            /*
             * Voorkomen dat de kolom meerdere keren wordt
             * toegevoegd.
             */
            if (
                table.querySelector(
                    '.tfsc-pods-connections-column'
                )
            ) {
                return;
            }


            const headerRow =
                table.querySelector('thead tr');

            if (!headerRow) {
                return;
            }


            /*
             * Bepalen in welke kolom "Name" staat.
             */
            const headerCells =
                Array.from(
                    headerRow.querySelectorAll('th')
                );

            const nameIndex =
                headerCells.findIndex(function(th) {
                    return th.textContent.trim() === 'Name';
                });


            if (nameIndex === -1) {
                return;
            }


            /*
             * Nieuwe header toevoegen.
             */
            const newHeader =
                document.createElement('th');

            newHeader.textContent = 'Connecties';
            newHeader.className =
                'tfsc-pods-connections-column';

            headerRow.appendChild(newHeader);


            /*
             * Iedere Pods-rij verwerken.
             */
            const rows =
                table.querySelectorAll('tbody tr');


            rows.forEach(function(row) {

                const cells =
                    row.querySelectorAll('td');

                if (!cells.length) {
                    return;
                }


                const nameCell =
                    cells[nameIndex];

                if (!nameCell) {
                    return;
                }


                /*
                 * Technische Pods-name uit de Name-kolom.
                 */
                const podName =
                    nameCell.textContent.trim();


                const newCell =
                    document.createElement('td');

                newCell.className =
                    'tfsc-pods-connections-column';


                /*
                 * Alleen taxonomies hebben in onze PHP-map
                 * een taxonomy → post type connectie.
                 */
                if (
                    Object.prototype.hasOwnProperty.call(
                        connections,
                        podName
                    ) &&
                    connections[podName].length
                ) {

                    newCell.textContent =
                        connections[podName].join(', ');

                } else {

                    newCell.textContent = '—';
                }


                row.appendChild(newCell);
            });
        }


        addTfscConnectionsColumn();

    });
    </script>

    <?php
} );
