<?php

/**
 * Make Taxonomy Base Archives | Beroepengids [v3.0] - Optimized [CLONE]
 *
 * Maak base archives voor taxonomies. BELANGRIJK!! Er zit een allow list in deze snippet. Elke taxonomy waar een archive op moet, moet je hier tussenzetten! 
 */
/**
 * TFSC - Taxonomy Base Archives + Elementor Theme Builder Conditions
 *
 * ============================================================
 * MANUAL
 * ============================================================
 *
 * Deze snippet voegt een extra archive-niveau toe voor
 * geselecteerde taxonomies.
 *
 * WordPress kent standaard bijvoorbeeld:
 *
 * /beroepengids/functie-inhoud/inbedrijfstellen/
 *
 * maar niet automatisch:
 *
 * /beroepengids/functie-inhoud/
 *
 * Deze snippet maakt dat taxonomy-base archive mogelijk.
 *
 *
 * ============================================================
 * ALLOWLIST
 * ============================================================
 *
 * Alleen taxonomies die hieronder staan krijgen een
 * TFSC taxonomy-base archive.
 *
 * Gebruik de INTERNE taxonomy name, dus niet het label
 * en niet de rewrite slug.
 *
 * Voorbeeld:
 *
 * 'beroep_functienaam'
 *
 * Taxonomy toevoegen:
 * → naam toevoegen aan de array
 *
 * Taxonomy uitschakelen:
 * → naam uit de array verwijderen
 *
 * Na een wijziging in deze lijst of in rewrite slugs:
 *
 * WordPress
 * → Instellingen
 * → Permalinks
 * → Wijzigingen opslaan
 *
 *
 * ============================================================
 * URL
 * ============================================================
 *
 * De URL wordt automatisch bepaald door de rewrite slug
 * van de taxonomy.
 *
 * Bijvoorbeeld:
 *
 * interne taxonomy:
 * beroep_functie_inhoud
 *
 * rewrite slug:
 * beroepengids/functie-inhoud
 *
 * archive:
 * /beroepengids/functie-inhoud/
 *
 *
 * ============================================================
 * QUERY VAR
 * ============================================================
 *
 * Op een TFSC taxonomy-base archive bevat:
 *
 * get_query_var( 'tfsc_taxonomy_archive' )
 *
 * de interne taxonomy name.
 *
 *
 * ============================================================
 * ELEMENTOR
 * ============================================================
 *
 * Er worden twee Theme Builder conditions geregistreerd:
 *
 * TFSC Beroepengids Taxonomy Base Archives
 *
 * → /beroepengids/functie-inhoud/
 *
 * TFSC Beroepengids Taxonomy Term Archives
 *
 * → /beroepengids/functie-inhoud/inbedrijfstellen/
 *
 * Voor de Beroepengids conditions moet de rewrite slug
 * beginnen met:
 *
 * beroepengids/
 *
 *
 * ============================================================
 * BESTAANDE URLS
 * ============================================================
 *
 * Deze snippet verandert niet:
 *
 * - normale taxonomy term archives
 * - CPT archives
 * - singular posts / CPT's
 * - TFSC combo archives
 *
 * De rewrite matcht uitsluitend exact de taxonomy-basis.
 */


/**
 * ============================================================
 * ALLOWLIST
 * ============================================================
 *
 * Alleen deze taxonomies krijgen een taxonomy-base archive.
 *
 * VERVANG onderstaande voorbeelden door je echte interne
 * taxonomy names.
 */

function tfsc_taxonomy_base_archive_allowed(
    $taxonomy_name
) {

    $allowed = [
		'beroep_bedrijfssector',
        'beroep_functienaam',
        'beroep_functie-inhoud',
        'beroep_kennisgebied',
        'beroep_locatie',
		'beroep_opleidingsniveau',
		'beroep_werkgebied',
    ];

    return in_array(
        $taxonomy_name,
        $allowed,
        true
    );
}


/**
 * ============================================================
 * QUERY VAR
 * ============================================================
 */

add_filter(
    'query_vars',
    function( $vars ) {

        $vars[] =
            'tfsc_taxonomy_archive';

        return $vars;
    }
);


/**
 * ============================================================
 * TAXONOMY BASE REWRITE RULES
 * ============================================================
 *
 * De rewrite slug van WordPress / Pods blijft leidend.
 */

