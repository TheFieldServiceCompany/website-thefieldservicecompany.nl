<?php

/**
 * Vac Combo Archive | Seo & Cononical Code [v2.2] Updated for Base Code with pagination
 *
 * Deze functionaliteit heeft het aan / uit staan
 * 
 * Level 1 — native vacature-taxonomy archives: aan, index/follow, canonical naar equivalente combo.
 * Level 2 — combo met functienaam: aan, index/follow als max. 3 taxonomies en minimaal 1 resultaat.
 * Level 3 — combo zonder functienaam: uit voor indexatie, noindex/follow.
 * Level 4 — combo met meer dan 3 taxonomies: uit voor indexatie, noindex/follow.
 * Search &amp; Filter query-URL’s onder /vacatures/?_...: uit voor indexatie, noindex/follow, canonical naar /vacatures/.
 *
 */
/**
 * TFSC - Combo Archive SEO + Native Archive Canonicals + Filter SEO
 * Version 4.3 - Location Combo Support
 *
 * Vereist:
 * - TFSC - Volledig dynamische vacature-archieven
 * - Rank Math SEO
 *
 * ============================================================
 * REGELT
 * ============================================================
 *
 * COMBO ARCHIVES
 * - SEO title
 * - Meta description
 * - Canonical
 * - Robots
 * - Correcte canonical voor /page/N/
 * - Correcte OpenGraph URL voor /page/N/
 * - Correcte CollectionPage schema URL voor /page/N/
 * - Correcte rel prev / next
 * - Centrale indexeringslogica voor SEO + sitemap
 *
 * INDEXERINGSLOGICA
 * - functienaam-combo: index/follow
 * - locatie-only combo: index/follow
 * - overige combo zonder functienaam: noindex/follow
 * - meer dan 3 taxonomies: noindex/follow
 * - minimaal 1 resultaat vereist
 *
 * NATIVE VACATURE TAXONOMY ARCHIVES
 * - blijven bereikbaar
 * - index/follow
 * - functienaam → equivalente combo
 * - locatie → equivalente locatie-only combo
 * - overige taxonomies → legacy FSE-combo
 * - pagination blijft behouden
 *
 * SEARCH & FILTER URLS OP /vacatures/
 * - noindex, follow
 * - canonical naar schone /vacatures/
 */


/* ============================================================
 * CONFIG
 * ============================================================ */

if ( ! defined( 'TFSC_COMBO_SEO_BRAND' ) ) {

    define(
        'TFSC_COMBO_SEO_BRAND',
        'The Field Service Company'
    );
}


if ( ! defined( 'TFSC_COMBO_SEO_MAX_TAXONOMIES' ) ) {

    define(
        'TFSC_COMBO_SEO_MAX_TAXONOMIES',
        3
    );
}


if (
    ! defined(
        'TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY'
    )
) {

    define(
        'TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY',
        'functienaam'
    );
}


if (
    ! defined(
        'TFSC_NATIVE_CANONICAL_FUNCTION_SLUG'
    )
) {

    define(
        'TFSC_NATIVE_CANONICAL_FUNCTION_SLUG',
        'field-service-engineer'
    );
}


if (
    ! defined(
        'TFSC_COMBO_SEO_LOCATION_TAXONOMY'
    )
) {

    define(
        'TFSC_COMBO_SEO_LOCATION_TAXONOMY',
        'locatie'
    );
}


/* ============================================================
 * HUIDIGE PAGINA
 * ============================================================ */

function tfsc_combo_seo_get_paged() {

    return max(
        1,
        (int) get_query_var(
            'paged'
        )
    );
}


/* ============================================================
 * GELDIG COMBO ARCHIVE?
 * ============================================================ */

function tfsc_combo_seo_is_active() {

    if ( is_admin() ) {
        return false;
    }


    if (
        ! function_exists(
            'tfsc_is_combo_archive'
        )
        ||
        ! function_exists(
            'tfsc_get_combo_archive'
        )
    ) {
        return false;
    }


    if (
        ! tfsc_is_combo_archive()
    ) {
        return false;
    }


    return (
        false !==
        tfsc_get_combo_archive()
    );
}


