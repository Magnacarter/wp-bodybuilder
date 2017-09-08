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
	 *
	 * @since 1.0.0
	 * @return array $recipe_schema
	 */
//	public function build_schema_array( $post_id ) {
//
//		global $post;
//
//		$recipe_vals    = $this->get_card_fields();
//		$total          = $recipe_vals['mrc_rating_total'];
//		$count          = $recipe_vals['mrc_review_count'];
//		$rating_average = Import::get_rating_average( $total, $count );
//
//		$recipe_schema = array(
//			'@context'          => 'http://schema.org',
//			'@type'             => 'Recipe',
//			'@id'               => get_permalink( $post->ID ),
//			'name'              => $recipe_vals['mrc_recipe_name'],
//			'description'       => $recipe_vals['mrc_summary'],
//			'aggregateRating'   => array(
//				'@type'       => 'AggregateRating',
//				'ratingValue' => $rating_average,
//				'reviewCount' => $count,
//				'bestRating'  => $recipe_vals['mrc_best_rating'],
//				'worstRating' => $recipe_vals['mrc_worst_rating'],
//			),
//			'url'               => get_permalink( $post->ID ),
//			'author'            => $recipe_vals['mrc_author'],
//			'image'             => $recipe_vals['mrc_photo'],
//			'mainEntityOfPage'  => get_permalink( $post->ID ),
//			'prepTime'          => $recipe_vals['mrc_prep_time'],
//			'cookTime'          => $recipe_vals['mrc_cook_time'],
//			'totalTime'         => $recipe_vals['mrc_total_time'],
//			'recipeYield'       => $recipe_vals['mrc_yield'],
//			'recipeCategory'    => $recipe_vals['mrc_type'],
//			'recipeCuisine'     => $recipe_vals['mrc_cuisine'],
//			'nutrition'         => array(
//				'@type'               => 'NutritionInformation',
//				'servingSize'         => $recipe_vals['mrc_serving_size'],
//				'calories'            => $recipe_vals['mrc_calories'],
//				'fatContent'          => $recipe_vals['mrc_fat'],
//				'carbohydrateContent' => $recipe_vals['mrc_carbohydrates'],
//				'cholesterolContent'  => $recipe_vals['mrc_cholesterol'],
//				'fiberContent'        => $recipe_vals['mrc_fiber'],
//				'proteinContent'      => $recipe_vals['mrc_protein'],
//				'saturatedFatContent' => $recipe_vals['mrc_saturated_fat'],
//				'sodiumContent'       => $recipe_vals['mrc_sodium'],
//				'sugarContent'        => $recipe_vals['mrc_sugar'],
//			),
//			'recipeIngredient'   => array( json_decode( $recipe_vals['mrc_ingredients'] ) ),
//			'recipeInstructions' => array( json_decode( $recipe_vals['mrc_instructions'] ) ),
//		);
//
//		$workout_schema  = $this->array_filter_recursive( $workout_schema );
//		$encoded_workout = json_encode( $workout_schema, JSON_UNESCAPED_SLASHES );
//		$workout_schema  = sprintf( '<script type=“application/ld+json”>%s</script>', $encoded_workout );
//
//		return $workout_schema;
//
//	}

}
