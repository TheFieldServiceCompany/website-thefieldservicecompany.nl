<?php
if (!defined('TFSC_COMBO_MANAGER_POLICY_HOOK_V1')) define('TFSC_COMBO_MANAGER_POLICY_HOOK_V1',true);

/**
 * TFSC - Combo Archive SEO + Native Archive Canonicals + Filter SEO
 * Version 4.4 - Phase 1: Maximaal 2 Taxonomieën
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
 * - meer dan 2 taxonomies: noindex/follow
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


    // Standalone archives belong to Rank Math, never to manager release controls.
    if (tfsc_combo_count_taxonomies($combo)===1) {
        $term=$combo[0]['term']??null;$index=$term instanceof WP_Term && class_exists('RankMath\\Helper') && \RankMath\Helper::is_term_indexable($term);
        return array('index'=>$index,'reason'=>$index?'rankmath_single_indexable':'rankmath_single_noindex','results'=>null,'taxonomies'=>1);
    }
    if(!tfsc_combo_has_function_name($combo))return array('index'=>false,'reason'=>'no_function_name','results'=>null,'taxonomies'=>tfsc_combo_count_taxonomies($combo));
    // Persistent manager controls survive plugin deactivation; only explicit exclusions.
    $denial = '';
    if ( function_exists('tfsc_combo_manager_index_denial') ) {
        $denial = tfsc_combo_manager_index_denial($combo);
    } else {
        $controls = get_option('tfsc_combo_manager_controls_v1', null);
        if (is_array($controls)) {
            if (isset($controls['blocks'][tfsc_combo_policy_signature($combo)])) $denial='manager_excluded_url';
            foreach ($combo as $item) if (!in_array($item['taxonomy'],(array)($controls['index_taxonomies']??array()),true)) $denial='manager_excluded_taxonomy';
        }
    }
    if ($denial) return array('index'=>false,'reason'=>$denial,'results'=>null,'taxonomies'=>tfsc_combo_count_taxonomies($combo));

    $taxonomy_count =
        tfsc_combo_count_taxonomies(
            $combo
        );


    $max_taxonomies =
        function_exists( 'tfsc_combo_policy_max_taxonomies' )
            ? tfsc_combo_policy_max_taxonomies()
            : 2;


    if (
        $taxonomy_count
        >
        $max_taxonomies
    ) {

        return [
            'index' => false,
            'reason' => 'too_many_taxonomies',
            'results' => null,
            'taxonomies' => $taxonomy_count,
        ];
    }


    foreach($combo as $item)if(!tfsc_combo_policy_rankmath_taxonomy_enabled($item['taxonomy'])||!tfsc_combo_policy_rankmath_term_allowed($item['term']))return array('index'=>false,'reason'=>'rankmath_excluded','results'=>null,'taxonomies'=>$taxonomy_count);

    $has_function =
        tfsc_combo_has_function_name(
            $combo
        );


    $has_location =
        tfsc_combo_has_location(
            $combo
        );


    if (
        $taxonomy_count > 2
        &&
        (
            ! function_exists( 'tfsc_combo_policy_is_released' )
            ||
            ! tfsc_combo_policy_is_released( $combo )
        )
    ) {

        return [
            'index' => false,
            'reason' => 'not_released',
            'results' => null,
            'taxonomies' => $taxonomy_count,
        ];
    }


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


    if(class_exists('RankMath\\Helper')&&\RankMath\Helper::get_term_meta('canonical_url',$term,$term->taxonomy))return false;

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


    // Preserve Rank Math's own index/noindex choice for standalone archives.
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


/**
 * TFSC combo-indexbeleid en gecontroleerde vrijgave.
 *
 * Dit blok hoort uitsluitend bij snippet 193. Andere snippets mogen deze
 * publieke functies gebruiken, maar bevatten zelf geen regels over toegestane
 * taxonomieën, indexeerbaarheid, maximale diepte of vrijgegeven combinaties.
 */

if ( ! defined( 'TFSC_COMBO_POLICY_OPTION' ) ) {
	define( 'TFSC_COMBO_POLICY_OPTION', 'tfsc_combo_index_policy_v1' );
}

if ( ! defined( 'TFSC_COMBO_POLICY_MAX_RELEASE_BATCH' ) ) {
	define( 'TFSC_COMBO_POLICY_MAX_RELEASE_BATCH', 50 );
}

/**
 * Centrale instellingen. Eén en twee taxonomieën behouden de bestaande
 * automatische regels. Vanaf drie taxonomieën is een exacte vrijgave nodig.
 */