/* ============================================================
 * COMBO VOLGORDE
 * ============================================================ */

function tfsc_combo_seo_order_combo(
    array $combo
) {

    $function = [];
    $others   = [];


    foreach (
        $combo
        as $item
    ) {

        if (
            ! empty(
                $item['taxonomy']
            )
            &&
            TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY
                ===
            $item['taxonomy']
        ) {

            $function[] =
                $item;

        } else {

            $others[] =
                $item;
        }
    }


    return array_merge(
        $function,
        $others
    );
}


/* ============================================================
 * COMBO ITEMS
 * ============================================================ */

function tfsc_combo_seo_get_items() {

    static $resolved = false;
    static $items    = [];


    if ( $resolved ) {
        return $items;
    }


    $resolved =
        true;


    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return [];
    }


    $combo =
        tfsc_get_combo_archive();


    if (
        ! is_array(
            $combo
        )
    ) {
        return [];
    }


    $combo =
        tfsc_combo_seo_order_combo(
            $combo
        );


    foreach (
        $combo
        as $item
    ) {

        if (
            empty(
                $item['taxonomy']
            )
            ||
            empty(
                $item['term']
            )
            ||
            ! $item['term']
                instanceof WP_Term
        ) {
            continue;
        }


        $name =
            trim(
                wp_strip_all_tags(
                    $item['term']->name
                )
            );


        if (
            '' === $name
        ) {
            continue;
        }


        $items[] = [
            'taxonomy' =>
                (string)
                $item['taxonomy'],

            'term_id' =>
                (int)
                $item['term']->term_id,

            'slug' =>
                (string)
                $item['term']->slug,

            'name' =>
                $name,
        ];
    }


    return $items;
}


/* ============================================================
 * AANTAL VERSCHILLENDE TAXONOMIES
 * ============================================================ */

function tfsc_combo_count_taxonomies(
    $combo = null
) {

    if (
        null === $combo
    ) {

        if (
            ! tfsc_combo_seo_is_active()
        ) {
            return 0;
        }


        $combo =
            tfsc_get_combo_archive();
    }


    if (
        ! is_array(
            $combo
        )
    ) {
        return 0;
    }


    $taxonomies =
        [];


    foreach (
        $combo
        as $item
    ) {

        if (
            empty(
                $item['taxonomy']
            )
        ) {
            continue;
        }


        $taxonomy =
            sanitize_key(
                $item['taxonomy']
            );


        if (
            '' === $taxonomy
        ) {
            continue;
        }


        $taxonomies[
            $taxonomy
        ] = true;
    }


    return count(
        $taxonomies
    );
}


/* ============================================================
 * HEEFT COMBO EEN FUNCTIENAAM?
 * ============================================================ */

function tfsc_combo_has_function_name(
    $combo = null
) {

    if (
        null === $combo
    ) {

        if (
            ! tfsc_combo_seo_is_active()
        ) {
            return false;
        }


        $combo =
            tfsc_get_combo_archive();
    }


    if (
        ! is_array(
            $combo
        )
    ) {
        return false;
    }


    foreach (
        $combo
        as $item
    ) {

        if (
            ! empty(
                $item['taxonomy']
            )
            &&
            TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY
                ===
            $item['taxonomy']
        ) {
            return true;
        }
    }


    return false;
}


/* ============================================================
 * HEEFT COMBO EEN LOCATIE?
 * ============================================================ */

function tfsc_combo_has_location(
    $combo = null
) {

    if (
        null === $combo
    ) {

        if (
            ! tfsc_combo_seo_is_active()
        ) {
            return false;
        }


        $combo =
            tfsc_get_combo_archive();
    }


    if (
        ! is_array(
            $combo
        )
    ) {
        return false;
    }


    foreach (
        $combo
        as $item
    ) {

        if (
            ! empty(
                $item['taxonomy']
            )
            &&
            TFSC_COMBO_SEO_LOCATION_TAXONOMY
                ===
            $item['taxonomy']
        ) {
            return true;
        }
    }


    return false;
}


