<?php

/**
 * Vac Combo Archives | SiteMap [v2.2] WORKING - don't remove!
 *
 * Zie in de snippet de fases. We zitten nu in fase 1. 
 */
/**
 * TFSC - Combo Vacancy Sitemap
 *
 * Vereist:
 * - TFSC - Volledig dynamische vacature-archieven
 * - TFSC - Combo Archive SEO
 * - Rank Math SEO
 *
 * Maakt:
 * /combo-vacatures-sitemap.xml
 *
 * Wordt automatisch toegevoegd aan:
 * /sitemap_index.xml
 *
 *
 * HUIDIGE STRATEGIE
 * ============================================================
 *
 * - Alleen gepubliceerde + open vacatures
 * - Alleen werkelijk bestaande termcombinaties
 * - Exact 2 verschillende taxonomies per combo
 * - tfsc_combo_should_index() bepaalt of de combo
 *   daadwerkelijk in de sitemap mag
 * - functienaam wordt altijd als eerste taxonomy
 *   in de URL geplaatst
 * - overige taxonomies blijven volledig dynamisch
 * - iedere combo-URL krijgt een betrouwbare lastmod
 * - sitemap-index krijgt per sitemapdeel de nieuwste lastmod
 *
 *
 * Performance:
 * - combinaties worden alleen berekend wanneer nodig
 * - URL-lijst wordt gecachet
 * - cache wordt automatisch verwijderd bij relevante wijzigingen
 */


/**
 * ============================================================
 * CORE CONFIG FALLBACKS
 * ============================================================
 *
 * Deze constants worden normaal gedefinieerd door:
 *
 * TFSC - Volledig dynamische vacature-archieven
 *
 * Ze staan hier ook als fallback zodat deze snippet niet
 * afhankelijk is van de laadvolgorde van Code Snippets.
 */

if ( ! defined( 'TFSC_COMBO_BASE' ) ) {
    define(
        'TFSC_COMBO_BASE',
        'vacatures'
    );
}

if ( ! defined( 'TFSC_VACANCY_POST_TYPE' ) ) {
    define(
        'TFSC_VACANCY_POST_TYPE',
        'vacature'
    );
}

if ( ! defined( 'TFSC_STATUS_TAXONOMY' ) ) {
    define(
        'TFSC_STATUS_TAXONOMY',
        'vac_status'
    );
}

if ( ! defined( 'TFSC_OPEN_STATUS_SLUG' ) ) {
    define(
        'TFSC_OPEN_STATUS_SLUG',
        'open'
    );
}


/**
 * ============================================================
 * CONFIG
 * ============================================================
 */

if (
    ! defined(
        'TFSC_COMBO_SITEMAP_MIN_DIMENSIONS'
    )
) {
    define(
        'TFSC_COMBO_SITEMAP_MIN_DIMENSIONS',
        2
    );
}


if (
    ! defined(
        'TFSC_COMBO_SITEMAP_MAX_DIMENSIONS'
    )
) {
    define(
        'TFSC_COMBO_SITEMAP_MAX_DIMENSIONS',
        3
    );
}


/**
 * Cache key met lastmod-support.
 */
if (
    ! defined(
        'TFSC_COMBO_SITEMAP_CACHE_KEY'
    )
) {
    define(
        'TFSC_COMBO_SITEMAP_CACHE_KEY',
        'tfsc_combo_sitemap_links_v4'
    );
}


if (
    ! defined(
        'TFSC_COMBO_SITEMAP_CACHE_TTL'
    )
) {
    define(
        'TFSC_COMBO_SITEMAP_CACHE_TTL',
        DAY_IN_SECONDS
    );
}


/**
 * ============================================================
 * BESCHIKBAARHEID
 * ============================================================
 */

