<?php
/**
 * Created by Buffercode.
 * User: M A Vinoth Kumar
 */

add_action( 'admin_init', 'fed_pages_upgrade' );
function fed_pages_upgrade() {
	$new_version = FED_PAGES_PLUGIN_VERSION;
	$old_version = get_option( 'fed_pages_plugin_version', '0' );

	if ( $old_version == $new_version ) {
		return;
	}

	do_action( 'fed_pages_upgrade_action', $new_version, $old_version );

}

add_action('fed_pages_upgrade_action','fed_pages_upgrade_action_fn',10,2);
function fed_pages_upgrade_action_fn($new_version, $old_version) {
	global $wpdb;
	fed_common_pages_plugin_activation( $wpdb );
}

register_activation_hook( FED_PAGES_PLUGIN, 'fed_pages_plugin_activation' );

function fed_pages_plugin_activation( $network_wide ) {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			fed_common_pages_plugin_activation( $wpdb );
			restore_current_blog();
		}

	} else {
		fed_common_pages_plugin_activation( $wpdb );
	}

}
//
//function fed_mu_blog_install( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
//	global $wpdb;
//	switch_to_blog( $blog_id );
//	fed_common_pages_plugin_activation( $wpdb );
//	restore_current_blog();
//}
//
//add_action( 'wpmu_new_blog', 'fed_mu_blog_install', 10, 6 );

function fed_common_pages_plugin_activation( $wpdb ) {
	$fed_menu = $wpdb->prefix . BC_FED_MENU_DB;
	if ( ! $wpdb->get_col_length( $fed_menu, 'menu_key' ) ) {
		$wpdb->query( "ALTER TABLE $fed_menu ADD menu_value TEXT AFTER extended, ADD menu_key  VARCHAR(255) AFTER extended" );
	}
	update_option( 'fed_pages_plugin_version', FED_PAGES_PLUGIN_VERSION );
}