function tfsc_combo_policy_get_settings() {
	$stored = get_option( TFSC_COMBO_POLICY_OPTION, array() );
	$stored = is_array( $stored ) ? $stored : array();

	$max_taxonomies = isset( $stored['max_taxonomies'] )
		? (int) $stored['max_taxonomies']
		: 3;

	$releases = isset( $stored['releases'] ) && is_array( $stored['releases'] )
		? $stored['releases']
		: array();

	$excluded_taxonomies = isset( $stored['excluded_taxonomies'] ) && is_array( $stored['excluded_taxonomies'] )
		? array_values( array_unique( array_filter( array_map( 'sanitize_key', $stored['excluded_taxonomies'] ) ) ) )
		: array();

	return array(
		'max_taxonomies'      => min( 4, max( 2, $max_taxonomies ) ),
		'excluded_taxonomies' => $excluded_taxonomies,
		'releases'             => $releases,
	);
}

function tfsc_combo_policy_update_settings( array $settings ) {
	$current = tfsc_combo_policy_get_settings();

	$max_taxonomies = isset( $settings['max_taxonomies'] )
		? (int) $settings['max_taxonomies']
		: $current['max_taxonomies'];

	$releases = isset( $settings['releases'] ) && is_array( $settings['releases'] )
		? $settings['releases']
		: $current['releases'];

	$excluded_taxonomies = isset( $settings['excluded_taxonomies'] ) && is_array( $settings['excluded_taxonomies'] )
		? array_values( array_unique( array_filter( array_map( 'sanitize_key', $settings['excluded_taxonomies'] ) ) ) )
		: $current['excluded_taxonomies'];

	$clean = array(
		'max_taxonomies'      => min( 4, max( 2, $max_taxonomies ) ),
		'excluded_taxonomies' => $excluded_taxonomies,
		'releases'             => $releases,
	);

	update_option( TFSC_COMBO_POLICY_OPTION, $clean, false );
	tfsc_combo_policy_flush_sitemap_cache();

	return $clean;
}

/**
 * Ontdekt alle technisch bruikbare vacaturetaxonomieën. Status en WordPress'
 * interne post-formats zijn nooit combo-dimensies.
 */
function tfsc_combo_policy_discover_taxonomies() {
	if ( ! defined( 'TFSC_VACANCY_POST_TYPE' ) || ! post_type_exists( TFSC_VACANCY_POST_TYPE ) ) {
		return array();
	}

	$status_taxonomy = defined( 'TFSC_STATUS_TAXONOMY' ) ? TFSC_STATUS_TAXONOMY : 'vac_status';
	$taxonomies      = array();

	foreach ( get_object_taxonomies( TFSC_VACANCY_POST_TYPE, 'objects' ) as $taxonomy => $object ) {
		$taxonomy = sanitize_key( $taxonomy );

		if (
			! $taxonomy ||
			$status_taxonomy === $taxonomy ||
			'post_format' === $taxonomy ||
			! $object ||
			! $object->publicly_queryable ||
			! in_array( TFSC_VACANCY_POST_TYPE, (array) $object->object_type, true )
		) {
			continue;
		}

		$taxonomies[] = $taxonomy;
	}

	sort( $taxonomies, SORT_STRING );

	return array_values( array_unique( $taxonomies ) );
}

/**
 * Enige allowlist-bron. We bewaren alleen expliciete uitsluitingen, zodat een
 * nieuwe geldige vacaturetaxonomie bij registratie automatisch beschikbaar is.
 */
function tfsc_combo_policy_allowed_taxonomies() {
	$settings = tfsc_combo_policy_get_settings();
	$eligible = tfsc_combo_policy_discover_taxonomies();

	return array_values( array_diff( $eligible, $settings['excluded_taxonomies'] ) );
}

function tfsc_combo_policy_taxonomy_allowed( $taxonomy ) {
	$taxonomy = sanitize_key( $taxonomy );

	return $taxonomy && in_array( $taxonomy, tfsc_combo_policy_allowed_taxonomies(), true );
}

/**
 * Stabiele identificatie van een combinatie, onafhankelijk van URL-volgorde.
 */
function tfsc_combo_policy_signature( $combo ) {
	if ( ! is_array( $combo ) || ! $combo ) {
		return '';
	}

	$groups = array();

	foreach ( $combo as $item ) {
		if (
			empty( $item['taxonomy'] ) ||
			empty( $item['term'] ) ||
			! $item['term'] instanceof WP_Term
		) {
			continue;
		}

		$taxonomy = sanitize_key( $item['taxonomy'] );
		$term_id  = (int) $item['term']->term_id;

		if ( ! $taxonomy || ! $term_id ) {
			continue;
		}

		if ( ! isset( $groups[ $taxonomy ] ) ) {
			$groups[ $taxonomy ] = array();
		}

		$groups[ $taxonomy ][ $term_id ] = true;
	}

	if ( ! $groups ) {
		return '';
	}

	ksort( $groups, SORT_STRING );
	$parts = array();

	foreach ( $groups as $taxonomy => $term_ids ) {
		$term_ids = array_map( 'intval', array_keys( $term_ids ) );
		sort( $term_ids, SORT_NUMERIC );
		$parts[] = $taxonomy . ':' . implode( ',', $term_ids );
	}

	return implode( '|', $parts );
}

