<?php
/**
 * Workout
 *
 * display workout card in the bottom of a post
 *
 * @package Bodybuilder\plugin\pub
 * @since 1.0.0
 * @author Adam Carter
 * @licence GNU-2.0+
 */

namespace Bodybuilder\plugin\pub;

use Bodybuilder\plugin\admin\custom\Custom_Tables;

/**
 * Class Workout
 */
class Workout {

	/**
	 * Get global id
	 *
	 * @since 1.0.0
	 * @return int $post_id
	 */
	public function get_global_id() {

		global $post;

		$post_id = $post->ID;

		return $post_id;

	}

	/**
	 * Get workout meta
	 *
	 * @since 1.0.0
	 * @param $workout_id
	 * @return string $title
	 */
	public function get_workout_meta( $workout_id, $field_id ) {

		if( ! isset( $workout_id ) )
			return;

		global $wpdb;

		$meta = $wpdb->get_results(
			"
			SELECT $field_id
			FROM $wpdb->bodybuilder_workout
			WHERE workout_id = $workout_id
			"
		);

		$workout_meta = $meta[0]->$field_id;

		return $workout_meta;

	}

	/**
	 * Get all workout meta
	 *
	 * @since 1.0.0
	 * @param $post_id
	 * @return array $meta
	 */
	public function get_all_workout_meta( $post_id ) {

		global $wpdb;

		$meta = $wpdb->get_results(
			"
			SELECT * 
			FROM $wpdb->bodybuilder_workout
			"
		);

		return $meta;
	}

	/**
	 * Get workout
	 *
	 * @since 1.0.0
	 * @return array|null|object $workout
	 */
	public function get_workout( $post_id ) {

		global $wpdb;

		$workout = $wpdb->get_results(
			"
			SELECT workout
			FROM $wpdb->bodybuilder_workout
			WHERE workout_id = $post_id
			"
		);

		return $workout;

	}

	/**
	 * Load exercises
	 *
	 * @since 1.0.0
	 * @param array $exercises
	 * @return void
	 */
	public function load_exercises( $exercises ) {

		if( ! isset( $exercises ) )
			return;

		foreach( $exercises as $day ) {

			$work_days = json_decode( $day->workout, true );

			foreach ( $work_days as $work_day ) {

				print( '<div class="grid-100">' );

				printf( '<h3>%s</h3>', $work_day['day'] );

				print( '</div>' );

				$exercises = $work_day['exercises'];

				if( ! isset( $exercises ) )
					return;

				foreach ( $exercises as $exercise ) {

					print( '<div class="grid-50">' );

					$exercise_id = $exercise[0]['id'];

					$title = get_the_title( $exercise_id );

					printf( '<h4>%s</h4>', $title );

					$sets = $exercise[1]['sets'];
					$reps = $exercise[1]['reps'];
					$rest = $exercise[1]['rest'];

					printf( '<h5>Sets : %s</h5>', $sets );
					printf( '<h5>Reps : %s</h5>', $reps );
					printf( '<h5>Rest per set : %s</h5>', $rest );

					print( '</div>' );

				}

			}

		}

	}

	/**
	 * Build schema array
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @return array $workout_schema
	 */
	public function build_schema_array( $post_id ) {

		$meta  = $this->get_all_workout_meta( $post_id );
		$image = $meta[0]->workout_image;
		$workout_image = wp_get_attachment_image_src( $image );

		$workout_schema = array(
			'@context'          => 'http://schema.org',
			'@type'             => 'ExercisePlan',
			'@id'               => get_permalink( $post_id ),
			'name'              => $meta[0]->workout_name,
			'description'       => $meta[0]->workout_description,
			'url'               => get_permalink( $post_id ),
			'author'            => $meta[0]->workout_author,
			'image'             => $workout_image,
			'mainEntityOfPage'  => get_permalink( $post_id ),
			'activityDuration'  => $meta[0]->workout_duration,
			'activityFrequency' => $meta[0]->workout_frequency,
			'intensity'         => $meta[0]->workout_intensity,
			'repetitions'       => $meta[0]->workout_repetitions,
			'exerciseType'      => $meta[0]->workout_category,
			'restPeriods'       => $meta[0]->workout_rest_periods,
			'workload'          => $meta[0]->workout_workload,
		);

		$encoded_workout = json_encode( $workout_schema, JSON_UNESCAPED_SLASHES );
		$workout_schema  = sprintf( '<script type=“application/ld+json”>%s</script>', $encoded_workout );

		return $workout_schema;

	}

}
