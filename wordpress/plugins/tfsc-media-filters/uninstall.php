<?php
/** Remove only this plugin's index; media and HappyFiles terms remain intact. */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

function tfsc_mf_uninstall_site() {
	global $wpdb;
	$table = $wpdb->prefix . 'tfsc_media_index';
	$wpdb->query( "DROP TABLE IF EXISTS `$table`" );
	foreach ( array( 'tfsc_mf_index_state', 'tfsc_mf_schema', 'tfsc_mf_index_lock' ) as $option ) {
		delete_option( $option );
	}
}

if ( is_multisite() ) {
	$offset = 0;
	do {
		$sites = get_sites( array( 'fields' => 'ids', 'number' => 100, 'offset' => $offset ) );
		foreach ( $sites as $site_id ) {
			switch_to_blog( $site_id );
			tfsc_mf_uninstall_site();
			restore_current_blog();
		}
		$offset += 100;
	} while ( count( $sites ) === 100 );
} else {
	tfsc_mf_uninstall_site();
}