/* ============================================================
 * RESULTAAT-AANTAL HUIDIGE FRONTEND COMBO
 * ============================================================ */

function tfsc_combo_get_current_result_count() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return 0;
    }


    global $wp_query;


    if (
        ! $wp_query
            instanceof WP_Query
    ) {
        return 0;
    }


    return max(
        0,
        (int)
        $wp_query->found_posts
    );
}


/* ============================================================
 * CENTRALE INDEXERINGSSTATUS
 * ============================================================
 *
 * Regels:
 *
 * - combo met functienaam: toegestaan
 * - locatie-only combo: toegestaan
 * - overige combo zonder functienaam: noindex
 * - maximaal ingestelde aantal taxonomies
 * - frontend moet minimaal 1 resultaat hebben
 */

function tfsc_combo_get_index_status(
    $combo = null
) {

    $is_frontend_combo =
        null === $combo;


    if (
        $is_frontend_combo
    ) {

        if (
            ! tfsc_combo_seo_is_active()
        ) {

            return [
                'index' => false,
                'reason' => 'not_combo',
                'results' => 0,
                'taxonomies' => 0,
            ];
        }


        $combo =
            tfsc_get_combo_archive();
    }


    if (
        ! is_array(
            $combo
        )
        ||
        ! $combo
    ) {

        return [
            'index' => false,
            'reason' => 'invalid_combo',
            'results' => 0,
            'taxonomies' => 0,
        ];
    }


    $taxonomy_count =
        tfsc_combo_count_taxonomies(
            $combo
        );


    if (
        $taxonomy_count
        >
        TFSC_COMBO_SEO_MAX_TAXONOMIES
    ) {

        return [
            'index' => false,
            'reason' => 'too_many_taxonomies',
            'results' => null,
            'taxonomies' => $taxonomy_count,
        ];
    }


    $has_function =
        tfsc_combo_has_function_name(
            $combo
        );


    $has_location =
        tfsc_combo_has_location(
            $combo
        );


    $is_location_only =
        (
            1 === $taxonomy_count
            &&
            $has_location
        );


    if (
        ! $has_function
        &&
        ! $is_location_only
    ) {

        return [
            'index' => false,
            'reason' => 'no_function_name',
            'results' => null,
            'taxonomies' => $taxonomy_count,
        ];
    }


    if (
        $is_frontend_combo
    ) {

        $result_count =
            tfsc_combo_get_current_result_count();


        if (
            $result_count < 1
        ) {

            return [
                'index' => false,
                'reason' => 'no_results',
                'results' => $result_count,
                'taxonomies' => $taxonomy_count,
            ];
        }


        return [
            'index' => true,
            'reason' =>
                $is_location_only
                    ? 'valid_location_combo'
                    : 'valid_function_combo',

            'results' => $result_count,
            'taxonomies' => $taxonomy_count,
        ];
    }


    return [
        'index' => true,

        'reason' =>
            $is_location_only
                ? 'valid_sitemap_location_combo'
                : 'valid_sitemap_function_combo',

        'results' => null,
        'taxonomies' => $taxonomy_count,
    ];
}


/* ============================================================
 * CENTRALE TRUE/FALSE INDEXERINGSFUNCTIE
 * ============================================================ */

function tfsc_combo_should_index(
    $combo = null
) {

    $status =
        tfsc_combo_get_index_status(
            $combo
        );


    return ! empty(
        $status['index']
    );
}


/* ============================================================
 * SEO TITLE COMBO ARCHIVES
 * ============================================================ */

