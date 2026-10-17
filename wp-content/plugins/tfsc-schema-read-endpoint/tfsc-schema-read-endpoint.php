<?php
/**
 * Plugin Name: TFSC Schema Read Endpoint
 * Description: Read-only REST endpoint for TFSC WordPress/Pods structure inventory.
 * Version: 2.0.0
 * Author: The Field Service Company
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route(
        'tfsc/v1',
        '/schema',
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'tfsc_schema_read_endpoint_v2',
            'permission_callback' => 'tfsc_schema_read_permission_v2',
        )
    );
} );

function tfsc_schema_read_permission_v2( WP_REST_Request $request ) {
    return is_user_logged_in() && current_user_can( 'read' );
}

function tfsc_schema_safe_scalar( $value ) {
    if ( is_null( $value ) || is_scalar( $value ) ) {
        return $value;
    }

    if ( is_array( $value ) ) {
        $out = array();
        foreach ( $value as $k => $v ) {
            if ( is_null( $v ) || is_scalar( $v ) ) {
                $out[ $k ] = $v;
            } elseif ( is_array( $v ) ) {
                $out[ $k ] = tfsc_schema_safe_scalar( $v );
            }
        }
        return $out;
    }

    return null;
}

function tfsc_schema_normalize_pods_field_v2( $field ) {

    if ( is_object( $field ) ) {
        if ( method_exists( $field, 'export' ) ) {
            try {
                $exported = $field->export();
                if ( is_array( $exported ) ) {
                    $field = $exported;
                }
            } catch ( Throwable $e ) {
                $field = get_object_vars( $field );
            }
        } else {
            $field = get_object_vars( $field );
        }
    }

    if ( ! is_array( $field ) ) {
        return null;
    }

    $keys = array(
        'id',
        'name',
        'label',
        'description',
        'type',
        'weight',
        'required',
        'unique',
        'repeatable',
        'default_value',
        'pick_object',
        'pick_val',
        'sister_id',
        'group_id',
        'options',
    );

    $out = array();

    foreach ( $keys as $key ) {
        if ( array_key_exists( $key, $field ) ) {
            $out[ $key ] = tfsc_schema_safe_scalar( $field[ $key ] );
        }
    }

    /*
     * Pods stores some field configuration under nested "options".
     * Preserve relationship/select-relevant configuration only.
     */
    if ( isset( $field['options'] ) && is_array( $field['options'] ) ) {
        $interesting = array(
            'pick_object',
            'pick_val',
            'pick_format_type',
            'pick_format_multi',
            'pick_allow_add_new',
            'pick_table_id',
            'pick_display',
            'pick_where',
            'pick_orderby',
            'pick_groupby',
            'pick_limit',
            'pick_user_role',
            'pick_post_status',
            'pick_taxonomy',
            'pick_custom',
            'data',
            'default',
        );

        $filtered = array();

        foreach ( $interesting as $key ) {
            if ( array_key_exists( $key, $field['options'] ) ) {
                $filtered[ $key ] = tfsc_schema_safe_scalar( $field['options'][ $key ] );
            }
        }

        if ( ! empty( $filtered ) ) {
            $out['options_filtered'] = $filtered;
        }
    }

    return $out;
}

