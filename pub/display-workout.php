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
			'workout_author'
		);

		foreach ( $args as $arg ) {

			$fields[$arg] .= $this->get_workout_meta( $post_id, $arg );

		}

		return $fields;

	}

	/**
	 * Add borttom rating
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @return void
	 */
	public function add_bottom_rating( $post_id ) {

		$average_rating = $this->get_workout_meta( $post_id, 'rating_average' );

		?>
		<script type="text/javascript">
			jQuery( document ).ready( function($) {
				const rateBottom = $( '#rating-bottom' );

				rateBottom.rateYo({
					rating    : <?php echo $average_rating ?>,
					starWidth : "35px",
					normalFill: "#dae4e7",
					ratedFill : "#3c4a50",
					fullStar  : true
				});

				rateBottom.rateYo().on( 'rateyo.set', function (e, data) {

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
							 div_id: 'rating-bottom'
						 },
						 success: function (response) {
							 console.log(response.data);
							 if (response.success === true) {
								 console.log(response.data);
								 alert('Thank you for rating this workout!');
							 }
							 if (response.success === false) {
								 console.log(response);
								 alert(response.data);
							 }
						 }
					 }); // ajax
				}); // on rate event
			}); // document ready
		</script>
		<?php
	}

	/**
	 * Add rating js
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @return void
	 */
	public function add_rating_js( $post_id ) {

		$name           = $this->get_workout_meta( $post_id, 'workout_name' );
		$div_id         = strtolower( str_replace( ' ', '-', $name ) ) . '-' . $post_id;
		$average_rating = $this->get_workout_meta( $post_id, 'rating_average' );

		?>

		<div class="no-padding">
			<div id="<?php echo esc_attr( $div_id ); ?>" class="grid-60"></div>
			<span class="desktop text-center"><p><?php echo esc_html( $average_rating ); ?> out of 5 star rating</p></span>
			<div class="clearfix"></div>
			<div class="mobile">
				<p><?php echo esc_html( $average_rating ); ?> out of 5 star rating</p>
			</div>
		</div>

		<script type="text/javascript">
			jQuery( document ).ready( function($) {

				$( '#<?php echo $div_id ?>' ).rateYo({
					rating    : <?php echo $average_rating ?>,
					starWidth : "22px",
					normalFill: "#dae4e7",
					ratedFill : "#3c4a50",
					fullStar  : true
				});

				$( '#<?php echo $div_id ?>' ).rateYo().on( 'rateyo.set', function (e, data) {

					$( '#rating_value').attr( 'value', data.rating );

					const userRating = data.rating;

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
								$( '.rating-meta' ).html( '<p>' + response.data[2] + ' out of 5 star rating</p>' );
								alert( 'Thank you for rating this workout!' );
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

		if ( is_single() ) :

			$post_id     = $this->get_global_id();
			$fields      = $this->add_workout_meta( $post_id );
			$img_id      = intval( $fields['workout_image'] );
			$img_att     = wp_get_attachment_image_src( $img_id, 'full' );
			$workouts    = $this->get_workout( $post_id );
			$avg_time    = Custom_Tables::get_workout_meta( $post_id, 'workout_duration' );
			$avg_energy  = Custom_Tables::get_workout_meta( $post_id, 'workout_workload' );
			$description = Custom_Tables::get_workout_meta( $post_id, 'workout_description' );

			// Put instructions into an array at each new line
			$instructions = preg_split( '/(\r\n|\n|\r)/', $fields['workout_instructions'] );

			// Filter the array for whitespace or empty stings from the textarea in the post admin
			$instructions = array_filter( $instructions, array( $this, 'check_whitespace_or_empty' ) );

			if ( empty( $workouts ) )
				return $content;

			ob_start();

			$workout_schema = $this->build_schema_array( $post_id );

			print( $workout_schema );

			?>

			<div id="wpbb-workout-card" class="wpbb-workout no-padding grid-container">

				<div id="rating-wrap">

					<section id="rating" class="no-padding grid-45 tablet-grid-50">

						<?php $this->add_rating_js( $post_id ) ?>

						<header class="no-pad-left">

							<h2><?php echo esc_html( $fields['workout_name'] ) ?></h2>

							<p>Author: <span><?php echo esc_html( $fields['workout_author'] ) ?></span></p>

							<p>Category: <span><?php echo esc_html( $fields['workout_category'] ) ?></span></p>

						</header>

						<div id="averages" class="desktop">

							<p>Workout Time : <span><?php echo esc_html( $avg_time ); ?></span></p>

							<p>Energy Used : <span><?php echo esc_html( $avg_energy ); ?></span></p>

						</div> <!-- #averages -->

					</section>

					<div class="workout-image-wrapper no-pad-right grid-55 tablet-grid-50">

						<img id="workout-img" src="<?php echo esc_attr( $img_att[0] ) ?>" />

						<div class="no-padding save-button">

							<a class="save-btn blue-btn" href="javascript:genPDF()">Save Workout</a>

						</div>

					</div><!-- .workout-image-wrapper -->

				</div> <!-- #rating-wrap -->

				<div class="clearfix"></div>

				<div id="mobile-rating-wrap" class="grid-container pl-30">

					<section id="rating-mobile" class="no-padding mobile-grid-100 mobile">

						<div id="averages">

							<p>Workout Time : <span><?php echo esc_html( $avg_time ); ?></span></p>

							<p>Energy Used : <span><?php echo esc_html( $avg_energy ); ?></span></p>

						</div> <!-- #averages -->

					</section>

				</div>

				<section id="wpbb-workout-inner" class="wpbb-content-inner">

					<div class="workout-content">

						<div class="description no-pad-left grid-100">

							<h3>Description :</h3>

							<p><?php echo stripslashes( $description ); ?></p>

						</div><!-- .description -->

						<div class="workout-instructions no-pad-left grid-100">

							<h3>Workout Instructions :</h3>

							<ol class="grid-100">

							<?php foreach ( $instructions as $instruction ) : ?>

								<li><?php echo esc_html( $instruction ); ?></li>

							<?php endforeach; ?>

							</ol>

						</div> <!-- .workout-instructions -->

					</div> <!-- .workout-content -->

				</section> <!-- .wpbb-content-inner -->

				<div class="gradient"></div>

				<section class="exercises">

					<?php $this->load_exercises( $workouts ) ?>

				</section> <!-- .exercises -->

				<section id="rate-it" class="grid-container">

					<div class="no-pad-left grid-50">
						<h3>Did you do it?</h3>
						<p>Rate this workout!</p>
					</div>

					<div id="rating-bottom" class="no-pad-left grid-50">
						<?php $this->add_bottom_rating( $post_id ); ?>
					</div>

				</section>

			</div><!-- .wpbb-workout -->

			<?php

			$workout = ob_get_clean();

			$content .= $workout;

		endif;

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

