<?php
/**
 * Create custom tables for wp-bodybuilder
 *
 * @package Bodybuilder\plugin\admin\custom
 * @since 1.0.0
 * @author Adam Carter
 * @licence GNU-2.0+
 */

namespace Bodybuilder\plugin\admin\custom;

/**
 * Class Custom_Table
 */
class Custom_Tables {

	/**
	 * @var $instance
	 */
	public static $instance;

	/**
	 * Custom_Tables constructor.
	 */
	public function __construct() {

		$this->install_workout_table();

	}

	/**
	 * Install workout table
	 *
	 * upon plugin activation, create custom table to store user workouts
	 *
	 * @return void
	 */
	public function install_workout_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'bodybuilder_workout';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		workout_id mediumint(9) NOT NULL AUTO_INCREMENT,
		workout_name text NOT NULL,
		workout text NOT NULL,
		activity_duration mediumint NOT NULL,
		activity_frequency mediumint NOT NULL,
		exercise_type text NOT NULL,
		intensity text NOT NULL,
		repetitions text NOT NULL,
		rest_periods mediumint NOT NULL,
		workload mediumint NOT NULL,
		author tinytext NOT NULL,
		notes text NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (workout_id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Save workout
	 *
	 * run array through to save values to the wpbb table
	 *
	 * @since 1.0.0
	 * @param array $args
	 * @param int $workout_id
	 * @return void
	 */
	public static function save_workout( $args, $workout_id = 0 ) {

		global $wpdb;
		$table_name = "wp_bodybuilder_workout";

		if( $workout_id === 0 ) {

			$wpdb->insert( $table_name, $args );

			return;

		}

		$wpdb->update( $table_name, $args, array( 'workout_id' => $workout_id ) );

	}

	/**
	 * Return active instance of Custom_Tables, create one if it doesn't exist
	 *
	 * @return Custom_Tables
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}

}

new Custom_Tables();