function tfsc_combo_policy_taxonomies( $combo ) {
	$taxonomies = array();

	foreach ( (array) $combo as $item ) {
		$taxonomy = ! empty( $item['taxonomy'] )
			? sanitize_key( $item['taxonomy'] )
			: '';

		if ( $taxonomy ) {
			$taxonomies[ $taxonomy ] = true;
		}
	}

	$taxonomies = array_keys( $taxonomies );
	sort( $taxonomies, SORT_STRING );

	return $taxonomies;
}

function tfsc_combo_policy_taxonomy_set_signature( array $taxonomies ) {
	$taxonomies = array_values(
		array_unique(
			array_filter(
				array_map( 'sanitize_key', $taxonomies )
			)
		)
	);

	sort( $taxonomies, SORT_STRING );

	return implode( '|', $taxonomies );
}

/**
 * Enige maximumdiepte die SEO én sitemap mogen gebruiken.
 */
function tfsc_combo_policy_max_taxonomies() {
	$settings = tfsc_combo_policy_get_settings();

	return (int) $settings['max_taxonomies'];
}

/**
 * Exacte combinatie vrijgegeven? Alleen relevant vanaf drie taxonomieën.
 */
function tfsc_combo_policy_is_released( $combo ) {
	$signature = tfsc_combo_policy_signature( $combo );

	if ( '' === $signature ) {
		return false;
	}

	$settings = tfsc_combo_policy_get_settings();

	return isset( $settings['releases'][ $signature ] );
}

/**
 * De sitemap hoeft alleen tot de diepste werkelijk vrijgegeven combinatie te
 * rekenen. Zonder vrijgaven blijft hij dus exact op het bestaande niveau 2.
 */
function tfsc_combo_policy_sitemap_max_dimensions() {
	$settings = tfsc_combo_policy_get_settings();
	$max      = 2;

	foreach ( $settings['releases'] as $release ) {
		$depth = isset( $release['depth'] ) ? (int) $release['depth'] : 0;
		$max   = max( $max, $depth );
	}

	return min( tfsc_combo_policy_max_taxonomies(), $max );
}

/**
 * Voorkomt dat snippet 148 onnodig producten maakt voor taxonomiesets waarvoor
 * geen enkele vrijgegeven URL bestaat.
 */
function tfsc_combo_policy_sitemap_taxonomy_set_allowed( array $taxonomies ) {
	$taxonomies = array_values( array_unique( array_map( 'sanitize_key', $taxonomies ) ) );
	$depth      = count( $taxonomies );

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! tfsc_combo_policy_taxonomy_allowed( $taxonomy ) ) {
			return false;
		}
	}

	if ( $depth <= 2 ) {
		return true;
	}

	if ( $depth > tfsc_combo_policy_max_taxonomies() ) {
		return false;
	}

	$wanted   = tfsc_combo_policy_taxonomy_set_signature( $taxonomies );
	$settings = tfsc_combo_policy_get_settings();

	foreach ( $settings['releases'] as $release ) {
		$set = isset( $release['taxonomies'] ) && is_array( $release['taxonomies'] )
			? tfsc_combo_policy_taxonomy_set_signature( $release['taxonomies'] )
			: '';

		if ( $wanted && $wanted === $set ) {
			return true;
		}
	}

	return false;
}

function tfsc_combo_policy_flush_sitemap_cache() {
	if ( function_exists( 'tfsc_combo_sitemap_flush_cache' ) ) {
		tfsc_combo_sitemap_flush_cache();
		return;
	}

	foreach (
		array(
			'tfsc_combo_sitemap_links_v5_phase1_max2',
			'tfsc_combo_sitemap_links_v6_controlled_rollout',
		)
		as $key
	) {
		delete_transient( $key );
	}
}

function tfsc_combo_policy_release_record( array $combo, $url, $matches = 0 ) {
	$taxonomies = tfsc_combo_policy_taxonomies( $combo );
	$labels     = array();
	$terms      = array();

	foreach ( $combo as $item ) {
		if (
			empty( $item['taxonomy'] ) ||
			empty( $item['term'] ) ||
			! $item['term'] instanceof WP_Term
		) {
			continue;
		}

		$taxonomy        = sanitize_key( $item['taxonomy'] );
		$taxonomy_object = get_taxonomy( $taxonomy );
		$taxonomy_label  = $taxonomy_object && ! empty( $taxonomy_object->labels->singular_name )
			? $taxonomy_object->labels->singular_name
			: $taxonomy;

		$labels[] = $taxonomy_label . ': ' . $item['term']->name;
		$terms[]  = array(
			'taxonomy' => $taxonomy,
			'term_id'  => (int) $item['term']->term_id,
			'slug'     => sanitize_title( $item['term']->slug ),
			'name'     => sanitize_text_field( $item['term']->name ),
		);
	}

	return array(
		'url'          => esc_url_raw( $url ),
		'label'        => sanitize_text_field( implode( ' · ', $labels ) ),
		'taxonomies'   => $taxonomies,
		'terms'        => $terms,
		'depth'        => count( $taxonomies ),
		'matches'      => max( 0, (int) $matches ),
		'released_at'  => current_time( 'mysql', true ),
		'released_by'  => get_current_user_id(),
	);
}

