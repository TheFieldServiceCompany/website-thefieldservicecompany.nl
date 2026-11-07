<?php
/**
 * Plugin Name: TFSC Rank Math Breadcrumbs
 * Description: Preserves TFSC breadcrumbs, marked schema, query redirects, the local sitemap and automatic redirect cleanup with Rank Math Free.
 * Version: 0.3.1
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: The Field Service Company
 * License: GPL-2.0-or-later
 * Text Domain: tfsc-rank-math-breadcrumbs
 */

namespace TFSC\RankMathBreadcrumbs;

defined( 'ABSPATH' ) || exit;

/**
 * Reuse the stored widget type so template IDs, classes and settings survive.
 * Pro keeps ownership of its widget whenever it is active.
 */
function register_widget( $widgets_manager ) {

    if (
        defined( 'RANK_MATH_PRO_VERSION' ) ||
        ! function_exists( 'rank_math_the_breadcrumbs' ) ||
        ! class_exists( '\Elementor\Widget_Base' )
    ) {
        return;
    }

    require_once __DIR__ . '/widget-breadcrumbs.php';
    $widgets_manager->register( new Widget_Breadcrumbs() );
}

add_action( 'elementor/widgets/register', __NAMESPACE__ . '\\register_widget', 100 );

/** Preserve only entity links already marked in the original post content. */
function preserve_marked_entities( $data ) {
    global $post;
    if ( defined( 'RANK_MATH_PRO_VERSION' ) || ! class_exists( '\\RankMath\\Helpers\\HTML' ) || ! is_array( $data ) || ! is_singular() || empty( $data['WebPage'] ) || ! is_array( $data['WebPage'] ) || empty( $post->post_content ) || ! is_string( $post->post_content ) ) {
        return $data;
    }

    // Match the existing plain-text anchor convention, without tagging new links.
    if ( ! preg_match_all( '|<a[^>]+>([^<]+)</a>|', $post->post_content, $links ) ) {
        return $data;
    }
    foreach ( $links[0] as $link ) {
        $attributes = \RankMath\Helpers\HTML::extract_attributes( $link );
        $properties = explode( ' ', $attributes['data-schema-attribute'] ?? '' );
        if ( empty( $attributes['href'] ) ) {
            continue;
        }
        foreach ( [ 'about', 'mentions' ] as $property ) {
            if ( ! in_array( $property, $properties, true ) ) {
                continue;
            }
            if ( isset( $data['WebPage'][$property] ) && ! is_array( $data['WebPage'][$property] ) ) {
                continue;
            }
            if ( ! empty( $data['WebPage'][$property]['@id'] ) ) {
                $data['WebPage'][$property] = [ $data['WebPage'][$property] ];
            }
            $data['WebPage'][$property][] = [
                '@type' => 'Thing',
                'name' => wp_strip_all_tags( $link ),
                'sameAs' => $attributes['href'],
            ];
        }
    }
    return $data;
}

/** Let the Free redirect engine verify a stored rule against the full URI. */
function match_query_redirect( $matched, $redirection ) {
    if ( defined( 'RANK_MATH_PRO_VERSION' ) || $matched || ! is_array( $redirection ) || empty( $redirection['sources'] ) || ! class_exists( '\\RankMath\\Redirections\\DB' ) || ! class_exists( '\\RankMath\\Redirections\\Redirection' ) ) {
        return $matched;
    }
    return \RankMath\Redirections\DB::compare_sources(
        $redirection['sources'],
        \RankMath\Redirections\Redirection::get_full_uri()
    );
}

/** Keep the existing single-location sitemap using the generator in Free. */
function enable_single_location_sitemap( $enabled ) {
    if ( defined( 'RANK_MATH_PRO_VERSION' ) || ! class_exists( '\\RankMath\\Helper' ) || \RankMath\Helper::get_settings( 'titles.use_multiple_locations', false ) ) {
        return $enabled;
    }
    return $enabled || (bool) \RankMath\Helper::get_settings( 'sitemap.local_sitemap', false );
}

add_filter( 'rank_math/json_ld', __NAMESPACE__ . '\\preserve_marked_entities', 11 );
add_filter( 'rank_math/redirection/redirection_match', __NAMESPACE__ . '\\match_query_redirect', 10, 2 );
add_filter( 'rank_math/sitemap/locations', __NAMESPACE__ . '\\enable_single_location_sitemap' );

/** Pro owns this workflow whenever present; respect the redirect module switch. */
function can_manage_auto_redirects() {
    return ! defined( 'RANK_MATH_PRO_VERSION' )
        && class_exists( '\\RankMath\\Helper' )
        && \RankMath\Helper::is_module_active( 'redirections' )
        && class_exists( '\\RankMath\\Redirections\\DB' );
}

