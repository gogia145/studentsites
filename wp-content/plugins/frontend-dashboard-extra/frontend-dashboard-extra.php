<?php
/**
 * Plugin Name: Frontend Dashboard Extra
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard-extra
 * Description: Front end dashboard provide high flexible way to customize the user dashboard on front end rather than WordPress wp-admin dashboard.
 * Version: 1.3.1
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
	define( 'BC_FED_EXTRA_PLUGIN_VERSION', '1.3.1' );

	/**
	 * App Name
	 */
	define( 'BC_FED_EXTRA_APP_NAME', 'Frontend Dashboard Extra' );

	/**
	 * Root Path
	 */
	define( 'BC_FED_EXTRA_PLUGIN', __FILE__ );
	/**
	 * Plugin Base Name
	 */
	define( 'BC_FED_EXTRA_PLUGIN_BASENAME', plugin_basename( BC_FED_EXTRA_PLUGIN ) );
	/**
	 * Plugin Name
	 */
	define( 'BC_FED_EXTRA_PLUGIN_NAME', trim( dirname( BC_FED_EXTRA_PLUGIN_BASENAME ), '/' ) );
	/**
	 * Plugin Directory
	 */
	define( 'BC_FED_EXTRA_PLUGIN_DIR', untrailingslashit( dirname( BC_FED_EXTRA_PLUGIN ) ) );


	require_once BC_FED_EXTRA_PLUGIN_DIR . '/menu/FEDE_Menu.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/functions.php';
} else {
	add_action( 'admin_notices', 'fed_global_admin_notification_extra' );
	function fed_global_admin_notification_extra() {
		?>
		<div class="notice notice-warning">
			<p>
				<b>
					<?php _e( 'Please install <a href="https://buffercode.com/plugin/frontend-dashboard">Frontend Dashboard</a> to use this plugin [Frontend Dashboard Extra]', 'frontend-dashboard-extra' );
					?>
				</b>
			</p>
		</div>
		<?php
	}
	?>
	<?php
}