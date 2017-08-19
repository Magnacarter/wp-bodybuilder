<?php
/**
 * Bodybuilder Custom Fields
 *
 * @package Bodybuilder\plugin\admin\custom
 * @since   1.0.0
 */

namespace Bodybuilder\plugin\admin\custom;

/**
 * Class Bodybuilder_Custom_Fields
 */
class Bodybuilder_Custom_Fields {

	/**
	 * Hold meta box instance
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

			add_action( 'admin_enqueue_scripts', array( $this, 'bodybuilder_localize_script' ) );

		}

		add_action( 'wp_ajax_bodybuilder_process_ajax', array( $this, 'bodybuilder_process_ajax' ) );

	}

	/**
	 * Hook our save and add methods into WordPress
	 *
	 * @action add_meta_boxes
	 * @action save_post
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_metabox_bodybuilder'  ) );

	}

	/**
	 * Add metabox bodybuilder
	 *
	 * Tell WordPress to create the meta box using our custom callback
	 *
	 * @since 1.0.0
	 * @action add_meta_boxes
	 * @return void
	 */
	public function add_metabox_bodybuilder() {

		add_meta_box(

			'bodybuilder',

			__( 'Phase', 'text_domain' ),

			array( $this, 'render_metabox_for_blueprint' ),

			'bodybuilder',

			'advanced',

			'default'

		);

	}

	/**
	 * Render metabox for blueprint
	 *
	 * @since 1.0.0
	 * @param object $post
	 * @return void
	 */
	public function render_metabox_for_bodybuilder() {

		global $post;
		$post_id = $post->ID;

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'phase',
			'post_status'      => 'publish',
		);

		$phase_posts = get_posts( $args );

		$selected_phases = get_post_meta( $post_id, 'scb_blueprint_phases', true );

		if( ! empty( $selected_phases ) ) {

			$selected_phases = json_decode( $selected_phases );

			print( '<div><h3>Current Phases</h3></div><ul class="blueprint-phases">' );

			foreach( $selected_phases as $phase ) {

				$title = get_the_title( (int)$phase );

				print( '<li>' . $title . '</li>' );

			}

			print( '</ul>' );

		}

		echo '<div><h3>Update Phases</h3></div><select class="sc-phase-selection">';

		print( '<option>Add Phase</option>' );

		foreach ( $phase_posts as $phase_post ) :

			printf( '<option value="%s">%s</option>', esc_attr( $phase_post->ID ), esc_html( $phase_post->post_title ) );

		endforeach;

		echo '</select>';

		printf( '<input class="sc-phase-setting" name="post_id" value="%s" type="hidden"/>', $post->ID );

		print( '<div class="selected-phases">The following Phases will be added to your Blueprint:</div>' );

		print( '<div class="selected-phases-wrap">' );

		print( '<ul id="phase-list"></ul>' );

		print( '</div>' );

		print( '<div id="save-phases"><a href="#"><button>Save Phases</button></a></div>' );

	}

	/**
	 * Bodybuilder process ajax
	 *
	 * @since 1.0.0
	 * @add_action wp_ajax
	 * @return void
	 */
	public function bodybuilder_process_ajax() {

		$phase_id = intval( $_POST['phase_id'] );

		$args = array(
			'p'           => $phase_id,
			'post_type'   => 'phase',
			'post_status' => 'publish',
		);

		$phase_post = get_posts( $args );

		$phase_meta = array(
			'title' => $phase_post[0]->post_title,
			'phaseID' => $phase_post[0]->ID
		);

		wp_send_json_success( json_encode( $phase_meta, JSON_UNESCAPED_SLASHES ) );

	}

	/**
	 * Bodybuilder localize script
	 *
	 * @since 1.0.0
	 * @add_action admin_enqueue_scripts
	 * @return void
	 */
	public function bodybuilder_localize_script() {

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
	 * @return Phase_Custom_Fields
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}

}

Bodybuilder_Custom_Fields::get_instance();
