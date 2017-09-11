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

		//$workout_schema = $this->build_schema_array( $post_id );

		//print( $workout_schema );

		?>

		<div class="wpbb-workout grid-container">

			<div class="wpbb-content-inner">

				<header class="grid-50">

					<h2><?php echo esc_html( $fields['workout_name'] ) ?></h2>

					<p>Category: <?php echo esc_html( $fields['workout_category'] ) ?></p>

				</header>

				<div class="workout-image-wrapper grid-50">

					<img src="<?php echo esc_attr( $img_att[0] ) ?>" />

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

			<?php echo $this->build_schema_array( $post_id ) ?>

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