add_action(
    'init',
    function() {

        $taxonomies =
            get_taxonomies(
                [
                    'public' => true,
                ],
                'objects'
            );

        foreach (
            $taxonomies
            as $taxonomy
        ) {

            /**
             * Alleen taxonomies uit de allowlist.
             */
            if (
                ! tfsc_taxonomy_base_archive_allowed(
                    $taxonomy->name
                )
            ) {
                continue;
            }

            $slug =
                trim(
                    (string) (
                        $taxonomy->rewrite['slug']
                        ?? ''
                    ),
                    '/'
                );

            if ( '' === $slug ) {
                continue;
            }

            /**
             * Match alleen exact de taxonomy-basis.
             *
             * Wel:
             * /beroepengids/functie-inhoud/
             *
             * Niet:
             * /beroepengids/functie-inhoud/inbedrijfstellen/
             */
            add_rewrite_rule(
                '^'
                . preg_quote(
                    $slug,
                    '#'
                )
                . '/?$',
                'index.php?tfsc_taxonomy_archive='
                . urlencode(
                    $taxonomy->name
                ),
                'top'
            );
        }
    },
    99
);


/**
 * ============================================================
 * HELPERS
 * ============================================================
 */

/**
 * Geeft het taxonomy-object van het huidige
 * TFSC taxonomy-base archive terug.
 */

function tfsc_get_current_taxonomy_archive() {

    $name =
        get_query_var(
            'tfsc_taxonomy_archive'
        );

    if (
        ! $name ||
        ! tfsc_taxonomy_base_archive_allowed(
            $name
        )
    ) {
        return false;
    }

    $taxonomy =
        get_taxonomy(
            $name
        );

    return (
        $taxonomy &&
        ! empty( $taxonomy->public )
    )
        ? $taxonomy
        : false;
}


/**
 * Controleert of de huidige pagina een geldig
 * TFSC taxonomy-base archive is.
 */

function tfsc_is_taxonomy_base_archive() {

    return (bool)
        tfsc_get_current_taxonomy_archive();
}


/**
 * Controleert of een taxonomy bij Beroepengids hoort.
 *
 * Voor Elementor Theme Builder conditions gebruiken we
 * hiervoor de rewrite prefix:
 *
 * beroepengids/
 */

function tfsc_is_beroepengids_taxonomy(
    $taxonomy_name
) {

    $taxonomy =
        get_taxonomy(
            $taxonomy_name
        );

    if (
        ! $taxonomy ||
        empty( $taxonomy->public )
    ) {
        return false;
    }

    $slug =
        trim(
            (string) (
                $taxonomy->rewrite['slug']
                ?? ''
            ),
            '/'
        );

    return (
        $slug &&
        0 === strpos(
            $slug,
            'beroepengids/'
        )
    );
}


/**
 * Beroepengids taxonomy-base archive.
 */

function tfsc_is_beroepengids_taxonomy_base_archive() {

    $taxonomy =
        tfsc_get_current_taxonomy_archive();

    return (
        $taxonomy &&
        tfsc_is_beroepengids_taxonomy(
            $taxonomy->name
        )
    );
}


/**
 * Normaal Beroepengids taxonomy term archive.
 */

function tfsc_is_beroepengids_taxonomy_term_archive() {

    $term =
        get_queried_object();

    return (
        $term instanceof WP_Term &&
        tfsc_is_beroepengids_taxonomy(
            $term->taxonomy
        )
    );
}


/**
 * ============================================================
 * ARCHIVE QUERY STATUS
 * ============================================================
 *
 * Een taxonomy-base URL heeft geen echte WordPress post/page.
 * Daarom markeren we hem handmatig als archive.
 */

add_action(
    'parse_query',
    function( $query ) {

        $name =
            $query->get(
                'tfsc_taxonomy_archive'
            );

        if (
            ! $name ||
            ! tfsc_taxonomy_base_archive_allowed(
                $name
            )
        ) {
            return;
        }

        $taxonomy =
            get_taxonomy(
                $name
            );

        if (
            ! $taxonomy ||
            empty( $taxonomy->public )
        ) {
            return;
        }

        $query->is_home       = false;
        $query->is_front_page = false;
        $query->is_singular   = false;
        $query->is_page       = false;
        $query->is_single     = false;
        $query->is_search     = false;
        $query->is_404        = false;
        $query->is_archive    = true;
    },
    20
);


/**
 * ============================================================
 * 404 VOORKOMEN
 * ============================================================
 */

add_filter(
    'pre_handle_404',
    function(
        $preempt,
        $wp_query
    ) {

        $name =
            $wp_query->get(
                'tfsc_taxonomy_archive'
            );

        if (
            ! $name ||
            ! tfsc_taxonomy_base_archive_allowed(
                $name
            )
        ) {
            return $preempt;
        }

        $taxonomy =
            get_taxonomy(
                $name
            );

        if (
            ! $taxonomy ||
            empty( $taxonomy->public )
        ) {
            return $preempt;
        }

        $wp_query->is_404     = false;
        $wp_query->is_archive = true;

        status_header( 200 );

        return true;
    },
    10,
    2
);


