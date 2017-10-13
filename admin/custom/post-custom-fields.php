<?php
/**
 * Post custom fields
 *
 * Add custom fields to the post editor screen to add a workout
 *
 * @package Bodybuilder\plugin\admin\custom
 * @since 1.0.0
 * @author Adam Carter
 * @licence GNU-2.0+
 */

namespace Bodybuilder\plugin\admin\custom;

use Bodybuilder\plugin\admin\custom\Custom_Tables;

/**
 * Class Post_Custom_Fields
 */
class Post_Custom_Fields extends Custom_Field {

	/**
	 * Hold Post_Custom_Fields instance
	 *
	 * @var string
	 */
	public static $instance;

	/**
	 * Class Constructor
	 *
	 * Hook our metabox into the post admin area
	 *
	 * @action load-post.php
	 * @action load-post-new.php
	 */
	public function __construct() {

		if ( is_admin() ) {

			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );

			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );

		}

		add_action( 'wp_ajax_workout_process_ajax', array( $this, 'workout_process_ajax' ) );

		add_action( 'wp_ajax_add_day_ajax', array( $this, 'add_day_ajax' ) );

		add_action( 'wp_ajax_save_workout_from_post', array( $this, 'save_workout_from_post' ) );

	}

	/**
	 * Hook our save and add methods into WordPress
	 *
	 * @action add_meta_boxes
	 * @action save_post
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_metabox_to_post'  ) );

		add_action( 'wp_insert_post', array( $this, 'save_workout_from_post' ), 10, 2 );

	}

	/**
	 * Add metabox to post
	 *
	 * Tell WordPress to create the meta box using our custom callback
	 *
	 * @since 1.0.0
	 * @action add_meta_boxes
	 * @return void
	 */
	public function add_metabox_to_post() {

		global $post;

		add_meta_box(
			'exercise-custom-fields',
			__( 'Add a New Workout', 'text_domain' ),
			array( $this, 'render_metabox_for_post' ),
			'post',
			'advanced',
			'high'
		);

	}

	/**
	 * Get workout
	 *
	 * @since 1.0.0
	 * @return array|null|object $workout
	 */
	public static function get_workout() {

		global $post, $wpdb;

		$post_id = $post->ID;

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
	 * Set new workout fields
	 *
	 * @since
	 * @param int $post_id
	 * @return void
	 */
	public function set_new_workout_fields( $post_id ) {

		$custom_meta_fields = $this->get_workout_meta_fields();

		foreach ( $custom_meta_fields as $field ) {

			// get value of this field if it exists for this post
			$meta = Custom_Tables::get_workout_meta( $post_id, $field['id'] );

			// begin a table row with
			printf( '<tr><th><label for="%s">%s</label></th><td>', esc_attr( $field['id'] ), esc_html( $field['label'] ) );

			switch ( $field['type'] ) {

				case 'text':
					$this->render_text_field( $field, $meta );
					break;

				case 'textarea':
					$this->render_textarea_field( $field, $meta );
					break;

				case 'checkbox':
					$this->render_checkbox_field( $field, $meta );
					break;

				case 'select':
					$this->render_select_field( $field, $meta );
					break;

				case 'image':
					$this->render_image_field( $field, $meta, $post_id );
					break;

				case 'day-repeater':
					$this->render_day_repeater_field( $field, $meta );
					break;

				case 'wysiwyg':
					$this->render_wysiwyg_field( $field, $meta );
					break;

			} // end switch

			print( '</td></tr>' );

		} // end foreach

	}

	/**
	 * Render metabox workout fields
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_metabox_workout_fields() {

		$post_id = $this->get_global_id();
		$workout = Post_Custom_Fields::get_workout();

		?>

		<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
		<input type="hidden" name="workout_meta_box_nonce" value="<?php echo wp_create_nonce( 'workout-nonce' ); ?>" />
		<table class="form-table">

			<?php $this->set_new_workout_fields( $post_id ); ?>

		</table>

		<a class="save-btn">Save Workout</a>

		<div class="progress-background">
			<div class="progress-bar-wrap">
				<div id="progress-bar" class="center" style="width:20%">20%</div>
			</div>
		</div>

		<?php

	}

	/**
	 * Render metabox for post
	 *
	 * Render the meta box in the post post editor screen
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_metabox_for_post() {

		$this->render_metabox_workout_fields();

	}

	/**
	 * Add day ajax
	 */
	public function add_day_ajax() {

		if( $_POST['addDay'] == true ) {

			$args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'exercise',
				'post_status'      => 'publish',
			);

			$exercisePosts = get_posts( $args );

			wp_send_json_success( $exercisePosts );

		}

	}

	/**
	 * Process ajax
	 *
	 * @since 1.0.0
	 * @add_action wp_ajax
	 * @return void
	 */
	public function workout_process_ajax() {

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

			$exercise_id = $_POST['exerciseId'];

			$exercise_post = get_post( $exercise_id );

			wp_send_json_success( $exercise_post );

		}

	}

	/**
	 * Save workout from post
	 *
	 * Do secutrity checks, sanitize and escape user inputs and
	 * save the meta data from the meta box into WordPress database
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function save_workout_from_post() {

		if (
			'POST' === $_SERVER['REQUEST_METHOD']
			&&
			isset( $_POST['nonce'] )
			&&
			isset( $_POST['workout'] )
			&&
			isset( $_POST['workoutId'] )
		) {

			$i                    = 0;
			$workout_id           = intval( $_POST['workoutId'] );
			$workout_object       = $_POST['workout'];
			$workout_json         = json_encode( $workout_object, JSON_UNESCAPED_SLASHES );
			$nonce_field          = $_POST['nonce'];
			$nonce_action         = 'workout-nonce';
			$workout_instructions = $_POST['workoutInstructions'];
			$workout_image        = $_POST['workoutImage'];
			$workout_name         = $_POST['workoutName'];
			$workout_category     = $_POST['workoutCategory'];
			$workout_duration     = $_POST['workoutDuration'];
			$workout_repetitions  = $_POST['workoutRepetitions'];
			$workout_author       = $_POST['workoutAuthor'];
			$workout_rest         = $_POST['workoutRest'];
			$workout_workload     = $_POST['workoutWorkload'];
			$workout_intensity    = $_POST['workoutIntensity'];
			$workout_desc_visual  = $_POST['workoutDescVisual'];

			// Check if a nonce is valid.
			if ( ! wp_verify_nonce( $nonce_field, $nonce_action ) )
				return;

			// Check if the user has permissions to save data.
			if ( ! current_user_can( 'edit_posts', $workout_object ) )
				return;

			// Check if it's not an autosave.
			if ( wp_is_post_autosave( $workout_object ) )
				return;

			// Check if it's not a revision.
			if ( wp_is_post_revision( $workout_object ) )
				return;

			$workout_days = json_decode( $workout_json, true );
			foreach ( $workout_days as $work_day ) {
				$i++;
			}

			$args = array(
				'workout'              => $workout_json,
				'workout_id'           => $workout_id,
				'workout_name'         => $workout_name,
				'workout_image'        => $workout_image,
				'workout_instructions' => $workout_instructions,
				'workout_category'     => $workout_category,
				'workout_frequency'    => $i . ' days',
				'workout_duration'     => $workout_duration,
				'workout_intensity'    => $workout_intensity,
				'workout_repetitions'  => $workout_repetitions,
				'workout_rest_periods' => $workout_rest,
				'workout_workload'     => $workout_workload,
				'workout_author'       => $workout_author,
				'workout_description'  => $workout_desc_visual
			);

			Custom_Tables::save_workout( $args, $workout_id );

			wp_send_json_success( $workout_instructions );

		}

	}

	/**
	 * Return active instance of Post_Custom_Fields, create one if it doesn't exist
	 *
	 * @return Post_Custom_Fields
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}

}

Post_Custom_Fields::get_instance();
