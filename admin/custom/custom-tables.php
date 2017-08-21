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

	public function __construct() {

		$this->install_workout_table();

	}

	public function install_workout_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'bodybuilder_workout';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		workout_id mediumint(9) NOT NULL AUTO_INCREMENT,
		workout_name text NOT NULL,
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

}
