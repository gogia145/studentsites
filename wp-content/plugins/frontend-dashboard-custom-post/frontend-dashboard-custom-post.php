<?php
/**
 * Plugin Name: Frontend Dashboard Custom Post
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard-custom-post-and-taxonomies
 * Description: Frontend Dashboard Custom Post is a plugin to show the custom post inside the Frontend Dashboard.
 * Version: 1.3.2
 * Author: vinoth06
 * Author URI: http://buffercode.com/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$fed_check = get_option( 'fed_plugin_version' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( $fed_check && is_plugin_active( 'frontend-dashboard/frontend-dashboard.php' ) ) {

	/**
	 * Version Number
	 */
	define( 'FED_CP_PLUGIN_VERSION', '1.3.2' );

	/**
	 * App Name
	 */
	define( 'FED_CP_APP_NAME', 'Frontend Dashboard Pages' );

	/**
	 * Root Path
	 */
	define( 'FED_CP_PLUGIN', __FILE__ );
	/**
	 * Plugin Base Name
	 */
	define( 'FED_CP_PLUGIN_BASENAME', plugin_basename( FED_CP_PLUGIN ) );
	/**
	 * Plugin Name
	 */
	define( 'FED_CP_PLUGIN_NAME', trim( dirname( FED_CP_PLUGIN_BASENAME ), '/' ) );
	/**
	 * Plugin Directory
	 */
	define( 'FED_CP_PLUGIN_DIR', untrailingslashit( dirname( FED_CP_PLUGIN ) ) );

	require_once FED_CP_PLUGIN_DIR . '/install.php';
	require_once FED_CP_PLUGIN_DIR . '/menu/FEDCP_Menu.php';
	require_once FED_CP_PLUGIN_DIR . '/menu/FEDCP_CustomPosts.php';
	require_once FED_CP_PLUGIN_DIR . '/menu/FEDCP_Taxonomies.php';
	require_once FED_CP_PLUGIN_DIR . '/functions.php';

	fed_custom_post_install();
} else {
	function fed_global_admin_notification_post() {
		?>
		<div class="notice notice-warning">
			<p>
				<b>
					<?php _e( 'Please install <a href="https://buffercode.com/plugin/frontend-dashboard">Frontend Dashboard</a> to use this plugin [Frontend Dashboard Custom Post and Taxonomies]', 'frontend-dashboard-custom-post' );
					?>
				</b>
			</p>
		</div>
		<?php

	}

	add_action( 'admin_notices', 'fed_global_admin_notification_post' );
}