function tfsc_combo_seo_build_title() {

    $items =
        tfsc_combo_seo_get_items();


    if (
        ! $items
    ) {
        return '';
    }


    $function_names =
        [];

    $other_names =
        [];


    foreach (
        $items
        as $item
    ) {

        if (
            TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY
            ===
            $item['taxonomy']
        ) {

            if (
                ! in_array(
                    $item['name'],
                    $function_names,
                    true
                )
            ) {

                $function_names[] =
                    $item['name'];
            }


            continue;
        }


        if (
            ! in_array(
                $item['name'],
                $other_names,
                true
            )
        ) {

            $other_names[] =
                $item['name'];
        }
    }


    if (
        $function_names
    ) {

        $title =
            implode(
                ' ',
                $function_names
            )
            .
            ' vacatures';


        if (
            $other_names
        ) {

            $title .=
                ' '
                .
                implode(
                    ' ',
                    $other_names
                );
        }


        return trim(
            $title
        );
    }


    if (
        ! $other_names
    ) {
        return '';
    }


    return trim(
        'Field Service vacatures '
        .
        implode(
            ' ',
            $other_names
        )
    );
}


/* ============================================================
 * META DESCRIPTION COMBO ARCHIVES
 * ============================================================ */

function tfsc_combo_seo_build_description() {

    $title =
        tfsc_combo_seo_build_title();


    if (
        '' === $title
    ) {
        return '';
    }


    return sprintf(
        '%s bij %s.',
        $title,
        TFSC_COMBO_SEO_BRAND
    );
}


/* ============================================================
 * BASIS COMBO URL
 * ============================================================ */

function tfsc_combo_seo_build_base_url() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return '';
    }


    if (
        ! defined(
            'TFSC_COMBO_BASE'
        )
    ) {
        return '';
    }


    $combo =
        tfsc_get_combo_archive();


    if (
        ! is_array(
            $combo
        )
    ) {
        return '';
    }


    $combo =
        tfsc_combo_seo_order_combo(
            $combo
        );


    $segments =
        [];


    foreach (
        $combo
        as $item
    ) {

        if (
            empty(
                $item['taxonomy']
            )
            ||
            empty(
                $item['path']
            )
            ||
            ! is_array(
                $item['path']
            )
        ) {
            return '';
        }


        $taxonomy =
            sanitize_key(
                $item['taxonomy']
            );


        if (
            '' === $taxonomy
        ) {
            return '';
        }


        $segments[] =
            $taxonomy;


        foreach (
            $item['path']
            as $term
        ) {

            if (
                ! $term
                    instanceof WP_Term
                ||
                $term->taxonomy
                    !==
                $taxonomy
            ) {
                return '';
            }


            $segments[] =
                $term->slug;
        }
    }


    if (
        ! $segments
    ) {
        return '';
    }


    $path =
        TFSC_COMBO_BASE
        .
        '/'
        .
        implode(
            '/',
            array_map(
                'rawurlencode',
                $segments
            )
        );


    return trailingslashit(
        home_url(
            '/' . $path
        )
    );
}


/* ============================================================
 * PAGINATED COMBO URL
 * ============================================================ */

function tfsc_combo_seo_build_page_url(
    $paged = 1
) {

    $base_url =
        tfsc_combo_seo_build_base_url();


    if (
        '' === $base_url
    ) {
        return '';
    }


    $paged =
        max(
            1,
            (int) $paged
        );


    if (
        1 === $paged
    ) {
        return $base_url;
    }


    return trailingslashit(
        $base_url
        .
        'page/'
        .
        $paged
    );
}


/* ============================================================
 * CANONICAL COMBO ARCHIVES
 * ============================================================ */

function tfsc_combo_seo_build_canonical() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return '';
    }


    return tfsc_combo_seo_build_page_url(
        tfsc_combo_seo_get_paged()
    );
}


/* ============================================================
 * CENTRAAL SEO OBJECT COMBO ARCHIVES
 * ============================================================ */

function tfsc_get_combo_seo() {

    static $resolved = false;
    static $seo      = false;


    if (
        $resolved
    ) {
        return $seo;
    }


    $resolved =
        true;


    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return false;
    }


    $title =
        tfsc_combo_seo_build_title();


    if (
        '' === $title
    ) {
        return false;
    }


    $index_status =
        tfsc_combo_get_index_status();


    $seo = [
        'title' =>
            $title,

        'description' =>
            tfsc_combo_seo_build_description(),

        'canonical' =>
            tfsc_combo_seo_build_canonical(),

        'index' =>
            ! empty(
                $index_status['index']
            ),

        'index_reason' =>
            $index_status['reason'],

        'results' =>
            $index_status['results'],

        'taxonomies' =>
            $index_status['taxonomies'],

        'paged' =>
            tfsc_combo_seo_get_paged(),
    ];


    return $seo;
}


