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

		$wpdb->bodybuilder_workout = $wpdb->prefix . 'bodybuilder_workout';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		workout_id mediumint(9) NOT NULL AUTO_INCREMENT,
		workout_name text NOT NULL,
		workout text NOT NULL,
		workout_category text NOT NULL,
		workout_image text NOT NULL,
		workout_instructions text NOT NULL,
		workout_duration text NOT NULL,
		workout_frequency text NOT NULL,
		workout_intensity text NOT NULL,
		workout_repetitions text NOT NULL,
		workout_rest_periods text NOT NULL,
		workout_workload text NOT NULL,
		workout_author tinytext NOT NULL,
		workout_description text NOT NULL,
		rating_count mediumint(9) NOT NULL,
		rating_total mediumint(9) NOT NULL,
		rating_average mediumint(9) NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (workout_id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Get workout meta
	 *
	 * @since 1.0.0
	 * @param $workout_id
	 * @return string $title
	 */
	public static function get_workout_meta( $workout_id, $field_id ) {

		if( ! isset( $workout_id ) )
			return;

		if( $field_id === 'workout_day' )
			return;

		global $wpdb;

		$meta = $wpdb->get_results(
			"
			SELECT $field_id
			FROM $wpdb->bodybuilder_workout
			WHERE workout_id = $workout_id
			"
		);

		$metas = json_decode( json_encode( $meta ), true );

		if( ! isset( $metas[0] ) )
			return;

		$workout_meta = $metas[0][$field_id];

		return $workout_meta;

		return $workout_meta;

	}

	/**
	 * Update workout meta
	 *
	 * @since 1.0.0
	 * @param $workout_id
	 * @param $data
	 * @return void
	 */
	public static function update_workout_meta( $workout_id, $data ) {

		global $wpdb;
		$table_name = "wp_bodybuilder_workout";
		$where      = array( 'workout_id' => $workout_id );

		$wpdb->update( $table_name, $data, $where );

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

		$id = $wpdb->get_results(
			"
			SELECT workout_id
			FROM   $wpdb->bodybuilder_workout
			WHERE  workout_id = $workout_id
			"
		);

		if( empty( $id ) || $id === null ) {

			$wpdb->insert( $table_name, $args );

		} else {

			$wpdb->update( $table_name, $args, array( 'workout_id' => $workout_id ) );

		}

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

Custom_Tables::get_instance();