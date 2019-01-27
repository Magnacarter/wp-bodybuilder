<?php
/**
 * Custom Post Types
 *
 * @package Bodybuilder\plugin\admin\custom
 * @since   1.0.0
 */

namespace Bodybuilder\plugin\admin\custom;

/**
 * Register the custom post type.
 *
 * @since 1.0.0
 * @return void
 */
function register_exercise_custom_post_type() {

	$labels = array(
		'name'               => _x( 'Exercises', 'post type general name', 'exercise' ),
		'singular_name'      => _x( 'Exercise', 'post type singular name', 'exercise' ),
		'menu_name'          => _x( 'Exercises', 'admin menu', 'exercise' ),
		'name_admin_bar'     => _x( 'Exercise', 'add new on admin bar', 'exercise' ),
		'add_new'            => _x( 'Add New Exercise', 'team-bios', 'exercise' ),
		'add_new_item'       => __( 'Add New Exercise', 'exercise' ),
		'new_item'           => __( 'New Exercise', 'exercise' ),
		'edit_item'          => __( 'Edit Exercise', 'exercise' ),
		'view_item'          => __( 'View Exercise', 'exercise' ),
		'all_items'          => __( 'All Exercises', 'exercise' ),
		'search_items'       => __( 'Search Exercises', 'exercise' ),
		'parent_item_colon'  => __( 'Parent Exercises:', 'exercise' ),
		'not_found'          => __( 'No Exercises found.', 'exercise' ),
		'not_found_in_trash' => __( 'No Exercises found in Trash.', 'exercise' ),
	);

	$features = get_all_post_type_features( 'post', array(
		'excerpt',
		'comments',
		'trackbacks',
		'author',
		'revisions',
		'editor',
		'thumbnail',
		'custom-fields',
	) );

	$capabilities = array(
		'edit_post'          => 'update_core',
		'read_post'          => 'update_core',
		'delete_post'        => 'update_core',
		'edit_posts'         => 'update_core',
		'edit_others_posts'  => 'update_core',
		'delete_posts'       => 'update_core',
		'publish_posts'      => 'update_core',
		'read_private_posts' => 'update_core'
	);

	$args = array(
		'label'         => __( 'Exercises', 'exercise' ),
		'labels'        => $labels,
		'public'        => true,
		'supports'      => $features,
		'menu_icon'     => 'dashicons-admin-page',
		'hierarchical'  => false,
		'has_archive'   => false,
		'menu_position' => 10,
		'capabilities'  => $capabilities,
		'menu_icon'   => BODYBUILDER_URL . 'assets/img/exercise-icon.png',
	);

	register_post_type( 'exercise', $args );

}
add_action( 'init', __NAMESPACE__ . '\register_exercise_custom_post_type' );

/**
 * Get all the post type features for the given post type.
 *
 * @since 1.0.0
 *
 * @param string $post_type Given post type
 * @param array $exclude_features Array of features to exclude
 *
 * @return array
 */
function get_all_post_type_features( $post_type = 'post', $exclude_features = array() ) {

	$configured_features = get_all_post_type_supports( $post_type );

	if ( ! $exclude_features ) {

		return array_keys( $configured_features );

	}

	$features = array();

	foreach ( $configured_features as $feature => $value ) {

		if ( in_array( $feature, $exclude_features ) ) {

			continue;

		}

		$features[] = $feature;

	}

	return $features;

}