/* ============================================================
 * COMBO MAX NUM PAGES
 * ============================================================ */

function tfsc_combo_seo_get_max_pages() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return 1;
    }


    global $wp_query;


    if (
        ! $wp_query
            instanceof WP_Query
    ) {
        return 1;
    }


    return max(
        1,
        (int)
        $wp_query->max_num_pages
    );
}


/* ============================================================
 * PREV URL
 * ============================================================ */

function tfsc_combo_seo_get_prev_url() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return '';
    }


    $paged =
        tfsc_combo_seo_get_paged();


    if (
        $paged <= 1
    ) {
        return '';
    }


    return tfsc_combo_seo_build_page_url(
        $paged - 1
    );
}


/* ============================================================
 * NEXT URL
 * ============================================================ */

function tfsc_combo_seo_get_next_url() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return '';
    }


    $paged =
        tfsc_combo_seo_get_paged();


    $max_pages =
        tfsc_combo_seo_get_max_pages();


    if (
        $paged >= $max_pages
    ) {
        return '';
    }


    return tfsc_combo_seo_build_page_url(
        $paged + 1
    );
}


/* ============================================================
 * NATIVE VACATURE TAXONOMY ARCHIVE?
 * ============================================================ */

function tfsc_native_canonical_is_target() {

    if (
        is_admin()
    ) {
        return false;
    }


    if (
        ! is_tax()
        ||
        (
            function_exists(
                'tfsc_is_combo_archive'
            )
            &&
            tfsc_is_combo_archive()
        )
    ) {
        return false;
    }


    $term =
        get_queried_object();


    if (
        ! $term
            instanceof WP_Term
    ) {
        return false;
    }


    if (
        ! function_exists(
            'tfsc_combo_taxonomy_allowed'
        )
        ||
        ! tfsc_combo_taxonomy_allowed(
            $term->taxonomy
        )
    ) {
        return false;
    }


    return true;
}


/* ============================================================
 * VOLLEDIG NATIVE TERM-PAD
 * ============================================================ */

function tfsc_native_canonical_get_term_path(
    WP_Term $term
) {

    $taxonomy =
        get_taxonomy(
            $term->taxonomy
        );


    if (
        ! $taxonomy
    ) {
        return [];
    }


    if (
        ! $taxonomy->hierarchical
    ) {
        return [
            $term,
        ];
    }


    $path =
        [];


    $ancestor_ids =
        array_reverse(
            get_ancestors(
                $term->term_id,
                $term->taxonomy,
                'taxonomy'
            )
        );


    foreach (
        $ancestor_ids
        as $ancestor_id
    ) {

        $ancestor =
            get_term(
                $ancestor_id,
                $term->taxonomy
            );


        if (
            $ancestor
                instanceof WP_Term
            &&
            ! is_wp_error(
                $ancestor
            )
        ) {

            $path[] =
                $ancestor;
        }
    }


    $path[] =
        $term;


    return $path;
}


/* ============================================================
 * NATIVE ARCHIVE → COMBO CANONICAL
 * ============================================================ */

