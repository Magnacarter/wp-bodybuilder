<?php
/**
 * Settings page
 *
 * Settings page for the plugin
 *
 * @since 1.0.0
 * @package Bodybuilder\plugin\admin\custom
 */

namespace Bodybuilder\plugin\admin\custom;
 
/**
 * Settings init
 *
 * custom option and settings
 * @since 1.0.0
 * @return void
 */
function settings_init() {

	// register a new setting for "wpbb-plugin" page
	register_setting( 'wpbb-plugin', 'wpbb_plugin_options' );

	// register a new section in the "wpbb-plugin" page
	add_settings_section(

		'section_developers',

		__( 'Welcome to the WP Bodybuilder Plugin!', 'wpbb-plugin' ),

		__NAMESPACE__ . '\section_developers_cb',

		'wpbb-plugin'

	);
 
	// register a new field in the "section_developers" section, inside the "wpbb-plugin" page
	add_settings_field(

		'get_started', // as of WP 4.6 this value is used only internally

		// use $args' label_for to populate the id inside the callback
		__( 'Get Started', 'wpbb-plugin' ),

		__NAMESPACE__ . '\get_started_cb',

		'wpbb-plugin',

		'section_developers',

		[
			'label_for'         => 'import_recipes',
			'class'             => 'scb_plugin_row',
			'custom_data'       => 'custom',
		]

	);

}
// register our settings_init to the admin_init action hook
add_action( 'admin_init',  __NAMESPACE__ . '\settings_init' );
 
/**
 * Section developers cb
 *
 * custom option and settings:
 * callback functions
 *
 * section callbacks can accept an $args parameter, which is an array.
 * $args have the following keys defined: title, id, callback.
 * the values are defined at the add_settings_section() function.
 * 
 * @param array $args
 * @since 1.0.0
 * @return void
 */
function section_developers_cb( $args ) {

	?>

		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'To start or not to start... that is the question', 'wpbb-plugin' ); ?></p>

	<?php

}
 
/**
 * Get started cb
 *
 * $args is defined at the add_settings_field() function.
 * wordpress has magic interaction with the following keys: label_for, class.
 * the "label_for" key value is used for the "for" attribute of the <label>.
 * the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * you can add custom key value pairs to be used inside your callbacks.
 * 
 * @param array $args
 * @since 1.0.0
 * @return void
 */
function get_started_cb( $args ) {

	// get the value of the setting we've registered with register_setting()
	$options = get_option( 'wpbb_plugin_options' );

}
 
/**
 * Options page
 *
 * top level menu
 *
 * @since 1.0.0
 * @action admin_menu
 * @return void
 */
function options_page() {

	// add top level menu page
	add_menu_page(
		'WP BodyBuilder',
		'WP Bodybuilder Options',
		'manage_options',
		'wpbb-plugin',
		__NAMESPACE__ . '\options_page_html'
	);

}
// register our wporg_options_page to the admin_menu action hook
add_action( 'admin_menu', __NAMESPACE__ . '\options_page' );
 
/**
 * Option page html
 *
 * top level menu:
 * callback functions
 *
 * @since 1.0.0
 * @return void
 */
function options_page_html() {

	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) )

		return;

	// get the value of the setting we've registered with register_setting()
	$options = get_option( 'wpbb_plugin_options' );
 
	// add error/update messages

	// check if the user have submitted the settings
	// wordpress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {

			// add settings saved message with the class of "updated"
			add_settings_error( 'wpbb_plugin_messages', 'wpbb_plugin_message', __( 'Lets get to it...', 'wpbb-plugin' ), 'updated' );

	}
 
	// show error/update messages
	settings_errors( 'wpbb_plugin_messages' );

	?>

	<div class="wrap">

		<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>

		<form action="options.php" method="post">

			<?php

			// output security fields for the registered setting "wporg"
			settings_fields( 'wpbb-plugin' );

			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections( 'wpbb-plugin' );

			// output save settings button
			submit_button( 'Get Started' );

			?>

		</form>

	</div>

	<?php

}
