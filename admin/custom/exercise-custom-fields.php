<?php
/**
 * Exercise Custom Fields
 *
 * @package Bodybuilder\plugin\admin\custom
 * @since   1.0.0
 */

namespace Bodybuilder\plugin\admin\custom;

/**
 * Class Exercise_Custom_Fields
 */
class Exercise_Custom_Fields extends Custom_Field {

	/**
	 * Hold instance of class
	 *
	 * @var $instance
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

			add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ) );

		}

		add_action( 'wp_ajax_process_ajax', array( $this, 'process_ajax' ) );

	}

	/**
	 * Hook our save and add methods into WordPress
	 *
	 * @action add_meta_boxes
	 * @action save_post
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_exercise_meta_box'  ) );

		add_action( 'save_post', array( $this, 'save_exercise_meta' ) );

	}

	/**
	 * Add metabox
	 *
	 * Tell WordPress to create the meta box using our custom callback
	 *
	 * @since 1.0.0
	 * @action add_meta_boxes
	 * @return void
	 */
	public function add_exercise_meta_box() {

		add_meta_box(
			'exercise-custom-fields',                   // $id
			'Exercise Fields',                          // $title
			array( $this, 'render_exercise_meta_box' ), // $callback
			'exercise',                                 // $post
			'normal',                                   // $context
			'high'                                      // $priority
		);

	}

	/**
	 * Render exercise meta box
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_exercise_meta_box() {

		global $post;

		$post_id = $post->ID;

		$custom_meta_fields = self::set_exercise_meta_fields();

		// Use nonce for verification
		print( '<input type="hidden" name="exercise_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ).'" />' );

		// Begin the field table and loop
		print( '<table class="form-table">' );

		foreach ( $custom_meta_fields as $field ) {

			// get value of this field if it exists for this post
			$meta = get_post_meta( $post->ID, $field['id'], true );

			// begin a table row with
			printf( '<tr><th><label for="%s">%s</label></th><td>', esc_attr( $field['id'] ), esc_html( $field['label'] ) );

				switch( $field['type'] ) {

					case 'text':

						self::render_text_field( $field, $meta );

					break;

					case 'textarea':

						self::render_textarea_field( $field, $meta );

					break;

					case 'checkbox':

						self::render_checkbox_field( $field, $meta );

					break;

					case 'select':

						self::render_select_field( $field, $meta );

					break;

					case 'image':

						self::render_image_field( $field, $meta, $post_id );

					break;

					case 'repeatable':

						self::render_repeater_field( $field, $meta );

					break;

				} //end switch

			print( '</td></tr>' );

		} // end foreach

		print( '</table>' ); // end table

	}

	/**
	 * Save exercise meta
	 *
	 * Since the save_post action is triggered right after the post has been saved,
	 * you can easily access the post object $post_id
	 *
	 * @since 1.0.0
	 * @param int $post_id
	 * @return int $post_id
	 */
	public function save_exercise_meta( $post_id ) {

		$custom_meta_fields = self::set_exercise_meta_fields();

		// verify nonce
		if ( ! wp_verify_nonce( $_POST['exercise_meta_box_nonce'], basename( __FILE__ ) ) )
			return $post_id;

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// check permissions
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;

		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {

			return $post_id;

		}

		// loop through fields and save the data
		foreach ( $custom_meta_fields as $field ) {

			$old = get_post_meta( $post_id, $field['id'], true );

			$new = $_POST[$field['id']];

			if ( $new && $new != $old ) {

				update_post_meta( $post_id, $field['id'], $new );

			} elseif ( '' == $new && $old ) {

				delete_post_meta( $post_id, $field['id'], $old );

			}

			if ( $field['id'] == 'exercise_image' ) {

				$img_id = $_POST['custom-img-id'];

				update_post_meta( $post_id, $field['id'] , $img_id );

			}

		} // end foreach

	}

	/**
	 * Process ajax
	 *
	 * @since 1.0.0
	 * @add_action wp_ajax
	 * @return void
	 */
	public function process_ajax() {



	}

	/**
	 * Bodybuilder localize script
	 *
	 * @since 1.0.0
	 * @add_action admin_enqueue_scripts
	 * @return void
	 */
	public function localize_script() {

		global $post;

		if( isset( $post ) ) {

			$post_id = $post->ID;

			wp_localize_script( 'wpbb_admin_script', 'wpbbConfig', array(

				'postID' => $post_id,

			) );

		}

	}

	/**
	 * Return active instance of Bodybuilder_Custom_Fields, create one if it doesn't exist
	 *
	 * @return object Exercise_Custom_Fields
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}

}

Exercise_Custom_Fields::get_instance();