function tfsc_native_canonical_build_url() {

    if (
        ! tfsc_native_canonical_is_target()
    ) {
        return '';
    }


    if (
        ! defined(
            'TFSC_COMBO_BASE'
        )
    ) {
        return '';
    }


    $native_term =
        get_queried_object();


    if (
        ! $native_term
            instanceof WP_Term
    ) {
        return '';
    }


    $native_path =
        tfsc_native_canonical_get_term_path(
            $native_term
        );


    if (
        ! $native_path
    ) {
        return '';
    }


    $segments =
        [];


    /**
     * Native functienaam archive.
     */

    if (
        TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY
        ===
        $native_term->taxonomy
    ) {

        $segments[] =
            TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY;


        foreach (
            $native_path
            as $path_term
        ) {

            if (
                ! $path_term
                    instanceof WP_Term
            ) {
                return '';
            }


            $segments[] =
                $path_term->slug;
        }


    /**
     * Native locatie archive.
     *
     * Canonical rechtstreeks naar locatie-only combo.
     */

    } elseif (
        TFSC_COMBO_SEO_LOCATION_TAXONOMY
        ===
        $native_term->taxonomy
    ) {

        $segments[] =
            TFSC_COMBO_SEO_LOCATION_TAXONOMY;


        foreach (
            $native_path
            as $path_term
        ) {

            if (
                ! $path_term
                    instanceof WP_Term
            ) {
                return '';
            }


            $segments[] =
                $path_term->slug;
        }


    /**
     * Overige native taxonomies:
     * legacy koppeling aan Field Service Engineer.
     */

    } else {

        $function_term =
            get_term_by(
                'slug',
                TFSC_NATIVE_CANONICAL_FUNCTION_SLUG,
                TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY
            );


        if (
            ! $function_term
                instanceof WP_Term
            ||
            is_wp_error(
                $function_term
            )
        ) {
            return '';
        }


        $segments = [
            TFSC_NATIVE_CANONICAL_FUNCTION_TAXONOMY,
            $function_term->slug,
            $native_term->taxonomy,
        ];


        foreach (
            $native_path
            as $path_term
        ) {

            if (
                ! $path_term
                    instanceof WP_Term
            ) {
                return '';
            }


            $segments[] =
                $path_term->slug;
        }
    }


    if (
        ! $segments
    ) {
        return '';
    }


    $path =
        TFSC_COMBO_BASE
        .
        '/'
        .
        implode(
            '/',
            array_map(
                'rawurlencode',
                $segments
            )
        );


    $url =
        trailingslashit(
            home_url(
                '/' . $path
            )
        );


    $paged =
        max(
            1,
            (int)
            get_query_var(
                'paged'
            )
        );


    if (
        $paged > 1
    ) {

        $url =
            trailingslashit(
                $url
                .
                'page/'
                .
                $paged
            );
    }


    return $url;
}


/* ============================================================
 * SEARCH & FILTER URL HERKENNEN
 * ============================================================ */

function tfsc_vacancy_filter_seo_is_active() {

    if (
        is_admin()
    ) {
        return false;
    }


    if (
        function_exists(
            'tfsc_is_combo_archive'
        )
        &&
        tfsc_is_combo_archive()
    ) {
        return false;
    }


    if (
        ! defined(
            'TFSC_VACANCY_POST_TYPE'
        )
    ) {
        return false;
    }


    if (
        ! is_post_type_archive(
            TFSC_VACANCY_POST_TYPE
        )
    ) {
        return false;
    }


    if (
        empty(
            $_GET
        )
    ) {
        return false;
    }


    foreach (
        array_keys(
            $_GET
        )
        as $key
    ) {

        $key =
            (string)
            $key;


        if (
            '' !== $key
            &&
            '_' === $key[0]
        ) {
            return true;
        }
    }


    return false;
}


/* ============================================================
 * SCHONE VACATURE CANONICAL
 * ============================================================ */

function tfsc_vacancy_filter_clean_canonical() {

    if (
        ! defined(
            'TFSC_VACANCY_POST_TYPE'
        )
    ) {
        return '';
    }


    $url =
        get_post_type_archive_link(
            TFSC_VACANCY_POST_TYPE
        );


    if (
        ! $url
    ) {
        return '';
    }


    return trailingslashit(
        $url
    );
}


/* ============================================================
 * RANK MATH CALLBACKS - COMBO
 * ============================================================ */

function tfsc_combo_rankmath_title(
    $title
) {

    $seo =
        tfsc_get_combo_seo();


    if (
        ! is_array(
            $seo
        )
        ||
        empty(
            $seo['title']
        )
    ) {
        return $title;
    }


    return $seo['title'];
}


