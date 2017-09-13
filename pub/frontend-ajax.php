<?php
/**
 * Frontend ajax
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
 * Update user rating
 *
 * Rating without comment, ajax callback
 *
 * @since 1.0.0
 * @add_action wp_ajax
 * @add_action wp_ajax_nopriv
 * @return void
 */
function update_user_rating() {

	if (
		'POST' !== $_SERVER['REQUEST_METHOD']
		||
		! isset( $_POST['user_rating'] )
		||
		! isset( $_POST['post_id'] )
		||
		! isset( $_POST['div_id'] )
	) {

		wp_send_json_error( 'Doing it wrong.' );

	}

	$user_rating = (int)filter_input( INPUT_POST, 'user_rating' );
	$post_id     = (int)filter_input( INPUT_POST, 'post_id' );
	$div_id      = sanitize_text_field( filter_input( INPUT_POST, 'div_id' ) );
	$total       = Custom_Tables::get_workout_meta( $post_id, 'rating_total' );
	$count       = Custom_Tables::get_workout_meta( $post_id, 'rating_count' );
	$new_total   = (int)$total + (int)$user_rating;
	$new_count   = (int)$count + 1;
	$average     = Rating::get_average_rating( $new_total, $new_count );

	if ( isset( $_COOKIE['rating_cookie'] ) ) {

		$rating_array = stripcslashes( $_COOKIE['rating_cookie'] );
		$rating_array = json_decode( $rating_array );

		if( $rating_array->workout_id === $post_id ) {

			wp_send_json_error( 'Looks like you\'ve already submitted a rating.' );

		}

	}

	$rating_meta = array(
		'rating_count'   => $new_count,
		'rating_total'   => $new_total,
		'rating_average' => $average
	);

	Custom_Tables::update_workout_meta( $post_id, $rating_meta );

	Rating::set_rating_cookie( $user_rating, $post_id );

	$data = array(
		$user_rating,
		$post_id,
		$average,
	);

	wp_send_json_success( $data );

}
add_action( 'wp_ajax_update_user_rating',  __NAMESPACE__ . '\update_user_rating' );
add_action( 'wp_ajax_nopriv_update_user_rating',  __NAMESPACE__ . '\update_user_rating' );

/**
 * Enqueue scripts
 *
 * Load jQuery and make admin-ajax.php URL accessible
 * on the front-end.
 *
 * @since 1.0.0
 * @action wp_enqueue_scripts
 * @return void
 */
function wpbb_enqueue_scripts() {

	//wp_enqueue_script( 'jquery' );

	wp_localize_script( 'jquery', 'wpbb_rating_vars',
		array(
			'ajaxurl'      => admin_url( 'admin-ajax.php' ),
			'php_callback' => __NAMESPACE__ . '\update_user_rating',
		)
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\wpbb_enqueue_scripts' );