function tfsc_combo_sitemap_is_available() {

    return (
        function_exists(
            'tfsc_get_allowed_combo_taxonomies'
        )
        &&
        function_exists(
            'tfsc_get_combo_taxonomy_match_method'
        )
        &&
        function_exists(
            'tfsc_combo_should_index'
        )
        &&
        defined(
            'TFSC_VACANCY_POST_TYPE'
        )
        &&
        defined(
            'TFSC_STATUS_TAXONOMY'
        )
        &&
        defined(
            'TFSC_OPEN_STATUS_SLUG'
        )
        &&
        defined(
            'TFSC_COMBO_BASE'
        )
    );
}


/**
 * ============================================================
 * TAXONOMY VOLGORDE
 * ============================================================
 */

function tfsc_combo_sitemap_order_taxonomies(
    array $taxonomies
) {

    $function = [];
    $others   = [];

    foreach ( $taxonomies as $taxonomy ) {

        if ( 'functienaam' === $taxonomy ) {
            $function[] = $taxonomy;
        } else {
            $others[] = $taxonomy;
        }
    }

    return array_merge(
        $function,
        $others
    );
}


/**
 * ============================================================
 * COMBO VOLGORDE
 * ============================================================
 */

function tfsc_combo_sitemap_order_combo(
    array $combo
) {

    $function = [];
    $others   = [];

    foreach ( $combo as $item ) {

        if (
            isset( $item['taxonomy'] ) &&
            'functienaam'
            === $item['taxonomy']
        ) {
            $function[] = $item;
        } else {
            $others[] = $item;
        }
    }

    return array_merge(
        $function,
        $others
    );
}


/**
 * ============================================================
 * VOLLEDIG TERM-PAD
 * ============================================================
 */

function tfsc_combo_sitemap_get_term_path(
    WP_Term $term
) {

    $taxonomy =
        get_taxonomy(
            $term->taxonomy
        );

    if ( ! $taxonomy ) {
        return [];
    }


    if (
        ! $taxonomy->hierarchical
    ) {
        return [
            $term,
        ];
    }


    $path = [];


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
            $ancestor instanceof WP_Term &&
            ! is_wp_error( $ancestor )
        ) {
            $path[] = $ancestor;
        }
    }


    $path[] = $term;


    return $path;
}


/**
 * ============================================================
 * TERM → COMBO ITEM
 * ============================================================
 */

function tfsc_combo_sitemap_make_item(
    WP_Term $term
) {

    return [
        'taxonomy' =>
            $term->taxonomy,

        'term' =>
            $term,

        'path' =>
            tfsc_combo_sitemap_get_term_path(
                $term
            ),
    ];
}


/**
 * ============================================================
 * COMBO URL
 * ============================================================
 */

