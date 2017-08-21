<?php
/**
 * The plugin bootstrap file
 *
 * @link              http://www.adamkristopher.com/
 * @since             1.0.0
 * @package           Bodybuilder\plugin
 *
 * @wordpress-plugin
 * Plugin Name:       WP BodyBuilder
 * Plugin URI:        
 * Description:       Put exercises together to build a workout plan
 * Version:           1.0.0
 * Author:            Adam Carter
 * Author URI:        https://www.adamkristopher.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-bodybuilder
 * Domain Path:       /languages
 */

namespace Bodybuilder\plugin;

use Bodybuilder\plugin\admin\custom\Custom_Tables;

if ( ! defined( 'ABSPATH' ) ) {

	exit( 'Cheatin&#8217?' );

}

/**
 * Setup the plugin's constants.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_constants() {

	$plugin_url = plugin_dir_url( __FILE__ );

	if ( is_ssl() ) {

		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );

	}

	define( 'BODYBUILDER_URL', $plugin_url );

	define( 'BODYBUILDER_DIR', plugin_dir_path( __DIR__ ) );

	define( 'ADMIN_CSS_VERSION', '1.0.0' );

	define( 'ADMIN_JS_VERSION', '1.0.0' );

	define( 'PUB_JS_VER', '1.0.0' );

}

/**
 * Enqueue public scripts and styles
 *
 * @since 1.0.0
 * @return void
 */
function public_scripts() {

	wp_enqueue_style(   'wpbb_grid',   BODYBUILDER_URL . 'pub/css/unsemantic-grid-responsive.css' );
	wp_enqueue_style(   'wpbb_styles', BODYBUILDER_URL . 'pub/css/pub.css' );
	wp_enqueue_script(  'wpbb_script', BODYBUILDER_URL . 'pub/js/pub-script.js', array( 'jquery' ), PUB_JS_VER, false );

}

/**
 * Enqueue admin scripts and styles
 *
 * @since 1.0.0
 * @return void
 */
function admin_scripts() {

	wp_enqueue_style(  'wpbb_admin_grid',   BODYBUILDER_URL . 'admin/css/unsemantic-grid-responsive.css' );
	wp_enqueue_style(  'wpbb_admin_styles', BODYBUILDER_URL . 'admin/css/admin.css', ADMIN_CSS_VERSION );
	wp_enqueue_script( 'wpbb_admin_script', BODYBUILDER_URL . 'admin/js/admin-script.js', array( 'jquery' ), ADMIN_JS_VERSION, false );
	wp_enqueue_media();

}

/**
 * Initialize the plugin hooks
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_hooks() {

	register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );

	register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_plugin' );

	register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall_plugin' );

	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\public_scripts' );

	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_scripts' );

}

/**
 * Plugin activation handler
 *
 * @since 1.0.0
 *
 * @return void
 */
function activate_plugin() {

	init_autoloader();

	flush_rewrite_rules();

	new Custom_Tables();

}

/**
 * The plugin is deactivating.  Delete out the rewrite rules option.
 *
 * @since 1.0.1
 *
 * @return void
 */
function deactivate_plugin() {

	delete_option( 'rewrite_rules' );

}

/**
 * Uninstall plugin handler
 *
 * @since 1.0.1
 *
 * @return void
 */
function uninstall_plugin() {

	delete_option( 'rewrite_rules' );

}

/**
 * Kick off the plugin by initializing the plugin files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_autoloader() {

	// Admin files
	require_once( 'admin/custom/post-types.php' );

	require_once( 'admin/custom/custom-tables.php' );

	require_once( 'admin/custom/settings-page.php' );

	require_once( 'admin/custom/exercise-custom-fields.php' );

	//Public files

	// Testing
	require_once( 'admin/root.php' );

}

/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {

	init_autoloader();

}
add_action( 'plugins_loaded', __NAMESPACE__ . '\launch' );

init_constants();

init_hooks();