function tfsc_combo_rankmath_description(
    $description
) {

    $seo =
        tfsc_get_combo_seo();


    if (
        ! is_array(
            $seo
        )
        ||
        empty(
            $seo['description']
        )
    ) {
        return $description;
    }


    return $seo['description'];
}


function tfsc_combo_rankmath_canonical(
    $canonical
) {

    $seo =
        tfsc_get_combo_seo();


    if (
        ! is_array(
            $seo
        )
        ||
        empty(
            $seo['canonical']
        )
    ) {
        return $canonical;
    }


    return $seo['canonical'];
}


function tfsc_combo_rankmath_robots(
    $robots
) {

    $seo =
        tfsc_get_combo_seo();


    if (
        ! is_array(
            $seo
        )
    ) {
        return $robots;
    }


    if (
        ! is_array(
            $robots
        )
    ) {
        $robots = [];
    }


    if (
        ! empty(
            $seo['index']
        )
    ) {

        $robots['index'] =
            'index';

        $robots['follow'] =
            'follow';

    } else {

        $robots['index'] =
            'noindex';

        $robots['follow'] =
            'follow';
    }


    return $robots;
}


/* ============================================================
 * RANK MATH CALLBACK - NATIVE ROBOTS
 * ============================================================ */

function tfsc_native_rankmath_robots(
    $robots
) {

    if (
        ! tfsc_native_canonical_is_target()
    ) {
        return $robots;
    }


    if (
        ! is_array(
            $robots
        )
    ) {
        $robots = [];
    }


    $robots['index'] =
        'index';

    $robots['follow'] =
        'follow';


    return $robots;
}


/* ============================================================
 * RANK MATH OPEN GRAPH URL
 * ============================================================ */

function tfsc_combo_rankmath_og_url(
    $url
) {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return $url;
    }


    $canonical =
        tfsc_combo_seo_build_canonical();


    return (
        '' !== $canonical
            ? $canonical
            : $url
    );
}


/* ============================================================
 * RANK MATH JSON-LD
 * ============================================================ */

function tfsc_combo_rankmath_json_ld(
    $data,
    $jsonld = null
) {

    if (
        ! tfsc_combo_seo_is_active()
        ||
        ! is_array(
            $data
        )
    ) {
        return $data;
    }


    $current_url =
        tfsc_combo_seo_build_canonical();


    $base_url =
        tfsc_combo_seo_build_base_url();


    if (
        '' === $current_url
        ||
        '' === $base_url
    ) {
        return $data;
    }


    foreach (
        $data
        as $key => &$entity
    ) {

        if (
            ! is_array(
                $entity
            )
        ) {
            continue;
        }


        $type =
            $entity['@type']
            ??
            '';


        if (
            'CollectionPage'
            ===
            $type
        ) {

            $entity['url'] =
                $current_url;


            if (
                ! empty(
                    $entity['@id']
                )
            ) {

                $entity['@id'] =
                    $current_url
                    .
                    '#webpage';
            }


            if (
                isset(
                    $entity['breadcrumb']
                )
                &&
                is_array(
                    $entity['breadcrumb']
                )
                &&
                isset(
                    $entity['breadcrumb']['@id']
                )
            ) {

                $entity[
                    'breadcrumb'
                ][
                    '@id'
                ] =
                    $current_url
                    .
                    '#breadcrumb';
            }
        }


        if (
            'BreadcrumbList'
            ===
            $type
        ) {

            $entity['@id'] =
                $current_url
                .
                '#breadcrumb';
        }
    }


    unset(
        $entity
    );


    return $data;
}


/* ============================================================
 * RANK MATH CALLBACK - NATIVE CANONICAL
 * ============================================================ */

function tfsc_native_rankmath_canonical(
    $canonical
) {

    if (
        ! tfsc_native_canonical_is_target()
    ) {
        return $canonical;
    }


    $combo_canonical =
        tfsc_native_canonical_build_url();


    if (
        '' === $combo_canonical
    ) {
        return $canonical;
    }


    return $combo_canonical;
}