/**
 * Kandidaten worden alleen voor één functienaam en een expliciet gekozen set
 * extra taxonomieën opgebouwd. Zo ontstaat nooit onbedoeld een sitebrede dump.
 */
function tfsc_combo_policy_generate_candidates( $function_term_id, array $extra_taxonomies ) {
	$required_helpers = array(
		'tfsc_combo_sitemap_get_post_dimensions',
		'tfsc_combo_sitemap_term_products',
		'tfsc_combo_sitemap_order_combo',
		'tfsc_combo_sitemap_build_url',
	);

	foreach ( $required_helpers as $helper ) {
		if ( ! function_exists( $helper ) ) {
			return new WP_Error( 'missing_sitemap_helper', 'De sitemap-renderer (snippet 148) is niet beschikbaar.' );
		}
	}

	$function_term = get_term( (int) $function_term_id, 'functienaam' );

	if ( ! $function_term instanceof WP_Term || is_wp_error( $function_term ) ) {
		return new WP_Error( 'invalid_function', 'Kies een geldige functienaam.' );
	}

	$allowed = tfsc_combo_policy_allowed_taxonomies();

	$extra_taxonomies = array_values(
		array_unique(
			array_filter(
				array_map( 'sanitize_key', $extra_taxonomies ),
				static function( $taxonomy ) use ( $allowed ) {
					return (
						'functienaam' !== $taxonomy &&
						'post_format' !== $taxonomy &&
						in_array( $taxonomy, $allowed, true )
					);
				}
			)
		)
	);

	$taxonomy_set = array_merge( array( 'functienaam' ), $extra_taxonomies );
	$depth        = count( $taxonomy_set );

	if ( $depth < 3 || $depth > tfsc_combo_policy_max_taxonomies() ) {
		return new WP_Error( 'invalid_depth', 'Kies genoeg extra taxonomieën zonder de ingestelde maximumdiepte te overschrijden.' );
	}

	$post_ids = get_posts(
		array(
			'post_type'      => defined( 'TFSC_VACANCY_POST_TYPE' ) ? TFSC_VACANCY_POST_TYPE : 'vacature',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array(
				'relation' => 'AND',
				array(
					'taxonomy'         => defined( 'TFSC_STATUS_TAXONOMY' ) ? TFSC_STATUS_TAXONOMY : 'vac_status',
					'field'            => 'slug',
					'terms'            => array( defined( 'TFSC_OPEN_STATUS_SLUG' ) ? TFSC_OPEN_STATUS_SLUG : 'open' ),
					'operator'         => 'IN',
					'include_children' => false,
				),
				array(
					'taxonomy'         => 'functienaam',
					'field'            => 'term_id',
					'terms'            => array( (int) $function_term->term_id ),
					'operator'         => 'IN',
					'include_children' => false,
				),
			),
		)
	);

	$candidates = array();

	foreach ( $post_ids as $post_id ) {
		$dimensions = tfsc_combo_sitemap_get_post_dimensions( (int) $post_id, $taxonomy_set );

		if ( count( $dimensions ) !== $depth ) {
			continue;
		}

		$dimensions['functienaam'] = array( $function_term );
		$products = tfsc_combo_sitemap_term_products( $taxonomy_set, $dimensions );

		foreach ( $products as $combo ) {
			$combo     = tfsc_combo_sitemap_order_combo( $combo );
			$signature = tfsc_combo_policy_signature( $combo );
			$url       = tfsc_combo_sitemap_build_url( $combo );

			if ( ! $signature || ! $url ) {
				continue;
			}

			if ( ! isset( $candidates[ $signature ] ) ) {
				$candidates[ $signature ] = array(
					'combo'    => $combo,
					'url'      => $url,
					'post_ids' => array(),
				);
			}

			$candidates[ $signature ]['post_ids'][ (int) $post_id ] = true;
		}
	}

	foreach ( $candidates as $signature => $candidate ) {
		$candidates[ $signature ]['matches'] = count( $candidate['post_ids'] );
		unset( $candidates[ $signature ]['post_ids'] );
	}

	uasort(
		$candidates,
		static function( $left, $right ) {
			return strcmp( $left['url'], $right['url'] );
		}
	);

	return $candidates;
}

function tfsc_combo_policy_admin_url( array $args = array() ) {
	return add_query_arg( $args, admin_url( 'tools.php?page=tfsc-combo-indexering' ) );
}

function tfsc_combo_policy_redirect( $notice ) {
	wp_safe_redirect(
		tfsc_combo_policy_admin_url(
			array( 'tfsc_combo_notice' => sanitize_key( $notice ) )
		)
	);
	exit;
}

