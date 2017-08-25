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
class Post_Custom_Fields {

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
	public function render_metabox_workout_fields( $post ) {

		print( '<header><h3>Feel the burn</h3></header>' );

	}

	/**
	 * Render metabox for post
	 *
	 * Render the meta box in the post post editor screen
	 *
	 * @since 1.0.0
	 * @param object $post
	 * @return void
	 */
	public function render_metabox_for_post( $post ) {

		$this->render_metabox_workout_fields( $post );

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
	public function save_recipe_from_post() {

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
