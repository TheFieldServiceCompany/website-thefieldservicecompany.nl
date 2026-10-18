<?php

/**
 * TFSC - Archive title without prefix + optional term override [v2.0]
 *
 * Source snippet ID: 39
 * Scope: global
 *
 * Taxonomy term archives may provide a custom visible archive heading in
 * term meta field `pods_archive_title`. When the field is empty or missing,
 * the normal term name remains the fallback.
 *
 * This filter controls Elementors dynamic Archive Title tag. It does not
 * change the term name, slug, URL, breadcrumb or Rank Math SEO title.
 */

add_filter( 'elementor/utils/get_the_archive_title', function( $title ) {

    if ( is_category() ) {
        return single_cat_title( '', false );
    }

    if ( is_tag() ) {
        return single_tag_title( '', false );
    }

    if ( is_tax() ) {

        $term = get_queried_object();

        if ( $term instanceof WP_Term ) {

            $override = get_term_meta(
                $term->term_id,
                'pods_archive_title',
                true
            );

            $override = is_string( $override )
                ? trim( wp_strip_all_tags( $override ) )
                : '';

            if ( '' !== $override ) {
                return $override;
            }
        }

        return single_term_title( '', false );
    }

    if ( is_post_type_archive() ) {
        return post_type_archive_title( '', false );
    }

    return $title;
});
