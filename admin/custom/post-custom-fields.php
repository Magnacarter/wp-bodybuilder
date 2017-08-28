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
			'workout',
			__( 'Add a New Workout', 'text_domain' ),
			array( $this, 'render_metabox_for_post' ),
			'post',
			'advanced',
			'high'
		);

	}

	/**
	 * Render metabox card fields
	 */
	public function render_metabox_workout_fields() {

		$post_id = $this->get_global_id();

		$custom_meta_fields = $this->get_workout_meta_fields();

		// Use nonce for verification
		print( '<input type="hidden" name="workout_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ).'" />' );

		// Begin the field table and loop
		print( '<table class="form-table">' );

		foreach ( $custom_meta_fields as $field ) {

			// get value of this field if it exists for this post
			$meta = get_post_meta( $post_id, $field['id'], true );

			// begin a table row with
			printf( '<tr><th><label for="%s">%s</label></th><td>', esc_attr( $field['id'] ), esc_html( $field['label'] ) );

			switch( $field['type'] ) {

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

			} //end switch

			print( '</td></tr>' );

		} // end foreach

		print( '</table>' ); // end table

		print( '<a class="save-btn">Save Workout</a>' );

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
		) {

			$workout_id = intval( $_POST['workout_id'] );

		}

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['mrc_nonce'];
		$nonce_action = 'mrc_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $nonce_name ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $workout_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $workout_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $workout_id ) )
			return;

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