function tfsc_schema_normalize_pod_v2( $pod_name, $pod_data ) {

    if ( is_object( $pod_data ) ) {
        if ( method_exists( $pod_data, 'export' ) ) {
            try {
                $exported = $pod_data->export();
                if ( is_array( $exported ) ) {
                    $pod_data = $exported;
                }
            } catch ( Throwable $e ) {
                $pod_data = get_object_vars( $pod_data );
            }
        } else {
            $pod_data = get_object_vars( $pod_data );
        }
    }

    if ( ! is_array( $pod_data ) ) {
        return null;
    }

    $out = array(
        'name' => $pod_name,
    );

    foreach ( array(
        'id',
        'label',
        'description',
        'type',
        'object',
        'storage',
        'pod_index',
    ) as $key ) {
        if ( array_key_exists( $key, $pod_data ) ) {
            $out[ $key ] = tfsc_schema_safe_scalar( $pod_data[ $key ] );
        }
    }

    $fields = array();

    if ( isset( $pod_data['fields'] ) && is_array( $pod_data['fields'] ) ) {
        foreach ( $pod_data['fields'] as $field_name => $field ) {
            $normalized = tfsc_schema_normalize_pods_field_v2( $field );

            if ( $normalized ) {
                if ( ! isset( $normalized['name'] ) ) {
                    $normalized['name'] = is_string( $field_name ) ? $field_name : null;
                }
                $fields[] = $normalized;
            }
        }
    }

    $out['fields'] = $fields;

    return $out;
}

function tfsc_schema_load_pods_via_api_v2() {

    $result = array(
        'available' => false,
        'message'   => null,
        'pods'      => array(),
        'debug'     => array(),
    );

    if ( ! function_exists( 'pods_api' ) ) {
        $result['message'] = 'pods_api() is not available.';
        return $result;
    }

    try {
        $api = pods_api();

        if ( ! $api ) {
            $result['message'] = 'pods_api() returned no API object.';
            return $result;
        }

        $result['debug']['api_class'] = is_object( $api ) ? get_class( $api ) : gettype( $api );

        /*
         * First try load_pods().
         */
        if ( method_exists( $api, 'load_pods' ) ) {
            try {
                $loaded = $api->load_pods();

                $result['debug']['load_pods_type'] = gettype( $loaded );

                if ( is_array( $loaded ) ) {
                    $result['debug']['load_pods_count'] = count( $loaded );

                    foreach ( $loaded as $key => $pod_data ) {
                        $pod_name = is_string( $key ) ? $key : null;

                        if ( ! $pod_name && is_array( $pod_data ) && ! empty( $pod_data['name'] ) ) {
                            $pod_name = $pod_data['name'];
                        }

                        if ( ! $pod_name && is_object( $pod_data ) && isset( $pod_data->name ) ) {
                            $pod_name = $pod_data->name;
                        }

                        $pod_name = $pod_name ?: 'pod_' . count( $result['pods'] );

                        $normalized = tfsc_schema_normalize_pod_v2( $pod_name, $pod_data );
                        if ( $normalized ) {
                            $result['pods'][] = $normalized;
                        }
                    }
                }
            } catch ( Throwable $e ) {
                $result['debug']['load_pods_error'] = $e->getMessage();
            }
        }

        /*
         * Fallback: use load_pod() on actual Pods config posts.
         * This avoids depending on the exact return shape of load_pods().
         */
        if ( empty( $result['pods'] ) && method_exists( $api, 'load_pod' ) ) {

            $pod_posts = get_posts(
                array(
                    'post_type'      => '_pods_pod',
                    'post_status'    => 'any',
                    'posts_per_page' => -1,
                    'orderby'        => 'ID',
                    'order'          => 'ASC',
                    'fields'         => 'ids',
                    'no_found_rows'  => true,
                )
            );

            $result['debug']['pods_config_post_count'] = count( $pod_posts );

            foreach ( $pod_posts as $pod_post_id ) {
                try {
                    $pod_data = $api->load_pod(
                        array(
                            'id'   => (int) $pod_post_id,
                            'full' => true,
                        )
                    );

                    if ( empty( $pod_data ) ) {
                        continue;
                    }

                    $pod_name = null;

                    if ( is_array( $pod_data ) && ! empty( $pod_data['name'] ) ) {
                        $pod_name = $pod_data['name'];
                    } elseif ( is_object( $pod_data ) && isset( $pod_data->name ) ) {
                        $pod_name = $pod_data->name;
                    }

                    if ( ! $pod_name ) {
                        $pod_name = get_post_field( 'post_name', $pod_post_id );
                    }

                    $normalized = tfsc_schema_normalize_pod_v2( $pod_name, $pod_data );

                    if ( $normalized ) {
                        $result['pods'][] = $normalized;
                    }

                } catch ( Throwable $e ) {
                    $result['debug']['load_pod_errors'][] = array(
                        'pod_post_id' => (int) $pod_post_id,
                        'message'     => $e->getMessage(),
                    );
                }
            }
        }

        $result['available'] = true;

        if ( ! empty( $result['pods'] ) ) {
            $result['message'] = 'Pods structure loaded.';
        } else {
            $result['message'] = 'Pods API is available, but no Pods structures were returned.';
        }

        return $result;

    } catch ( Throwable $e ) {
        $result['message'] = 'Pods structure could not be loaded: ' . $e->getMessage();
        return $result;
    }
}