function tfsc_combo_policy_handle_admin_action() {
	if ( ! current_user_can( 'manage_options' ) || empty( $_POST['tfsc_combo_action'] ) ) {
		return null;
	}

	$action = sanitize_key( wp_unslash( $_POST['tfsc_combo_action'] ) );
	check_admin_referer( 'tfsc_combo_policy_' . $action );

	if ( 'settings' === $action ) {
		$settings                   = tfsc_combo_policy_get_settings();
		$settings['max_taxonomies'] = isset( $_POST['max_taxonomies'] ) ? (int) $_POST['max_taxonomies'] : 3;

		if ( isset( $_POST['taxonomy_settings_present'] ) ) {
			$eligible = tfsc_combo_policy_discover_taxonomies();
			$enabled  = isset( $_POST['allowed_taxonomies'] ) ? (array) wp_unslash( $_POST['allowed_taxonomies'] ) : array();
			$enabled  = array_values( array_unique( array_map( 'sanitize_key', $enabled ) ) );
			$enabled  = array_values( array_intersect( $eligible, $enabled ) );

			$settings['excluded_taxonomies'] = array_values( array_diff( $eligible, $enabled ) );
		}

		tfsc_combo_policy_update_settings( $settings );
		tfsc_combo_policy_redirect( 'settings_saved' );
	}

	if ( 'remove' === $action ) {
		$selected = isset( $_POST['release_ids'] ) ? (array) wp_unslash( $_POST['release_ids'] ) : array();
		$settings = tfsc_combo_policy_get_settings();
		$removed  = 0;

		foreach ( $selected as $signature ) {
			$signature = sanitize_text_field( $signature );
			if ( isset( $settings['releases'][ $signature ] ) ) {
				unset( $settings['releases'][ $signature ] );
				$removed++;
			}
		}

		tfsc_combo_policy_update_settings( $settings );
		tfsc_combo_policy_redirect( $removed ? 'removed' : 'nothing_selected' );
	}

	$function_term_id = isset( $_POST['function_term_id'] ) ? (int) $_POST['function_term_id'] : 0;
	$extras           = isset( $_POST['extra_taxonomies'] ) ? (array) wp_unslash( $_POST['extra_taxonomies'] ) : array();
	$extras           = array_values( array_unique( array_map( 'sanitize_key', $extras ) ) );
	$candidates       = tfsc_combo_policy_generate_candidates( $function_term_id, $extras );

	if ( 'release' === $action ) {
		if ( is_wp_error( $candidates ) ) {
			tfsc_combo_policy_redirect( 'candidate_error' );
		}

		$selected = isset( $_POST['candidate_ids'] ) ? (array) wp_unslash( $_POST['candidate_ids'] ) : array();
		$selected = array_values( array_unique( array_map( 'sanitize_text_field', $selected ) ) );

		if ( ! $selected ) {
			tfsc_combo_policy_redirect( 'nothing_selected' );
		}

		if ( count( $selected ) > TFSC_COMBO_POLICY_MAX_RELEASE_BATCH ) {
			tfsc_combo_policy_redirect( 'batch_too_large' );
		}

		$settings = tfsc_combo_policy_get_settings();
		$added    = 0;

		foreach ( $selected as $signature ) {
			if ( ! isset( $candidates[ $signature ] ) ) {
				continue;
			}

			$candidate = $candidates[ $signature ];
			$settings['releases'][ $signature ] = tfsc_combo_policy_release_record(
				$candidate['combo'],
				$candidate['url'],
				$candidate['matches']
			);
			$added++;
		}

		tfsc_combo_policy_update_settings( $settings );
		tfsc_combo_policy_redirect( $added ? 'released' : 'nothing_selected' );
	}

	return array(
		'action'           => $action,
		'function_term_id' => $function_term_id,
		'extras'           => $extras,
		'candidates'       => $candidates,
	);
}

