<?php

/**
 * TFSC — Featured image ALT en TITLE, versie 2.0.
 * Code Snippets ID: 9; locatie: overal uitvoeren.
 * WordPress en Elementor, inclusief afbeeldingsresolutie Custom.
 * Alleen de featured image van het huidige afzonderlijke bericht.
 */

function tfsc_featured_image_text_context() {
    if ( ! is_singular() ) {
        return null;
    }

    $post_id = get_queried_object_id();
    $image_id = $post_id ? (int) get_post_thumbnail_id( $post_id ) : 0;

    if ( ! $image_id ) {
        return null;
    }

    // De HTML API verwacht ongeëscapete tekst en verzorgt zelf de escaping.
    $title = trim( html_entity_decode(
        wp_strip_all_tags( get_the_title( $post_id ) ),
        ENT_QUOTES,
        get_bloginfo( 'charset' ) ?: 'UTF-8'
    ) );

    if ( '' === $title ) {
        return null;
    }

    return [ 'image_id' => $image_id, 'title' => $title ];
}

// Normale WordPress-afbeeldingen, ongeacht de gekozen afbeeldingsgrootte.
add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
    $context = tfsc_featured_image_text_context();

    if ( ! $context || (int) $attachment->ID !== $context['image_id'] ) {
        return $attr;
    }

    $attr['alt'] = $context['title'];
    $attr['title'] = $context['title'];

    return $attr;
}, 10, 3 );

// Elementor bouwt bij Custom zelf HTML en slaat de WordPress-filter over.
add_filter( 'elementor/image_size/get_attachment_image_html', function( $html, $settings, $image_size_key, $image_key ) {
    if ( ! is_string( $html ) || '' === $html || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
        return $html;
    }

    $image_key = $image_key ?: $image_size_key;
    $image = $settings[ $image_key ] ?? null;

    if ( ! is_array( $image ) || empty( $image['id'] ) ) {
        return $html;
    }

    $context = tfsc_featured_image_text_context();

    if ( ! $context || (int) $image['id'] !== $context['image_id'] ) {
        return $html;
    }

    $processor = new WP_HTML_Tag_Processor( $html );

    if ( ! $processor->next_tag( 'IMG' ) ) {
        return $html;
    }

    $processor->set_attribute( 'alt', $context['title'] );
    $processor->set_attribute( 'title', $context['title'] );

    return $processor->get_updated_html();
}, 10, 4 );