/* ============================================================
 * RANK MATH CALLBACKS - FILTER URLS
 * ============================================================ */

function tfsc_vacancy_filter_rankmath_robots(
    $robots
) {

    if (
        ! tfsc_vacancy_filter_seo_is_active()
    ) {
        return $robots;
    }


    if (
        ! is_array(
            $robots
        )
    ) {
        $robots = [];
    }


    $robots['index'] =
        'noindex';

    $robots['follow'] =
        'follow';


    return $robots;
}


function tfsc_vacancy_filter_rankmath_canonical(
    $canonical
) {

    if (
        ! tfsc_vacancy_filter_seo_is_active()
    ) {
        return $canonical;
    }


    $clean =
        tfsc_vacancy_filter_clean_canonical();


    if (
        '' === $clean
    ) {
        return $canonical;
    }


    return $clean;
}


/* ============================================================
 * CORRECTE COMBO PREV / NEXT
 * ============================================================ */

function tfsc_combo_output_prev_next() {

    if (
        ! tfsc_combo_seo_is_active()
    ) {
        return;
    }


    $prev =
        tfsc_combo_seo_get_prev_url();


    $next =
        tfsc_combo_seo_get_next_url();


    if (
        '' !== $prev
    ) {

        echo '<link rel="prev" href="'
            .
            esc_url(
                $prev
            )
            .
            '" />'
            .
            "\n";
    }


    if (
        '' !== $next
    ) {

        echo '<link rel="next" href="'
            .
            esc_url(
                $next
            )
            .
            '" />'
            .
            "\n";
    }
}


/* ============================================================
 * RANK MATH FILTERS
 * ============================================================ */

add_action(
    'wp',
    function() {

        /* ----------------------------------------------------
         * COMBO ARCHIVE
         * ---------------------------------------------------- */

        if (
            tfsc_combo_seo_is_active()
        ) {

            add_filter(
                'rank_math/frontend/title',
                'tfsc_combo_rankmath_title',
                99
            );


            add_filter(
                'rank_math/frontend/description',
                'tfsc_combo_rankmath_description',
                99
            );


            add_filter(
                'rank_math/frontend/canonical',
                'tfsc_combo_rankmath_canonical',
                99
            );


            add_filter(
                'rank_math/frontend/robots',
                'tfsc_combo_rankmath_robots',
                99
            );


            add_filter(
                'rank_math/opengraph/url',
                'tfsc_combo_rankmath_og_url',
                99
            );


            add_filter(
                'rank_math/opengraph/facebook/og_url',
                'tfsc_combo_rankmath_og_url',
                99
            );


            add_filter(
                'rank_math/json_ld',
                'tfsc_combo_rankmath_json_ld',
                99,
                2
            );


            add_filter(
                'rank_math/frontend/disable_adjacent_rel_links',
                '__return_true',
                99
            );


            add_action(
                'wp_head',
                'tfsc_combo_output_prev_next',
                20
            );


            return;
        }


        /* ----------------------------------------------------
         * SEARCH & FILTER RESULTAAT-URL
         * ---------------------------------------------------- */

        if (
            tfsc_vacancy_filter_seo_is_active()
        ) {

            add_filter(
                'rank_math/frontend/canonical',
                'tfsc_vacancy_filter_rankmath_canonical',
                99
            );


            add_filter(
                'rank_math/frontend/robots',
                'tfsc_vacancy_filter_rankmath_robots',
                99
            );


            return;
        }


        /* ----------------------------------------------------
         * NATIVE VACATURE TAXONOMY ARCHIVE
         * ---------------------------------------------------- */

        if (
            tfsc_native_canonical_is_target()
        ) {

            add_filter(
                'rank_math/frontend/canonical',
                'tfsc_native_rankmath_canonical',
                99
            );


            add_filter(
                'rank_math/frontend/robots',
                'tfsc_native_rankmath_robots',
                99
            );
        }
    },
    20
);
