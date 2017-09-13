<?php
/**
 * Display workout
 *
 * @package Bodybuilder\plugin\pub
 * @since 1.0.0
 * @author Adam Carter
 * @licence GNU-2.0+
 */

namespace Bodybuilder\plugin\pub;
use Bodybuilder\plugin\admin\custom\Custom_Tables;
use Bodybuilder\plugin\pub\Rating;

/**
 * Class Workout
 */
class Display_Workout extends Workout {

	/**
	 * Hold instance of the Workout class
	 * @var $instance
	 */
	public static $instance;

	/**
	 * Workout constructor
	 */
	public function __construct() {

		add_filter( 'the_content', array( $this, 'add_workout_to_post' ) );

	}

	/**
	 * Add workout meta
	 *
	 * @since 1.0.0
	 * @param $post_id
	 * @return array
	 */
	public function add_workout_meta( $post_id ) {

		$fields = [];

		$args = array(
			'workout_name',
			'workout_category',
			'workout_image',
			'workout_instructions',
		);

		foreach ( $args as $arg ) {

			$fields[$arg] .= $this->get_workout_meta( $post_id, $arg );

		}

		return $fields;

	}

	/**
	 * Add rating js
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @return void
	 */
	public function add_rating_js( $post_id ) {

		$name   = $this->get_workout_meta( $post_id, 'workout_name' );
		$div_id = strtolower( str_replace( ' ', '-', $name ) ) . '-' . $post_id;
		$average_rating = $this->get_workout_meta( $post_id, 'rating_average' );

		printf( '<div id="%s"></div>', $div_id );
		printf( '<div class="rating-meta">%s out of 5 stars</div>', $average_rating );

		?>
		<script type="text/javascript">

			jQuery( document ).ready( function($) {

				$( '#<?php echo $div_id ?>' ).rateYo({
					rating    : <?php echo $average_rating ?>,
					starWidth : "25px",
					normalFill: "#ddd",
					ratedFill : "#000",
					fullStar  : true
				});

				$( '#<?php echo $div_id ?>' ).rateYo().on( 'rateyo.set', function (e, data) {

					$( '#rating_value').attr( 'value', data.rating );

					var userRating = data.rating;

					console.log( userRating );

					$.ajax({
						type: 'POST',
						url: wpbb_rating_vars.ajaxurl,
						dataType: 'json',
						data: {
							action: 'update_user_rating',
							user_rating: userRating,
							post_id: <?php echo $post_id ?>,
							div_id: '#<?php echo $div_id ?>'
						},
						success: function( response ) {

							console.log( response.data );

							if( response.success === true ) {

								console.log( response.data );

								$( '.rating-meta' ).html( '<p>' + response.data[2] + ' out of 5 stars</p>' );

								alert( 'Thank you for rating this recipe!' );

							 }

							if( response.success === false ) {

								console.log( response );

								alert( response.data );

							}

						}

					}); // rate, no comment event

				}); // on rate event

			}); // on rate event

		</script>
		<?php

	}

	/**
	 * Add workout to post
	 *
	 * Filter the post content. Add the workout to the bottom of the post.
	 *
	 * @since 1.0.0
	 * @return object $content
	 */
	public function add_workout_to_post( $content ) {

		$post_id  = $this->get_global_id();
		$fields   = $this->add_workout_meta( $post_id );
		$img_id   = intval( $fields['workout_image'] );
		$img_att  = wp_get_attachment_image_src( $img_id, 'full' );
		$workouts = $this->get_workout( $post_id );

		if ( empty( $workouts ) )
			return;

		ob_start();

		$workout_schema = $this->build_schema_array( $post_id );

		print( $workout_schema );

		?>

		<div class="wpbb-workout grid-container">

			<div class="wpbb-content-inner">

				<header class="grid-50">

					<h2><?php echo esc_html( $fields['workout_name'] ) ?></h2>

					<p>Category: <?php echo esc_html( $fields['workout_category'] ) ?></p>

				</header>

				<div class="workout-image-wrapper grid-50">

					<img src="<?php echo esc_attr( $img_att[0] ) ?>" />

					<?php echo $this->add_rating_js( $post_id ) ?>

				</div><!-- .workout-image-wrapper -->

				<section class="workout-content">

					<div class="workout-instructions grid-100">

						<?php echo esc_html( stripslashes( $fields['workout_instructions'] ) ) ?>

					</div><!-- .workout-instructions -->

					<div class="exercises">

						<div class="grid-100"><h2>Exercises :</h2></div>

						<?php $this->load_exercises( $workouts ) ?>

					</div><!-- .exercises -->

				</section><!-- .workout-content -->

			</div><!-- .wpbb-content-inner -->

		</div><!-- .wpbb-workout -->

		<?php

		$workout = ob_get_clean();

		$content .= $workout;

		return $content;

	}

	/**
	 * Get instance
	 *
	 * Return active instance of Workout, create one if it doesn't exist
	 * @since 1.0.0
	 * @return object Workout
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}
}

Display_Workout::get_instance();

