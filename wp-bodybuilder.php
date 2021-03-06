<?php
/**
 * The plugin bootstrap file
 *
 * @link              http://www.adamkristopher.co/
 * @since             1.0.0
 * @package           Bodybuilder\plugin
 *
 * @wordpress-plugin
 * Plugin Name:       WP BodyBuilder
 * Plugin URI:        
 * Description:       Put exercises together to build a workout plan
 * Version:           1.0.0
 * Author:            Adam Carter
 * Author URI:        https://www.adamkristopher.co/
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

$plugin_url = plugin_dir_url( __FILE__ );
define( 'BODYBUILDER_URL', $plugin_url );
define( 'BODYBUILDER_DIR', plugin_dir_path( __DIR__ ) );
define( 'BODYBUILDER_VER', '1.0.0' );

/**
 * Class Init_Plugin
 */
class Init_Plugin {

	/**
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Construct function
	 *
	 * @return void
	 */
	public function __construct() {
		if ( is_admin ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		// Load public scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'public_scripts' ) );

		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall_plugin' ) );

		self::init_autoloader();
	}

	/**
	 * Enqueue public scripts and styles
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function public_scripts() {
		wp_enqueue_style(   'wpbb_grid',          BODYBUILDER_URL . 'assets/css/unsemantic-grid-responsive.css', BODYBUILDER_VER );
		wp_enqueue_style(   'wpbb_rateyo_styles', BODYBUILDER_URL . 'assets/css/jquery.rateyo.min.css', '2.3.2' );
		wp_enqueue_style(   'wpbb_styles',        BODYBUILDER_URL . 'assets/css/pub.css' );
		wp_enqueue_script(  'wpbb_pdf_script',    BODYBUILDER_URL . 'assets/js/jspdf.min.js', array( 'jquery' ), BODYBUILDER_VER, true );
		wp_enqueue_script(  'wpbb_html_script',   BODYBUILDER_URL . 'assets/js/html2canvas.min.js', array( 'jquery' ), BODYBUILDER_VER, true );
		wp_enqueue_script(  'wpbb_rateyo_script', BODYBUILDER_URL . 'assets/js/jquery.rateyo.min.js', array( 'jquery' ), BODYBUILDER_VER, false );
		wp_enqueue_script(  'wpbb_script',        BODYBUILDER_URL . 'assets/js/pub-script.js', array( 'jquery' ), BODYBUILDER_VER, false );
	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_style(  'wpbb_admin_styles', BODYBUILDER_URL . 'assets/css/admin.css', BODYBUILDER_VER );
		wp_enqueue_style(  'select2_css',       BODYBUILDER_URL . 'assets/css/select2.min.css', BODYBUILDER_VER );
		wp_enqueue_script( 'select2_js',        BODYBUILDER_URL . 'assets/js/select2.min.js', array( 'jquery' ), BODYBUILDER_VER, false );
		wp_enqueue_script( 'wpbb_admin_script', BODYBUILDER_URL . 'assets/js/admin-script.js', array( 'jquery' ), BODYBUILDER_VER, false );
		wp_enqueue_media();
	}

	/**
	 * Plugin activation handler
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate_plugin() {
		self::init_autoloader();
		flush_rewrite_rules();
	}

	/**
	 * The plugin is deactivating.  Delete out the rewrite rules option.
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function deactivate_plugin() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Uninstall plugin handler
	 *
	 * @since 1.0.1
	 *
	 * @return void
	 */
	public function uninstall_plugin() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Kick off the plugin by initializing the plugin files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function init_autoloader() {
		// Admin files
		require_once 'admin/custom/custom-fields.php';
		require_once 'admin/custom/post-types.php';
		require_once 'admin/custom/custom-tables.php';
		require_once 'admin/custom/post-custom-fields.php';
		require_once 'admin/custom/exercise-custom-fields.php';
		require_once 'admin/custom/set-custom-fields.php';

		//Public files
		require_once 'pub/frontend-ajax.php';
		require_once 'pub/workout.php';
		require_once 'pub/ratings.php';
		require_once 'pub/display-workout.php';

		// Testing
		require_once 'admin/root.php';
	}

	/**
	 * Return active instance of Init_Plugin, create one if it doesn't exist
	 *
	 * @return object $instance
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}
}
Init_Plugin::get_instance();