function tfsc_combo_policy_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$preview  = tfsc_combo_policy_handle_admin_action();
	$settings = tfsc_combo_policy_get_settings();
	$notice   = isset( $_GET['tfsc_combo_notice'] ) ? sanitize_key( wp_unslash( $_GET['tfsc_combo_notice'] ) ) : '';
	$messages = array(
		'settings_saved'  => 'Instellingen opgeslagen. De sitemapcache is vernieuwd.',
		'released'        => 'De geselecteerde combinaties zijn vrijgegeven en aan de sitemapregeling toegevoegd.',
		'removed'         => 'De geselecteerde vrijgaven zijn ingetrokken.',
		'nothing_selected'=> 'Er was niets geselecteerd.',
		'batch_too_large' => 'Selecteer maximaal 50 nieuwe URL’s per vrijgave.',
		'candidate_error' => 'De kandidaten konden niet opnieuw worden opgebouwd.',
	);

	$function_terms = get_terms(
		array(
			'taxonomy'   => 'functienaam',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);
	$function_terms = is_wp_error( $function_terms ) ? array() : $function_terms;

	$eligible_taxonomies = tfsc_combo_policy_discover_taxonomies();
	$allowed_taxonomies  = tfsc_combo_policy_allowed_taxonomies();

	$selected_function = is_array( $preview ) ? (int) $preview['function_term_id'] : 0;
	$selected_extras   = is_array( $preview ) ? (array) $preview['extras'] : array();
	$candidates        = is_array( $preview ) ? $preview['candidates'] : array();
	?>
	<div class="wrap tfsc-combo-policy">
		<h1>TFSC Combo-indexering</h1>
		<p>Hier bepaal je centraal welke diepe vacaturecombinaties geïndexeerd en in de combositemap opgenomen mogen worden. Eén en twee taxonomieën behouden de bestaande automatische regeling; vanaf drie taxonomieën is altijd een expliciete vrijgave nodig.</p>

		<?php if ( $notice && isset( $messages[ $notice ] ) ) : ?>
			<div class="notice notice-<?php echo in_array( $notice, array( 'batch_too_large', 'candidate_error' ), true ) ? 'error' : 'success'; ?> is-dismissible"><p><?php echo esc_html( $messages[ $notice ] ); ?></p></div>
		<?php endif; ?>

		<div class="tfsc-policy-card">
			<h2>Diepte en taxonomieën</h2>
			<p class="description">Dit is de enige centrale regeling voor de maximale diepte en taxonomieën van combo-archieven. Diepe URL’s worden pas indexeerbaar nadat je ze hieronder selecteert.</p>
			<form method="post">
				<?php wp_nonce_field( 'tfsc_combo_policy_settings' ); ?>
				<input type="hidden" name="tfsc_combo_action" value="settings">
				<input type="hidden" name="taxonomy_settings_present" value="1">
				<div class="tfsc-policy-grid">
					<label for="tfsc-max-taxonomies">Maximaal aantal verschillende taxonomieën
						<select id="tfsc-max-taxonomies" name="max_taxonomies">
							<?php foreach ( array( 2, 3, 4 ) as $depth ) : ?>
								<option value="<?php echo esc_attr( $depth ); ?>" <?php selected( (int) $settings['max_taxonomies'], $depth ); ?>><?php echo esc_html( $depth ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<fieldset class="tfsc-policy-taxonomies">
						<legend>Toegestane combo-taxonomieën</legend>
						<p class="description">Nieuwe geldige vacaturetaxonomieën staan automatisch aan. Status en post-formats zijn altijd uitgesloten.</p>
						<?php foreach ( $eligible_taxonomies as $taxonomy ) : ?>
							<?php $object = get_taxonomy( $taxonomy ); ?>
							<label><input type="checkbox" name="allowed_taxonomies[]" value="<?php echo esc_attr( $taxonomy ); ?>" <?php checked( in_array( $taxonomy, $allowed_taxonomies, true ) ); ?>> <?php echo esc_html( $object && ! empty( $object->labels->name ) ? $object->labels->name : $taxonomy ); ?> <code><?php echo esc_html( $taxonomy ); ?></code></label>
						<?php endforeach; ?>
					</fieldset>
				</div>
				<?php submit_button( 'Diepte en taxonomieën opslaan', 'secondary', 'submit', false ); ?>
			</form>
		</div>

		<div class="tfsc-policy-card">
			<h2>Kandidaten zoeken</h2>
			<p class="description">Kies één functienaam en de extra taxonomieën. Alleen combinaties die werkelijk bij een gepubliceerde, open vacature voorkomen worden getoond.</p>
			<form method="post">
				<?php wp_nonce_field( 'tfsc_combo_policy_preview' ); ?>
				<input type="hidden" name="tfsc_combo_action" value="preview">
				<div class="tfsc-policy-grid">
					<label>Functienaam
						<select name="function_term_id" required>
							<option value="">Kies een functienaam…</option>
							<?php foreach ( $function_terms as $term ) : ?>
								<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected_function, (int) $term->term_id ); ?>><?php echo esc_html( $term->name ); ?> (<?php echo esc_html( $term->count ); ?>)</option>
							<?php endforeach; ?>
						</select>
					</label>
					<label>Extra taxonomieën
						<select name="extra_taxonomies[]" multiple size="7" required>
							<?php foreach ( $allowed_taxonomies as $taxonomy ) : ?>
								<?php if ( 'functienaam' === $taxonomy ) { continue; } ?>
								<?php $object = get_taxonomy( $taxonomy ); ?>
								<option value="<?php echo esc_attr( $taxonomy ); ?>" <?php echo in_array( $taxonomy, $selected_extras, true ) ? 'selected' : ''; ?>><?php echo esc_html( $object && ! empty( $object->labels->name ) ? $object->labels->name : $taxonomy ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
				<?php submit_button( 'Kandidaten tonen', 'primary', 'submit', false ); ?>
			</form>
		</div>

		<?php if ( is_wp_error( $candidates ) ) : ?>
			<div class="notice notice-error inline"><p><?php echo esc_html( $candidates->get_error_message() ); ?></p></div>
		<?php elseif ( is_array( $preview ) ) : ?>
			<div class="tfsc-policy-card">
				<h2>Gevonden kandidaten (<?php echo esc_html( count( $candidates ) ); ?>)</h2>
				<p class="description">Selecteer maximaal <?php echo esc_html( TFSC_COMBO_POLICY_MAX_RELEASE_BATCH ); ?> URL’s per keer. Al vrijgegeven combinaties zijn gemarkeerd.</p>
				<input type="search" id="tfsc-candidate-search" class="regular-text" placeholder="Zoek in kandidaat-URL’s…">
				<form method="post">
					<?php wp_nonce_field( 'tfsc_combo_policy_release' ); ?>
					<input type="hidden" name="tfsc_combo_action" value="release">
					<input type="hidden" name="function_term_id" value="<?php echo esc_attr( $selected_function ); ?>">
					<?php foreach ( $selected_extras as $taxonomy ) : ?>
						<input type="hidden" name="extra_taxonomies[]" value="<?php echo esc_attr( $taxonomy ); ?>">
					<?php endforeach; ?>
					<div class="tfsc-policy-table-wrap">
						<table class="widefat striped" id="tfsc-candidate-table">
							<thead><tr><th class="check-column"><input type="checkbox" data-tfsc-select-all="candidates"></th><th>URL</th><th>Open vacatures</th><th>Status</th></tr></thead>
							<tbody>
							<?php foreach ( $candidates as $signature => $candidate ) : ?>
								<?php $is_released = isset( $settings['releases'][ $signature ] ); ?>
								<tr>
									<th class="check-column"><input class="tfsc-candidate-checkbox" type="checkbox" name="candidate_ids[]" value="<?php echo esc_attr( $signature ); ?>" <?php disabled( $is_released ); ?>></th>
									<td><a href="<?php echo esc_url( $candidate['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $candidate['url'] ); ?></a></td>
									<td><?php echo esc_html( $candidate['matches'] ); ?></td>
									<td><?php echo $is_released ? '<span class="tfsc-policy-ok">Vrijgegeven</span>' : 'Nog noindex'; ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php submit_button( 'Geselecteerde URL’s vrijgeven', 'primary', 'submit', false ); ?>
				</form>
			</div>
		<?php endif; ?>

		<div class="tfsc-policy-card">
			<h2>Vrijgegeven combinaties (<?php echo esc_html( count( $settings['releases'] ) ); ?>)</h2>
			<p class="description">Deze lijst is de enige bron voor indexering vanaf drie taxonomieën. Intrekken zet de URL terug op noindex en verwijdert hem uit de sitemap zodra de cache is vernieuwd.</p>
			<input type="search" id="tfsc-release-search" class="regular-text" placeholder="Zoek in vrijgegeven URL’s…">
			<form method="post">
				<?php wp_nonce_field( 'tfsc_combo_policy_remove' ); ?>
				<input type="hidden" name="tfsc_combo_action" value="remove">
				<div class="tfsc-policy-table-wrap">
					<table class="widefat striped" id="tfsc-release-table">
						<thead><tr><th class="check-column"><input type="checkbox" data-tfsc-select-all="releases"></th><th>URL</th><th>Diepte</th><th>Open vacatures bij vrijgave</th><th>Vrijgegeven</th><th>Actief</th></tr></thead>
						<tbody>
						<?php foreach ( $settings['releases'] as $signature => $release ) : ?>
							<?php $active = (int) ( $release['depth'] ?? 0 ) <= (int) $settings['max_taxonomies']; ?>
							<tr>
								<th class="check-column"><input class="tfsc-release-checkbox" type="checkbox" name="release_ids[]" value="<?php echo esc_attr( $signature ); ?>"></th>
								<td><a href="<?php echo esc_url( $release['url'] ?? '' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $release['url'] ?? $signature ); ?></a></td>
								<td><?php echo esc_html( (int) ( $release['depth'] ?? 0 ) ); ?></td>
								<td><?php echo esc_html( (int) ( $release['matches'] ?? 0 ) ); ?></td>
								<td><?php echo esc_html( ! empty( $release['released_at'] ) ? get_date_from_gmt( $release['released_at'], 'Y-m-d H:i:s' ) : '—' ); ?></td>
								<td><?php echo $active ? '<span class="tfsc-policy-ok">Ja</span>' : '<span class="tfsc-policy-off">Nee, boven maximum</span>'; ?></td>
							</tr>
						<?php endforeach; ?>
						<?php if ( ! $settings['releases'] ) : ?><tr><td colspan="6">Nog geen diepe combinaties vrijgegeven.</td></tr><?php endif; ?>
						</tbody>
					</table>
				</div>
				<?php submit_button( 'Geselecteerde vrijgaven intrekken', 'delete', 'submit', false ); ?>
			</form>
		</div>
	</div>
	<style>
		.tfsc-combo-policy{max-width:1500px}.tfsc-policy-card{background:#fff;border:1px solid #c3c4c7;padding:18px 20px;margin:18px 0}.tfsc-policy-card h2{margin-top:0}.tfsc-policy-grid{display:grid;grid-template-columns:minmax(280px,1fr) minmax(360px,2fr);gap:18px;max-width:1100px;margin:16px 0}.tfsc-policy-grid label{font-weight:600}.tfsc-policy-grid select{display:block;width:100%;margin-top:6px}.tfsc-policy-taxonomies{border:0;padding:0;margin:0}.tfsc-policy-taxonomies legend{font-weight:600}.tfsc-policy-taxonomies label{display:block;font-weight:400;margin:7px 0}.tfsc-policy-table-wrap{max-height:520px;overflow:auto;margin:14px 0;border:1px solid #dcdcde}.tfsc-policy-table-wrap table{border:0}.tfsc-policy-ok{color:#06752c;font-weight:600}.tfsc-policy-off{color:#a02222;font-weight:600}@media(max-width:782px){.tfsc-policy-grid{grid-template-columns:1fr}}
	</style>
	<script>
	(function(){
		document.querySelectorAll('[data-tfsc-select-all]').forEach(function(master){
			master.addEventListener('change',function(){
				var selector='candidates'===master.dataset.tfscSelectAll?'.tfsc-candidate-checkbox':'.tfsc-release-checkbox';
				document.querySelectorAll(selector).forEach(function(box){if(!box.disabled){box.checked=master.checked;}});
			});
		});
		var search=document.getElementById('tfsc-release-search');
		if(search){search.addEventListener('input',function(){var needle=search.value.toLowerCase();document.querySelectorAll('#tfsc-release-table tbody tr').forEach(function(row){row.style.display=row.textContent.toLowerCase().indexOf(needle)>-1?'':'none';});});}
		var candidateSearch=document.getElementById('tfsc-candidate-search');
		if(candidateSearch){candidateSearch.addEventListener('input',function(){var needle=candidateSearch.value.toLowerCase();document.querySelectorAll('#tfsc-candidate-table tbody tr').forEach(function(row){row.style.display=row.textContent.toLowerCase().indexOf(needle)>-1?'':'none';});});}
	})();
	</script>
	<?php
}

add_action(
	'admin_menu',
	static function() {
		add_management_page(
			'TFSC Combo-indexering',
			'TFSC Combo-indexering',
			'manage_options',
			'tfsc-combo-indexering',
			'tfsc_combo_policy_render_admin_page'
		);
	}
);


/** Rank Math owns standalone taxonomy maps; use the already canonical TFSC URL. */
add_filter('rank_math/sitemap/entry',function($entry,$type,$object){
    if($type!=='term'||!is_array($entry)||!$object instanceof WP_Term||!function_exists('tfsc_combo_taxonomy_allowed')||!tfsc_combo_taxonomy_allowed($object->taxonomy))return $entry;
    if(class_exists('RankMath\\Helper')&&\RankMath\Helper::get_term_meta('canonical_url',$object,$object->taxonomy))return $entry;
    if(function_exists('tfsc_combo_sitemap_make_item')&&function_exists('tfsc_combo_sitemap_build_url'))$entry['loc']=tfsc_combo_sitemap_build_url(array(tfsc_combo_sitemap_make_item($object)));
    return $entry;
},30,3);

/** Available combo filters follow Rank Math, without changing router availability. */
function tfsc_combo_policy_rankmath_reset_cache(){ $GLOBALS['tfsc_combo_rankmath_cache']=array(); }
function tfsc_combo_policy_rankmath_taxonomy_enabled($taxonomy){
    $cache=&$GLOBALS['tfsc_combo_rankmath_cache'];if(isset($cache['tax'][$taxonomy]))return $cache['tax'][$taxonomy];
    if(!class_exists('RankMath\\Helper'))return false;
    $available=\RankMath\Helper::get_accessible_taxonomies();
    if(!isset($available[$taxonomy])||!\RankMath\Helper::is_taxonomy_indexable($taxonomy))return $cache['tax'][$taxonomy]=false;
    return $cache['tax'][$taxonomy]=!apply_filters('rank_math/sitemap/exclude_taxonomy',false,$taxonomy);
}
function tfsc_combo_policy_combo_taxonomies(){
    return array_values(array_filter(tfsc_combo_policy_allowed_taxonomies(),'tfsc_combo_policy_rankmath_taxonomy_enabled'));
}
function tfsc_combo_policy_rankmath_term_allowed($term){
    if(!$term instanceof WP_Term)return false;
    $key=$term->taxonomy.':'.$term->term_id;$cache=&$GLOBALS['tfsc_combo_rankmath_cache'];if(isset($cache['term'][$key]))return $cache['term'][$key];
    if(!$term instanceof WP_Term||!class_exists('RankMath\\Helper')||!\RankMath\Helper::is_term_indexable($term))return $cache['term'][$key]=false;
    $excluded=wp_parse_id_list(\RankMath\Helper::get_settings('sitemap.exclude_terms'));
    return $cache['term'][$key]=!in_array((int)$term->term_id,$excluded,true);
}
