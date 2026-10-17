<?php

/**
 * debug 6
 */
/**
 * TFSC - Combo Pagination Source Debug
 * Alleen debug.
 */

add_action(
    'wp_footer',
    function() {

        if (
            ! function_exists( 'tfsc_is_combo_archive' ) ||
            ! tfsc_is_combo_archive()
        ) {
            return;
        }

        global $wp_query;

        $debug = [
            'request_uri' =>
                $_SERVER['REQUEST_URI'] ?? '',

            'query_vars' => [
                'tfsc_combo_archive' =>
                    get_query_var( 'tfsc_combo_archive' ),

                'tfsc_combo_path' =>
                    get_query_var( 'tfsc_combo_path' ),

                'paged' =>
                    get_query_var( 'paged' ),

                'page' =>
                    get_query_var( 'page' ),
            ],

            'main_query' => [
                'is_main_query' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->is_main_query()
                        : null,

                'post_type' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->get( 'post_type' )
                        : null,

                'posts_per_page' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->get( 'posts_per_page' )
                        : null,

                'paged' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->get( 'paged' )
                        : null,

                'offset' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->get( 'offset' )
                        : null,

                'orderby' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->get( 'orderby' )
                        : null,

                'tax_query' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->get( 'tax_query' )
                        : null,

                'found_posts' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->found_posts
                        : null,

                'max_num_pages' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->max_num_pages
                        : null,

                'post_count' =>
                    $wp_query instanceof WP_Query
                        ? $wp_query->post_count
                        : null,

                'post_ids' =>
                    $wp_query instanceof WP_Query
                        ? wp_list_pluck(
                            $wp_query->posts,
                            'ID'
                        )
                        : [],
            ],
        ];

        echo "\n<!-- TFSC COMBO PAGINATION SOURCE DEBUG\n";
        echo esc_html(
            print_r(
                $debug,
                true
            )
        );
        echo "\n-->\n";
    },
    9999
);

/**
 * TFSC - Elementor / Secondary Query Debug
 */

add_action(
    'pre_get_posts',
    function( $query ) {

        if (
            is_admin() ||
            ! function_exists( 'tfsc_is_combo_archive' ) ||
            ! tfsc_is_combo_archive() ||
            $query->is_main_query()
        ) {
            return;
        }

        $post_type = $query->get( 'post_type' );

        if (
            'vacature' !== $post_type &&
            ! (
                is_array( $post_type ) &&
                in_array( 'vacature', $post_type, true )
            )
        ) {
            return;
        }

        add_action(
            'wp_footer',
            function() use ( $query ) {

                $debug = [
                    'is_main_query' =>
                        $query->is_main_query(),

                    'post_type' =>
                        $query->get( 'post_type' ),

                    'posts_per_page' =>
                        $query->get( 'posts_per_page' ),

                    'paged' =>
                        $query->get( 'paged' ),

                    'orderby' =>
                        $query->get( 'orderby' ),

                    'search_filter_query_id' =>
                        $query->get( 'search_filter_query_id' ),

                    'tax_query' =>
                        $query->get( 'tax_query' ),

                    'found_posts' =>
                        $query->found_posts,

                    'max_num_pages' =>
                        $query->max_num_pages,

                    'post_count' =>
                        $query->post_count,

                    'post_ids' =>
                        wp_list_pluck(
                            $query->posts,
                            'ID'
                        ),
                ];

                echo "\n<!-- TFSC SECONDARY VACANCY QUERY DEBUG\n";
                echo esc_html(
                    print_r(
                        $debug,
                        true
                    )
                );
                echo "\n-->\n";
            },
            9999
        );
    },
    9999
);

/**
 * TFSC - Elementor Query Results Debug
 * Alleen debug.
 */

add_action(
    'elementor/query/query_results',
    function( $query ) {

        if (
            ! function_exists( 'tfsc_is_combo_archive' ) ||
            ! tfsc_is_combo_archive()
        ) {
            return;
        }

        if (
            ! $query instanceof WP_Query
        ) {
            return;
        }

        $debug = [
            'post_type' =>
                $query->get( 'post_type' ),

            'posts_per_page' =>
                $query->get( 'posts_per_page' ),

            'paged' =>
                $query->get( 'paged' ),

            'orderby' =>
                $query->get( 'orderby' ),

            'search_filter_query_id' =>
                $query->get( 'search_filter_query_id' ),

            'tax_query' =>
                $query->get( 'tax_query' ),

            'found_posts' =>
                $query->found_posts,

            'max_num_pages' =>
                $query->max_num_pages,

            'post_count' =>
                $query->post_count,

            'post_ids' =>
                wp_list_pluck(
                    $query->posts,
                    'ID'
                ),
        ];

        add_action(
            'wp_footer',
            function() use ( $debug ) {

                echo "\n<!-- TFSC ELEMENTOR QUERY RESULTS DEBUG\n";
                echo esc_html(
                    print_r(
                        $debug,
                        true
                    )
                );
                echo "\n-->\n";
            },
            9999
        );
    },
    9999
);

/**
 * TFSC - Search & Filter Query Args Debug
 * Alleen debug.
 */

add_filter(
    'search-filter/query/query_args',
    function( $query_args, $query ) {

        if (
            ! function_exists( 'tfsc_is_combo_archive' ) ||
            ! tfsc_is_combo_archive()
        ) {
            return $query_args;
        }

        $debug = [
            'request_uri' =>
                $_SERVER['REQUEST_URI'] ?? '',

            'query_args' =>
                $query_args,
        ];

        add_action(
            'wp_footer',
            function() use ( $debug ) {

                echo "\n<!-- TFSC SEARCH FILTER QUERY DEBUG\n";
                echo esc_html(
                    print_r(
                        $debug,
                        true
                    )
                );
                echo "\n-->\n";
            },
            9999
        );

        return $query_args;
    },
    9999,
    2
);
