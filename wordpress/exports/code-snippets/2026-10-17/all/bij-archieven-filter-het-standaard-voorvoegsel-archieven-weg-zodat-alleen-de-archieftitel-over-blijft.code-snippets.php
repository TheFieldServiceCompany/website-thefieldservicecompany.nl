<?php

/**
 * Bij archieven, filter het standaard voorvoegsel "Archieven" weg, zodat alleen de archieftitel over blijft. 
 */
add_filter( 'elementor/utils/get_the_archive_title', function( $title ) {

    if ( is_category() ) {
        return single_cat_title( '', false );
    }

    if ( is_tag() ) {
        return single_tag_title( '', false );
    }

    if ( is_tax() ) {
        return single_term_title( '', false );
    }

    if ( is_post_type_archive() ) {
        return post_type_archive_title( '', false );
    }

    return $title;
});
