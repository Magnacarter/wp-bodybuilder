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
			WHERE workout_id = $post_id
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
	 * Check whitespace or empty
	 *
	 * Callback for array_filter to filter an array's values
	 * for whitespace or empty strings and remove it
	 *
	 * @since 1.0.0
	 * @param string $val
	 * @return bool
	 */
	public function check_whitespace_or_empty( $val ) {

		return $val != '' || preg_match( '/\S/', $val );

	}

	/**
	 * Render instructions popup
	 */
	public function render_instructions_popup( $post_id ) {

		$instructions_arr = get_post_meta( $post_id, 'exercise_instructions' );

		// Put instructions into an array at each new line
		$instructions = preg_split( '/(\r\n|\n|\r)/', $instructions_arr[0] );

		// Filter the array for whitespace or empty stings from the textarea in the post admin
		$instructions = array_filter( $instructions, array( $this, 'check_whitespace_or_empty' ) );

		?>

		<div class="instruction-popup">

			<div class="close-button"><a class="close-btn">x</a></div>

			<ul>

				<?php foreach( $instructions as $instruction ) : ?>

					<li><?php echo $instruction ?></li>

				<?php endforeach ?>

			</ul>

		</div>

		<?php

	}

	/**
	 * Load exercises
	 *
	 * @since 1.0.0
	 * @param array $exercises
	 * @return void
	 */
	public function load_exercises( $exercises ) {

		if( ! isset( $exercises ) || $exercises === null || $exercises === 0 )
			return;

		foreach( $exercises as $day ) {

			$work_days = json_decode( $day->workout, true );

			foreach ( $work_days as $work_day ) {

				print( '<div class="day-title no-pad-left grid-100">' );

				printf( '<h3>%s</h3>', $work_day['day'] );

				print( '</div>' );

				$exercises = $work_day['exercises'];

				if( ! isset( $exercises ) )
					return;

				foreach ( $exercises as $exercise ) {

					print( '<div id="single-exercise" class="exercise no-pad-left grid-100">' );

					$exercise_id = $exercise[0]['id'];
					$image_id    = get_post_meta( $exercise_id, 'exercise_image' );
					$image_id    = (int)$image_id[0];
					$image       = get_post_meta( $image_id, '_wp_attached_file' );
					$title       = get_the_title( $exercise_id );
					$image_path  = wp_upload_dir();
					$image_path  = $image_path['baseurl'] . '/' . $image[0];
					$sets        = $exercise[1]['sets'];
					$reps        = $exercise[1]['reps'];
					$rest        = $exercise[1]['rest'];

					$this->render_instructions_popup( $exercise_id );

					printf( '<h4><span>%s</span></h4>', esc_html( $title ) );

					printf( '<div class="no-pad-left grid-30"><img src="%s"/></div>', esc_attr( $image_path ) );

					print( '<div class="sets grid-45">' );
					printf( '<p><span>Sets : </span> %s</p>', $sets );
					printf( '<p><span>Reps : </span> %s</p>', $reps );
					printf( '<p><span>Rest per set : </span> %s</p>', $rest );
					print( '</div>' );

					print( '<div class="instruction-button grid-25"><a class="instruction-btn">Instructions</a></div>' );

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
