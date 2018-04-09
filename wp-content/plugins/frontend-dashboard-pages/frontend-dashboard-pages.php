<?php
/**
 * Plugin Name: Frontend Dashboard Pages
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard-pages
 * Description: Frontend Dashboard Pages is a plugin to show pages inside the Frontend Dashboard menu. The assigning page may contain content, images and even shortcodes
 * Version: 1.5
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

include_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( $fed_check && is_plugin_active( 'frontend-dashboard/frontend-dashboard.php' ) ) {

	/**
	 * Version Number
	 */
	define( 'FED_PAGES_PLUGIN_VERSION', '1.4' );

	/**
	 * App Name
	 */
	define( 'FED_PAGES_APP_NAME', 'Frontend Dashboard Pages' );

	/**
	 * Root Path
	 */
	define( 'FED_PAGES_PLUGIN', __FILE__ );
	/**
	 * Plugin Base Name
	 */
	define( 'FED_PAGES_PLUGIN_BASENAME', plugin_basename( FED_PAGES_PLUGIN ) );
	/**
	 * Plugin Name
	 */
	define( 'FED_PAGES_PLUGIN_NAME', trim( dirname( FED_PAGES_PLUGIN_BASENAME ), '/' ) );
	/**
	 * Plugin Directory
	 */
	define( 'FED_PAGES_PLUGIN_DIR', untrailingslashit( dirname( FED_PAGES_PLUGIN ) ) );


	require_once FED_PAGES_PLUGIN_DIR . '/install.php';
	require_once FED_PAGES_PLUGIN_DIR . '/main_menu/FEDP_MainMenu.php';
	require_once FED_PAGES_PLUGIN_DIR . '/functions.php';
} else {
		function fed_global_admin_notification_pages() {
			?>
			<div class="notice notice-warning">
				<p>
					<b>
						<?php _e( 'Please install <a href="https://buffercode.com/plugin/frontend-dashboard">Frontend Dashboard</a> to use this plugin [Frontend Dashboard Pages]', 'fed' );
						?>
					</b>
				</p>
			</div>
			<?php
	}
	add_action( 'admin_notices', 'fed_global_admin_notification_pages' );
	?>
	<?php
}