/**
 * ============================================================
 * GEEN NORMALE POSTS OPHALEN
 * ============================================================
 *
 * Het taxonomy-base archive is bedoeld om taxonomy terms
 * te tonen. Elementor / TFSC term loops halen die zelf op.
 */

add_action(
    'pre_get_posts',
    function( $query ) {

        if (
            is_admin() ||
            ! $query->is_main_query()
        ) {
            return;
        }

        $name =
            $query->get(
                'tfsc_taxonomy_archive'
            );

        if (
            ! $name ||
            ! tfsc_taxonomy_base_archive_allowed(
                $name
            )
        ) {
            return;
        }

        $query->set(
            'post__in',
            [ 0 ]
        );
    },
    20
);


/**
 * ============================================================
 * ARCHIVE TITLE
 * ============================================================
 *
 * Gebruikt het plural taxonomy label.
 */

function tfsc_taxonomy_base_archive_title(
    $title
) {

    $taxonomy =
        tfsc_get_current_taxonomy_archive();

    return (
        $taxonomy &&
        ! empty(
            $taxonomy->labels->name
        )
    )
        ? $taxonomy->labels->name
        : $title;
}


add_filter(
    'get_the_archive_title',
    'tfsc_taxonomy_base_archive_title'
);

add_filter(
    'elementor/utils/get_the_archive_title',
    'tfsc_taxonomy_base_archive_title'
);


/**
 * ============================================================
 * BODY CLASSES
 * ============================================================
 *
 * Voorbeeld:
 *
 * tfsc-taxonomy-base-archive
 *
 * tfsc-taxonomy-base-archive--beroep_functienaam
 */

add_filter(
    'body_class',
    function( $classes ) {

        $taxonomy =
            tfsc_get_current_taxonomy_archive();

        if ( ! $taxonomy ) {
            return $classes;
        }

        $classes[] =
            'tfsc-taxonomy-base-archive';

        $classes[] =
            'tfsc-taxonomy-base-archive--'
            . sanitize_html_class(
                $taxonomy->name
            );

        return $classes;
    }
);


/**
 * ============================================================
 * ELEMENTOR THEME BUILDER CONDITIONS
 * ============================================================
 *
 * Registreert:
 *
 * 1. TFSC Beroepengids Taxonomy Base Archives
 * 2. TFSC Beroepengids Taxonomy Term Archives
 */

add_action(
    'elementor/theme/register_conditions',
    function( $conditions_manager ) {

        if (
            ! class_exists(
                '\ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base'
            )
        ) {
            return;
        }


        /**
         * Taxonomy Base Archives.
         */
        if (
            ! class_exists(
                'TFSC_Beroepengids_Taxonomy_Base_Condition'
            )
        ) {

            class TFSC_Beroepengids_Taxonomy_Base_Condition
                extends
                \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {

                public static function get_type(): string {
                    return 'archive';
                }

                public static function get_priority(): int {
                    return 90;
                }

                public function get_name(): string {
                    return 'tfsc_beroepengids_taxonomy_base_archive';
                }

                public function get_label(): string {
                    return 'TFSC Beroepengids Taxonomy Base Archives';
                }

                public function check( $args ): bool {
                    return
                        tfsc_is_beroepengids_taxonomy_base_archive();
                }
            }
        }


        /**
         * Taxonomy Term Archives.
         */
        if (
            ! class_exists(
                'TFSC_Beroepengids_Taxonomy_Term_Condition'
            )
        ) {

            class TFSC_Beroepengids_Taxonomy_Term_Condition
                extends
                \ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base {

                public static function get_type(): string {
                    return 'archive';
                }

                public static function get_priority(): int {
                    return 91;
                }

                public function get_name(): string {
                    return 'tfsc_beroepengids_taxonomy_term_archive';
                }

                public function get_label(): string {
                    return 'TFSC Beroepengids Taxonomy Term Archives';
                }

                public function check( $args ): bool {
                    return
                        tfsc_is_beroepengids_taxonomy_term_archive();
                }
            }
        }


        $archive =
            $conditions_manager
                ->get_condition(
                    'archive'
                );

        if ( ! $archive ) {
            return;
        }

        $archive->register_sub_condition(
            new TFSC_Beroepengids_Taxonomy_Base_Condition()
        );

        $archive->register_sub_condition(
            new TFSC_Beroepengids_Taxonomy_Term_Condition()
        );
    },
    100
);