function tfsc_combo_sitemap_build_url(
    array $combo
) {

    if ( ! $combo ) {
        return '';
    }


    $combo =
        tfsc_combo_sitemap_order_combo(
            $combo
        );


    $segments = [];


    foreach ( $combo as $item ) {

        if (
            empty( $item['taxonomy'] ) ||
            empty( $item['path'] ) ||
            ! is_array( $item['path'] )
        ) {
            return '';
        }


        $taxonomy =
            sanitize_key(
                $item['taxonomy']
            );


        if (
            function_exists(
                'tfsc_combo_taxonomy_allowed'
            )
            &&
            ! tfsc_combo_taxonomy_allowed(
                $taxonomy
            )
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
                ! $term instanceof WP_Term ||
                $term->taxonomy !== $taxonomy
            ) {
                return '';
            }


            $segments[] =
                $term->slug;
        }
    }


    if ( ! $segments ) {
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


/**
 * ============================================================
 * POST LASTMOD
 * ============================================================
 */

function tfsc_combo_sitemap_get_post_lastmod(
    $post_id
) {

    $modified_gmt =
        get_post_field(
            'post_modified_gmt',
            $post_id
        );


    if (
        ! $modified_gmt ||
        '0000-00-00 00:00:00'
        === $modified_gmt
    ) {
        return '';
    }


    $timestamp =
        strtotime(
            $modified_gmt . ' GMT'
        );


    if ( ! $timestamp ) {
        return '';
    }


    return gmdate(
        'c',
        $timestamp
    );
}


/**
 * ============================================================
 * NIEUWSTE LASTMOD
 * ============================================================
 */

function tfsc_combo_sitemap_newest_lastmod(
    $current,
    $candidate
) {

    if ( ! $candidate ) {
        return $current;
    }


    if ( ! $current ) {
        return $candidate;
    }


    $current_time =
        strtotime(
            $current
        );


    $candidate_time =
        strtotime(
            $candidate
        );


    if (
        ! $candidate_time
    ) {
        return $current;
    }


    if (
        ! $current_time ||
        $candidate_time > $current_time
    ) {
        return $candidate;
    }


    return $current;
}


/**
 * ============================================================
 * TAXONOMY TERMS VAN ÉÉN VACATURE
 * ============================================================
 */

function tfsc_combo_sitemap_get_post_dimensions(
    $post_id,
    array $taxonomies
) {

    $dimensions = [];


    foreach (
        $taxonomies
        as $taxonomy
    ) {

        $terms =
            get_the_terms(
                $post_id,
                $taxonomy
            );


        if (
            ! is_array( $terms ) ||
            ! $terms
        ) {
            continue;
        }


        $taxonomy_object =
            get_taxonomy(
                $taxonomy
            );


        $match_method =
            tfsc_get_combo_taxonomy_match_method(
                $taxonomy
            );


        $collected = [];


        foreach (
            $terms
            as $term
        ) {

            if (
                ! $term instanceof WP_Term
            ) {
                continue;
            }


            $collected[
                $term->term_id
            ] = $term;


            if (
                $taxonomy_object &&
                $taxonomy_object->hierarchical &&
                'AND' !== $match_method
            ) {

                $ancestor_ids =
                    get_ancestors(
                        $term->term_id,
                        $taxonomy,
                        'taxonomy'
                    );


                foreach (
                    $ancestor_ids
                    as $ancestor_id
                ) {

                    $ancestor =
                        get_term(
                            $ancestor_id,
                            $taxonomy
                        );


                    if (
                        $ancestor instanceof WP_Term &&
                        ! is_wp_error(
                            $ancestor
                        )
                    ) {

                        $collected[
                            $ancestor->term_id
                        ] = $ancestor;
                    }
                }
            }
        }


        if ( $collected ) {

            $dimensions[
                $taxonomy
            ] =
                array_values(
                    $collected
                );
        }
    }


    return $dimensions;
}


/**
 * ============================================================
 * COMBINATIES VAN TAXONOMIES
 * ============================================================
 */

function tfsc_combo_sitemap_dimension_sets(
    array $dimensions,
    $size
) {

    $dimensions =
        array_values(
            $dimensions
        );


    $size =
        (int) $size;


    if (
        $size < 1 ||
        count( $dimensions ) < $size
    ) {
        return [];
    }


    if ( 1 === $size ) {

        return array_map(
            static function(
                $dimension
            ) {

                return [
                    $dimension,
                ];
            },
            $dimensions
        );
    }


    $result = [];


    $first =
        array_shift(
            $dimensions
        );


    foreach (
        tfsc_combo_sitemap_dimension_sets(
            $dimensions,
            $size - 1
        )
        as $subset
    ) {

        array_unshift(
            $subset,
            $first
        );


        $result[] =
            $subset;
    }


    foreach (
        tfsc_combo_sitemap_dimension_sets(
            $dimensions,
            $size
        )
        as $subset
    ) {

        $result[] =
            $subset;
    }


    return $result;
}


/**
 * ============================================================
 * TERM PRODUCTEN
 * ============================================================
 */

function tfsc_combo_sitemap_term_products(
    array $taxonomy_set,
    array $dimensions
) {

    $taxonomy_set =
        tfsc_combo_sitemap_order_taxonomies(
            $taxonomy_set
        );


    $products = [
        [],
    ];


    foreach (
        $taxonomy_set
        as $taxonomy
    ) {

        if (
            empty(
                $dimensions[
                    $taxonomy
                ]
            )
        ) {
            return [];
        }


        $next = [];


        foreach (
            $products
            as $product
        ) {

            foreach (
                $dimensions[
                    $taxonomy
                ]
                as $term
            ) {

                if (
                    ! $term instanceof WP_Term
                ) {
                    continue;
                }


                $new_product =
                    $product;


                $new_product[] =
                    tfsc_combo_sitemap_make_item(
                        $term
                    );


                $next[] =
                    $new_product;
            }
        }


        $products =
            $next;
    }


    return $products;
}


/**
 * ============================================================
 * SITEMAP LINKS GENEREREN
 * ============================================================
 */

function tfsc_combo_sitemap_generate_links() {

    if (
        ! tfsc_combo_sitemap_is_available()
    ) {
        return [];
    }


    $cached =
        get_transient(
            TFSC_COMBO_SITEMAP_CACHE_KEY
        );


    if (
        is_array( $cached )
    ) {
        return $cached;
    }


    $taxonomies =
        tfsc_get_allowed_combo_taxonomies();


    if ( ! $taxonomies ) {
        return [];
    }


    $taxonomies =
        tfsc_combo_sitemap_order_taxonomies(
            $taxonomies
        );


    $posts =
        get_posts(
            [
                'post_type' =>
                    TFSC_VACANCY_POST_TYPE,

                'post_status' =>
                    'publish',

                'posts_per_page' =>
                    -1,

                'no_found_rows' =>
                    true,

                'orderby' =>
                    'ID',

                'order' =>
                    'ASC',

                'tax_query' => [
                    [
                        'taxonomy' =>
                            TFSC_STATUS_TAXONOMY,

                        'field' =>
                            'slug',

                        'terms' => [
                            TFSC_OPEN_STATUS_SLUG,
                        ],

                        'operator' =>
                            'IN',

                        'include_children' =>
                            false,
                    ],
                ],
            ]
        );


    if ( ! $posts ) {

        set_transient(
            TFSC_COMBO_SITEMAP_CACHE_KEY,
            [],
            TFSC_COMBO_SITEMAP_CACHE_TTL
        );


        return [];
    }


    $links = [];


    foreach (
        $posts
        as $post
    ) {

        if (
            ! $post instanceof WP_Post
        ) {
            continue;
        }


        $post_id =
            (int) $post->ID;


        $post_lastmod =
            tfsc_combo_sitemap_get_post_lastmod(
                $post_id
            );


        $dimensions =
            tfsc_combo_sitemap_get_post_dimensions(
                $post_id,
                $taxonomies
            );


        if ( ! $dimensions ) {
            continue;
        }


        $ordered_dimensions = [];


        foreach (
            $taxonomies
            as $taxonomy
        ) {

            if (
                isset(
                    $dimensions[
                        $taxonomy
                    ]
                )
            ) {

                $ordered_dimensions[
                    $taxonomy
                ] =
                    $dimensions[
                        $taxonomy
                    ];
            }
        }


        $dimensions =
            $ordered_dimensions;


        $taxonomy_names =
            array_keys(
                $dimensions
            );


        $min =
            max(
                2,
                (int)
                TFSC_COMBO_SITEMAP_MIN_DIMENSIONS
            );


        $max =
            min(
                count(
                    $taxonomy_names
                ),
                max(
                    $min,
                    (int)
                    TFSC_COMBO_SITEMAP_MAX_DIMENSIONS
                )
            );


        if ( $max < $min ) {
            continue;
        }


        for (
            $size = $min;
            $size <= $max;
            $size++
        ) {

            $dimension_sets =
                tfsc_combo_sitemap_dimension_sets(
                    $taxonomy_names,
                    $size
                );


            foreach (
                $dimension_sets
                as $taxonomy_set
            ) {

                $taxonomy_set =
                    tfsc_combo_sitemap_order_taxonomies(
                        $taxonomy_set
                    );


                $products =
                    tfsc_combo_sitemap_term_products(
                        $taxonomy_set,
                        $dimensions
                    );


                foreach (
                    $products
                    as $combo
                ) {

                    $combo =
                        tfsc_combo_sitemap_order_combo(
                            $combo
                        );


                    if (
                        ! tfsc_combo_should_index(
                            $combo
                        )
                    ) {
                        continue;
                    }


                    $url =
                        tfsc_combo_sitemap_build_url(
                            $combo
                        );


                    if ( '' === $url ) {
                        continue;
                    }


                    if (
                        ! isset(
                            $links[
                                $url
                            ]
                        )
                    ) {

                        $links[
                            $url
                        ] = [
                            'loc' =>
                                $url,

                            'lastmod' =>
                                $post_lastmod,
                        ];

                        continue;
                    }


                    $links[
                        $url
                    ][
                        'lastmod'
                    ] =
                        tfsc_combo_sitemap_newest_lastmod(
                            isset(
                                $links[
                                    $url
                                ][
                                    'lastmod'
                                ]
                            )
                                ?
                                $links[
                                    $url
                                ][
                                    'lastmod'
                                ]
                                :
                                '',

                            $post_lastmod
                        );
                }
            }
        }
    }


    ksort(
        $links,
        SORT_STRING
    );


    $links =
        array_values(
            $links
        );


    set_transient(
        TFSC_COMBO_SITEMAP_CACHE_KEY,
        $links,
        TFSC_COMBO_SITEMAP_CACHE_TTL
    );


    return $links;
}


/**
 * ============================================================
 * CACHE LEGEN
 * ============================================================
 */

function tfsc_combo_sitemap_flush_cache() {

    delete_transient(
        TFSC_COMBO_SITEMAP_CACHE_KEY
    );


    delete_transient(
        'tfsc_combo_sitemap_links_v1'
    );

    delete_transient(
        'tfsc_combo_sitemap_links_v2'
    );

    delete_transient(
        'tfsc_combo_sitemap_links_v3'
    );


    if (
        class_exists(
            '\RankMath\Sitemap\Cache'
        )
        &&
        method_exists(
            '\RankMath\Sitemap\Cache',
            'invalidate_storage'
        )
    ) {

        \RankMath\Sitemap\Cache::invalidate_storage(
            'combo-vacatures'
        );
    }
}


/**
 * ============================================================
 * CACHE INVALIDATIE BIJ VACATURE OPSLAAN
 * ============================================================
 */

add_action(
    'save_post_' .
    TFSC_VACANCY_POST_TYPE,

    function(
        $post_id,
        $post,
        $update
    ) {

        if (
            wp_is_post_revision(
                $post_id
            )
        ) {
            return;
        }


        tfsc_combo_sitemap_flush_cache();
    },

    20,
    3
);


/**
 * ============================================================
 * CACHE INVALIDATIE BIJ TERM-RELATIES
 * ============================================================
 */

add_action(
    'set_object_terms',

    function(
        $object_id,
        $terms,
        $tt_ids,
        $taxonomy,
        $append,
        $old_tt_ids
    ) {

        if (
            ! function_exists(
                'tfsc_combo_taxonomy_allowed'
            )
        ) {
            return;
        }


        if (
            TFSC_STATUS_TAXONOMY
                !== $taxonomy
            &&
            ! tfsc_combo_taxonomy_allowed(
                $taxonomy
            )
        ) {
            return;
        }


        if (
            TFSC_VACANCY_POST_TYPE
            !== get_post_type(
                $object_id
            )
        ) {
            return;
        }


        tfsc_combo_sitemap_flush_cache();
    },

    20,
    6
);


/**
 * ============================================================
 * CACHE INVALIDATIE BIJ TERM WIJZIGING
 * ============================================================
 */

function tfsc_combo_sitemap_term_changed(
    $term_id,
    $tt_id = 0,
    $taxonomy = ''
) {

    if (
        ! $taxonomy ||
        ! function_exists(
            'tfsc_combo_taxonomy_allowed'
        )
    ) {
        return;
    }


    if (
        TFSC_STATUS_TAXONOMY
            !== $taxonomy
        &&
        ! tfsc_combo_taxonomy_allowed(
            $taxonomy
        )
    ) {
        return;
    }


    tfsc_combo_sitemap_flush_cache();
}


add_action(
    'created_term',
    'tfsc_combo_sitemap_term_changed',
    20,
    3
);


add_action(
    'edited_term',
    'tfsc_combo_sitemap_term_changed',
    20,
    3
);


add_action(
    'delete_term',
    'tfsc_combo_sitemap_term_changed',
    20,
    3
);


/**
 * ============================================================
 * RANK MATH CUSTOM SITEMAP PROVIDER
 * ============================================================
 */

add_filter(
    'rank_math/sitemap/providers',

    function( $providers ) {

        if (
            ! interface_exists(
                '\RankMath\Sitemap\Providers\Provider'
            )
        ) {
            return $providers;
        }


        if (
            ! class_exists(
                'TFSC_Combo_Vacancy_Sitemap_Provider',
                false
            )
        ) {

            class TFSC_Combo_Vacancy_Sitemap_Provider
                implements
                \RankMath\Sitemap\Providers\Provider {


                public function handles_type(
                    $type
                ) {

                    return (
                        'combo-vacatures'
                        === $type
                    );
                }


                public function get_index_links(
                    $max_entries
                ) {

                    $links =
                        tfsc_combo_sitemap_generate_links();


                    $total =
                        count(
                            $links
                        );


                    if ( 0 === $total ) {
                        return [];
                    }


                    $max_entries =
                        max(
                            1,
                            (int) $max_entries
                        );


                    $pages =
                        max(
                            1,
                            (int) ceil(
                                $total
                                /
                                $max_entries
                            )
                        );


                    $index = [];


                    for (
                        $page = 1;
                        $page <= $pages;
                        $page++
                    ) {

                        $suffix =
                            $pages > 1
                                ? $page
                                : '';


                        $offset =
                            (
                                $page - 1
                            )
                            *
                            $max_entries;


                        $page_links =
                            array_slice(
                                $links,
                                $offset,
                                $max_entries
                            );


                        $page_lastmod = '';


                        foreach (
                            $page_links
                            as $page_link
                        ) {

                            if (
                                empty(
                                    $page_link[
                                        'lastmod'
                                    ]
                                )
                            ) {
                                continue;
                            }


                            $page_lastmod =
                                tfsc_combo_sitemap_newest_lastmod(
                                    $page_lastmod,
                                    $page_link[
                                        'lastmod'
                                    ]
                                );
                        }


                        $index[] = [
                            'loc' =>
                                \RankMath\Sitemap\Router::get_base_url(
                                    'combo-vacatures-sitemap'
                                    .
                                    $suffix
                                    .
                                    '.xml'
                                ),

                            'lastmod' =>
                                $page_lastmod,
                        ];
                    }


                    return $index;
                }


                public function get_sitemap_links(
                    $type,
                    $max_entries,
                    $current_page
                ) {

                    if (
                        'combo-vacatures'
                        !== $type
                    ) {
                        return [];
                    }


                    $links =
                        tfsc_combo_sitemap_generate_links();


                    if ( ! $links ) {
                        return [];
                    }


                    $max_entries =
                        max(
                            1,
                            (int) $max_entries
                        );


                    $page =
                        max(
                            1,
                            (int) $current_page
                        );


                    $offset =
                        (
                            $page - 1
                        )
                        *
                        $max_entries;


                    return array_slice(
                        $links,
                        $offset,
                        $max_entries
                    );
                }
            }
        }


        $providers[
            'combo-vacatures'
        ] =
            new TFSC_Combo_Vacancy_Sitemap_Provider();


        return $providers;
    }
);
