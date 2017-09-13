<?php
/**
 * Rating system
 *
 * Rate workouts from the workout card inside a post
 *
 * @package Bodybuilder\plugin\pub
 * @since   1.0.0
 */
namespace Bodybuilder\plugin\pub;
use Bodybuilder\plugin\admin\custom\Custom_Tables;

/**
 * Rating class
 */
class Rating {

	/**
	 * Hold instance of the Rating class
	 * @var $instance
	 */
	public static $instance;

	/**
	 * Set rating cookie
	 *
	 * @since 1.0.0
	 * @param int $rating_value
	 * @param int $post_id
	 * @return void
	 */
	public static function set_rating_cookie( $rating_value, $post_id ) {

		$cookie_name  = "rating_cookie";

		$cookie_value = array(
			'rating'     => $rating_value,
			'workout_id' => $post_id,
			'rating_id'  => rand( 100, 9999 )
		);

		$cookie_array = json_encode( $cookie_value, JSON_UNESCAPED_SLASHES );

		setcookie( $cookie_name, $cookie_array, time() + ( 86400 * 30 ), get_permalink( $post_id ) );

	}

	/**
	 * Get average
	 *
	 * @since 1.0.0
	 * @param int $total
	 * @param int $count
	 * @return float/int $rating_average
	 */
	public static function get_average_rating( $total, $count ) {

		if( $total != 0 && $count != 0 ) {

			$rating_average = $total / $count;

			$rating_average = round( $rating_average, 1, PHP_ROUND_HALF_UP );

			return $rating_average;

		}

		$rating_average = 5;

		return $rating_average;

	}

	/**
	 * Get instance
	 *
	 * Return active instance of Rating, create one if it doesn't exist
	 * @since 1.0.0
	 * @return object Rating
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}
}

Rating::get_instance();
