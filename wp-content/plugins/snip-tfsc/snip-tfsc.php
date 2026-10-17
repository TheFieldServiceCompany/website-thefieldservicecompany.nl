<?php
/*
Plugin Name: SNIP Job Places for TheFieldServiceCompany
Description: A plugin that adds a new Field Type in SNIP.
Author: Florian Simeth
Version: 0.2.0
Author URI: https://rich-snippets.io
Plugin URI: https://rich-snippets.io/how-to-add-your-own-field-type/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 *
 * PHP Version check.
 *
 */
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
	add_action( 'admin_notices', 'snip_tfsc_old_php_notice' );

	function snip_tfsc_old_php_notice() {

		printf(
			'<div class="notice error"><p>%s</p></div>',
			sprintf(
				__( 'Hey mate! Sorry for interrupting you. It seem\'s that you\'re using an old PHP version (your current version is %s). You should upgrade to at least 7.0 or higher in order to use the SNIP Field Type Example plugin. Thank you!', 'snip-fte' ),
				esc_html( PHP_VERSION )
			)
		);
	}

	# sorry. The plugin will not work with an old PHP version.
	return;
}


add_filter( 'wpbuddy/rich_snippets/fields/internal_subselect/values', 'snip_tfsc_subselects' );

/**
 * Adds new field to use in Global Snippets in the SNIP plugin.
 *
 * @param array $values
 *
 * @return array
 * @since 0.1.0
 *
 */
function snip_tfsc_subselects( $values ) {

	$values['http://schema.org/Place'][] = [
		'id'     => 'snip_fte_pods_places',
		'label'  => esc_html_x( 'PODs Places', 'subselect field', 'snip-fte' ),
		'method' => 'snip_tfsc_pods_places',
	];

	$values['http://schema.org/AdministrativeArea'][] = [
		'id'     => 'snip_fte_pods_areas',
		'label'  => esc_html_x( 'PODs Areas', 'subselect field', 'snip-fte' ),
		'method' => 'snip_tfsc_pods_areas',
	];

	return $values;
}

function snip_tfsc_is_state( $location_id ) {
	$a = [
		'streetaddress',
		'addresslocality',
		'postalcode',
		'addresscountry'
	];

	$state = get_term_meta( $location_id, 'addressregion', true );

	if ( empty( $state ) ) {
		return false;
	}

	foreach ( $a as $b ) {
		$v = get_term_meta( $location_id, $b, true );
		if ( ! empty( $v ) ) {
			return false;
		}
	}

	return true;
}

function snip_tfsc_is_country( $location_id ) {
	$a = [
		'streetaddress',
		'addresslocality',
		'postalcode',
		'addressregion'
	];

	$country = get_term_meta( $location_id, 'addresscountry', true );

	if ( empty( $country ) ) {
		return false;
	}

	foreach ( $a as $b ) {
		$v = get_term_meta( $location_id, $b, true );
		if ( ! empty( $v ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Returns the value.
 *
 * @param                                     $val
 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
 * @param array $meta_info
 *
 * @return object|object[]|string
 */
function snip_tfsc_pods_places( $val, \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet, array $meta_info ) {

	$terms = get_the_terms( $meta_info['current_post_id'], 'locatie' );

	if ( ! is_array( $terms ) ) {
		return '';
	}

	$places = [];

	foreach ( $terms as $term ) {

		if ( snip_tfsc_is_state( $term->term_id ) ) {
			continue;
		}

		if ( snip_tfsc_is_country( $term->term_id ) ) {
			continue;
		}

		$o                        = new stdClass();
		$o->{'@type'}             = 'Place';
		$o->{'@context'}          = 'https://schema.org';
		$o->address               = new stdClass();
		$o->address->{'@type'}    = 'PostalAddress';
		$o->address->{'@context'} = 'https://schema.org';

		$fields = [
			'streetAddress',
			'addressLocality',
			'addressRegion',
			'postalCode',
			'addressCountry',
		];

		foreach ( $fields as $field ) {
			$value = trim( get_term_meta( $term->term_id, strtolower( $field ), true ) );
			if ( empty( $value ) ) {
				continue;
			}

			$o->address->{$field} = $value;
		}

		$places[] = $o;
	}

	if ( 1 === count( $places ) ) {
		return reset( $places );
	}

	return $places;

}

/**
 * Returns the value.
 *
 * @param                                     $val
 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
 * @param array $meta_info
 *
 * @return object|object[]|string
 */
function snip_tfsc_pods_areas( $val, \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet, array $meta_info ) {
	$terms = get_the_terms( $meta_info['current_post_id'], 'locatie' );

	if ( ! is_array( $terms ) ) {
		return new stdClass();
	}

	$isTelecommuteJob = call_user_func( function ( $terms ) {
		foreach ( $terms as $term ) {
			$name = strtoupper( $term->name );
			if ( $name === 'TELECOMMUTE' ) {
				return true;
			}
		}

		return false;
	}, $terms );

	if ( ! $isTelecommuteJob ) {
		return '';
	}

	$places = [];

	foreach ( $terms as $term ) {
		$name = strtoupper( $term->name );
		if ( $name === 'TELECOMMUTE' ) {
			continue;
		}

		$o               = new stdClass();
		$o->{'@context'} = 'https://schema.org';

		if ( snip_tfsc_is_state( $term->term_id ) ) {
			$o->{'@type'} = 'State';
			$o->name      = get_term_meta( $term->term_id, 'addressregion', true );
			$places[]     = $o;
			continue;
		}

		if ( snip_tfsc_is_country( $term->term_id ) ) {
			$o->{'@type'} = 'Country';
			$o->name      = get_term_meta( $term->term_id, 'addresscountry', true );
			$places[]     = $o;
		}
	}

	if ( 1 === count( $places ) ) {
		return reset( $places );
	}

	return $places;

}