function tfsc_schema_read_endpoint_v2( WP_REST_Request $request ) {

    $response = array(
        'generated_at' => gmdate( 'c' ),
        'site_url'     => site_url(),
        'wordpress'    => array(
            'version' => get_bloginfo( 'version' ),
        ),
        'post_types'   => array(),
        'taxonomies'   => array(),
        'pods_status'  => array(),
        'pods'         => array(),
    );

    $post_types = get_post_types( array(), 'objects' );

    foreach ( $post_types as $name => $post_type ) {
        $response['post_types'][ $name ] = array(
            'name'         => $name,
            'label'        => isset( $post_type->label ) ? $post_type->label : null,
            'description'  => isset( $post_type->description ) ? $post_type->description : null,
            'public'       => isset( $post_type->public ) ? (bool) $post_type->public : null,
            'hierarchical' => isset( $post_type->hierarchical ) ? (bool) $post_type->hierarchical : null,
            'show_in_rest' => isset( $post_type->show_in_rest ) ? (bool) $post_type->show_in_rest : null,
            'rest_base'    => isset( $post_type->rest_base ) ? $post_type->rest_base : null,
            'taxonomies'   => get_object_taxonomies( $name ),
        );
    }

    $taxonomies = get_taxonomies( array(), 'objects' );

    foreach ( $taxonomies as $name => $taxonomy ) {

        $term_rows = array();

        $terms = get_terms(
            array(
                'taxonomy'   => $name,
                'hide_empty' => false,
            )
        );

        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $term_rows[] = array(
                    'term_id'     => (int) $term->term_id,
                    'name'        => $term->name,
                    'slug'        => $term->slug,
                    'description' => $term->description,
                    'parent'      => (int) $term->parent,
                    'count'       => (int) $term->count,
                );
            }
        }

        $response['taxonomies'][ $name ] = array(
            'name'         => $name,
            'label'        => isset( $taxonomy->label ) ? $taxonomy->label : null,
            'description'  => isset( $taxonomy->description ) ? $taxonomy->description : null,
            'public'       => isset( $taxonomy->public ) ? (bool) $taxonomy->public : null,
            'hierarchical' => isset( $taxonomy->hierarchical ) ? (bool) $taxonomy->hierarchical : null,
            'show_in_rest' => isset( $taxonomy->show_in_rest ) ? (bool) $taxonomy->show_in_rest : null,
            'rest_base'    => isset( $taxonomy->rest_base ) ? $taxonomy->rest_base : null,
            'object_type'  => isset( $taxonomy->object_type ) ? array_values( $taxonomy->object_type ) : array(),
            'terms'        => $term_rows,
        );
    }

    $pods_result = tfsc_schema_load_pods_via_api_v2();

    $response['pods_status'] = array(
        'available' => $pods_result['available'],
        'message'   => $pods_result['message'],
        'count'     => count( $pods_result['pods'] ),
        'debug'     => $pods_result['debug'],
    );

    $response['pods'] = $pods_result['pods'];

    return rest_ensure_response( $response );
}