/** Accept only explicit positive IDs from the existing Pro association field. */
function associated_redirect_ids( $value ) {
    if ( ! is_array( $value ) ) {
        return [];
    }
    $ids = [];
    foreach ( $value as $id ) {
        if ( ( is_int( $id ) || is_string( $id ) ) && preg_match( '/^[1-9][0-9]*$/D', (string) $id ) ) {
            $integer = (int) $id;
            if ( $integer > 0 && (string) $integer === (string) $id ) {
                $ids[] = $integer;
            }
        }
    }
    return array_values( array_unique( $ids ) );
}

/** Private per-site key; never exposed as REST metadata or generated on reads. */
function redirect_signing_key( $create = false ) {
    $key = get_option( '_tfsc_rm_redirect_signing_key', '' );
    if ( ! $key && $create ) {
        try {
            $generated = bin2hex( random_bytes( 32 ) );
        } catch ( \Exception $error ) {
            return '';
        }
        add_option( '_tfsc_rm_redirect_signing_key', $generated, '', false );
        $key = get_option( '_tfsc_rm_redirect_signing_key', '' );
    }
    return is_string( $key ) && preg_match( '/^[a-f0-9]{64}$/D', $key ) ? $key : '';
}

/** Bind the association to this site, object type, object ID and exact ID list. */
function redirect_signature( $object_id, $type, $ids, $key ) {
    return hash_hmac( 'sha256', wp_json_encode( [ get_current_blog_id(), $type, (int) $object_id, $ids ] ), $key );
}

/** Untrusted Rank Math metadata never authorizes deletion on its own. */
function verified_redirect_ids( $object_id, $type ) {
    $proof = get_metadata( $type, $object_id, '_tfsc_rm_verified_redirects', true );
    $key = redirect_signing_key();
    if ( ! $key || ! is_array( $proof ) || ! isset( $proof['ids'], $proof['signature'] ) || ! is_string( $proof['signature'] ) ) {
        return [];
    }
    $ids = associated_redirect_ids( $proof['ids'] );
    return hash_equals( redirect_signature( $object_id, $type, $ids, $key ), $proof['signature'] ) ? $ids : [];
}

/** Internal PHP API for trusted watcher events and reviewed legacy migration. */
function seal_redirect_ids( $object_id, $type, $ids ) {
    if ( (int) $object_id <= 0 || ! in_array( $type, [ 'post', 'term' ], true ) ) {
        return false;
    }
    $key = redirect_signing_key( true );
    if ( ! $key ) {
        return false;
    }
    $ids = associated_redirect_ids( $ids );
    return update_metadata( $type, $object_id, '_tfsc_rm_verified_redirects', [
        'ids' => $ids, 'signature' => redirect_signature( $object_id, $type, $ids, $key ),
    ] );
}

/** Keep Pro-compatible associations for redirects made by the Free watcher. */
function remember_auto_redirect( $redirect_id, $object_id, $type ) {
    if ( ! class_exists( '\\RankMath\\Helper' ) || ! \RankMath\Helper::is_module_active( 'redirections' ) || ! in_array( $type, [ 'post', 'term' ], true ) ) {
        return;
    }
    $new_id = associated_redirect_ids( [ $redirect_id ] );
    if ( ! $new_id || (int) $object_id <= 0 ) {
        return;
    }
    // Do not launder a forged legacy field through a genuine later slug change.
    $ids = verified_redirect_ids( $object_id, $type );
    if ( ! in_array( $new_id[0], $ids, true ) ) {
        $ids[] = $new_id[0];
        seal_redirect_ids( $object_id, $type, $ids );
    }
    if ( ! defined( 'RANK_MATH_PRO_VERSION' ) ) {
        update_metadata( $type, $object_id, 'rank_math_auto_redirect', $ids );
    }
}

/** Delete only explicitly associated rules, using Free's cache-aware API. */
function remove_auto_redirects( $object_id, $type ) {
    if ( ! can_manage_auto_redirects() || ! in_array( $type, [ 'post', 'term' ], true ) ) {
        return;
    }
    $ids = verified_redirect_ids( $object_id, $type );
    if ( $ids ) {
        \RankMath\Redirections\DB::delete( $ids );
    }
}

function remember_post_redirect( $redirect_id, $post_id ) {
    remember_auto_redirect( $redirect_id, $post_id, 'post' );
}

function remember_term_redirect( $redirect_id, $term_id ) {
    remember_auto_redirect( $redirect_id, $term_id, 'term' );
}

function remove_post_redirects( $post_id ) {
    remove_auto_redirects( $post_id, 'post' );
}

function remove_term_redirects( $term_id ) {
    remove_auto_redirects( $term_id, 'term' );
}

add_action( 'rank_math/redirection/post_updated', __NAMESPACE__ . '\\remember_post_redirect', 20, 2 );
add_action( 'rank_math/redirection/term_updated', __NAMESPACE__ . '\\remember_term_redirect', 20, 2 );
add_action( 'before_delete_post', __NAMESPACE__ . '\\remove_post_redirects', 20 );
add_action( 'pre_delete_term', __NAMESPACE__ . '\\remove_term_redirects', 20 );
