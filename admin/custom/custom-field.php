<?php
/**
 * Custom Field base class
 *
 * @package Bodybuilder\plugin\admin\custom
 * @since 1.0.0
 * @author Adam Carter
 * @licence GNU-2.0+
 */

namespace Bodybuilder\plugin\admin\custom;

/**
 * Class Custom_Field
 */
class Custom_Field {

	/**
	 * @var $exercise_prefix
	 */
	public $exercise_prefix = 'exercise_';

	/**
	 * @var $workout_prefix
	 */
	public $workout_prefix = 'workout_';

	/**
	 * @var array $exercise_meta_fields
	 */
	public $exercise_meta_fields = [];

	/**
	 * @var array $workout_meta_fields
	 */
	public $workout_meta_fields = [];

	/**
	 * Get global id
	 *
	 * @since 1.0.0
	 * @return int $post_id
	 */
	public function get_global_id() {

		global $post;

		$post_id = $post->ID;

		return $post_id;

	}

	/**
	 * Set custom meta fields
	 *
	 * define the custom fields for the CPT
	 *
	 * @since 1.0.0
	 * @return array $custom_meta_fields
	 */
	public function set_exercise_meta_fields( $prefix ) {

		$this->exercise_meta_fields = array(
			array(
				'label' => 'Category',
				'desc'  => 'Add a category that the exercise belongs to. Examples: "Core", "Legs", "Yoga", etc...',
				'id'    => $prefix . 'category',
				'type'  => 'text'
			),
			array(
				'label' => 'Instructions',
				'desc'  => 'Step by step instructions on how to perform the exercise. Skip to new line for each new instruction. **Do not number**',
				'id'    => $prefix . 'instructions',
				'type'  => 'textarea'
			),
			array(
				'label' => 'Caution',
				'desc'  => 'Add a caution about this exercise.',
				'id'    => $prefix . 'caution',
				'type'  => 'textarea'
			),
			array(
				'label' => 'Exercise Image',
				'desc'  => 'Add an image of the exercise being performed.',
				'id'    => $prefix . 'image',
				'type'  => 'image'
			),
		);

		return $this->exercise_meta_fields;

	}

	/**
	 * Set workout meta fields
	 *
	 * define the custom fields for the workout
	 *
	 * @since 1.0.0
	 * @return array $custom_meta_fields
	 */
	public function set_wokout_meta_fields( $prefix ) {

		$this->workout_meta_fields = array(
			array(
				'label' => 'Category',
				'desc'  => 'Add a category that the exercise belongs to. Examples: "Core", "Legs", "Yoga", etc...',
				'id'    => $prefix . 'category',
				'type'  => 'text'
			),
			array(
				'label' => 'Workout Directions',
				'desc'  => 'Add instructions for performing the workout. Skip to new line for each new instruction. **Do not number**',
				'id'    => $prefix . 'instructions',
				'type'  => 'textarea'
			),
			array(
				'label' => 'Workout Image',
				'desc'  => 'Add an image of the exercise being performed.',
				'id'    => $prefix . 'image',
				'type'  => 'image'
			),
			array(
				'label' => 'Add Day',
				'desc'  => 'Add exercises for a day.',
				'id'    => $prefix . 'day',
				'type'  => 'day-repeater'
			),
		);

		return $this->workout_meta_fields;

	}

	/**
	 * Get exercise meta fields
	 *
	 * @since 1.0.0
	 * @return array $exercise_meta_fields
	 */
	public function get_exercise_meta_fields() {

		return $this->set_exercise_meta_fields( $this->exercise_prefix );

	}

	/**
	 * Get workout meta fields
	 *
	 * @since 1.0.0
	 * @return array $exercise_meta_fields
	 */
	public function get_workout_meta_fields() {

		return $this->set_wokout_meta_fields( $this->workout_prefix );

	}

	/**
	 * Render text field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @return void
	 */
	public function render_text_field( $field, $meta ) {

		printf( '<span class="description">%s</span></br><input type="text" name="%s" id="%2$s" value="%s" size="30" />',
			esc_html( $field['desc'] ), esc_attr( $field['id'] ), esc_attr( $meta ) );

	}

	/**
	 * Render textarea field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @return void
	 */
	public static function render_textarea_field( $field, $meta ) {

		printf( '<span class="description">%s</span><br /><textarea name="%s" id="%2$s" cols="60" rows="4">%s</textarea>',
			esc_html( $field['desc'] ), esc_attr( $field['id'] ), esc_html( $meta ) );

	}

	/**
	 * Render checkbox field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @return void
	 */
	public static function render_checkbox_field( $field, $meta ) {

		echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '', '/>
		<label for="' . $field['id'] . '">' . $field['desc'] . '</label>';

	}

	/**
	 * Render select field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @return void
	 */
	public static function render_select_field( $field, $meta ) {

		printf( '<span class="description">%s</span><br />', esc_html( $field['desc'] ) );

		printf( '<select name="%s" id="%1$s">', esc_attr( $field['id'] ) );

		foreach ( $field['options'] as $option ) {

			echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['label'] ) . '</option>';

		}

		printf( '</select>' );

	}

	/**
	 * Render image field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @param int $post_id
	 * @return void
	 */
	public function render_image_field( $field, $meta, $post_id ) {

		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post_id ) );

		// See if there's a media id already saved as post meta
		$img_id = get_post_meta( $post_id, $field['id'], true );

		// Get the image src
		$img_src = wp_get_attachment_image_src( $img_id, 'full' );

		// For convenience, see if the array is valid
		$have_img = is_array( $img_src );

		?>

		<!-- Your image container, which can be manipulated with js -->
		<div class="custom-img-container">
			<span class="description"><?php echo esc_html( $field['desc'] ) ?></span><br/><br/>

			<?php if ( $have_img ) : ?>
				<img src="<?php echo $img_src[0] ?>" alt="" style="max-width:150px; max-height: 150px;"/>
			<?php endif; ?>
		</div>

		<!-- Your add & remove image links -->
		<p class="hide-if-no-js">
			<a class="upload-custom-img <?php if ( $have_img ) {
				echo 'hidden';
			} ?>"
			   href="<?php echo $upload_link ?>">
				<?php _e( 'Set custom image' ) ?>
			</a>
			<a class="delete-custom-img <?php if ( ! $have_img ) {
				echo 'hidden';
			} ?>"
			   href="#">
				<?php _e( 'Remove this image' ) ?>
			</a>
		</p>

		<!-- A hidden input to set and post the chosen image id -->
		<input class="custom-img-id" name="custom-img-id" type="hidden" value="<?php echo esc_attr( $img_id ); ?>"/>

		<?php

	}

	/**
	 * Render repeater field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @return void
	 */
	public function render_repeater_field( $field, $meta ) {

		$post_id = $this->get_global_id();

		echo '<span class="description">' . $field['desc'] . '</span><br/>';

		echo '<a class="repeatable-add button" href="#">+</a>

		<ul data-post-id="' . esc_attr( $post_id ) . '" id="' . $field['id'] . '-repeatable" class="custom_repeatable">';

		$i = 0;

		if ( $meta ) {

			foreach ( $meta as $row ) {

				echo '<li id="item-' . $i . '"><span class="sort hndle">|||</span>

				<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="' . $row . '" size="30" />

				<a class="repeatable-remove button" href="#">-</a></li>';

				$i ++;

			}

		} else {

			echo '<li><span class="sort hndle">|||</span>

			<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="" size="30" />

			<a class="repeatable-remove button" href="#">-</a></li>';

		}

		echo '</ul>';

	}

	/**
	 * Render day repeater field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string /int $meta
	 * @return void
	 */
	public function render_day_repeater_field( $field, $meta ) {

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'exercise',
			'post_status'      => 'publish',
		);

		$exercise_posts = get_posts( $args );

		$post_id = $this->get_global_id();

		echo '<span class="description">' . $field['desc'] . '</span><br/>';

		echo '<a class="day-repeat-add button" href="#">Add Day</a>

		<ul data-post-id="' . esc_attr( $post_id ) . '" id="' . $field['id'] . '-repeatable" class="day_repeat">';

		$i = 0;

		if ( $meta ) {

			foreach ( $meta as $row ) {

				echo '<li id="item-' . $i . '">

				<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="' . $row . '" size="30" />

				<a class="day-repeat-remove button" href="#">-</a></li>';

				$i++;

			}

		} else {

			print( '<li id="day-list-item" class="day-exercises">' );

				print( '<div class="day-header"><span><h3>Day 1</h3></div>' );

				print( '<div><p>Add Exercises</p></div><select class="wpbb-exercise-selection">' );

					print( '<option>Add Exercise</option>' );

					foreach ( $exercise_posts as $exercise_post ) :

						printf( '<option value="%s">%s</option>', esc_attr( $exercise_post->ID ), esc_html( $exercise_post->post_title ) );

					endforeach;

				print( '</select>' );

				printf( '<input class="wpbb-exercise-setting" name="post_id" value="%s" type="hidden"/>', $post_id );

				print( '<div class="selected-exercises">You\'ve added the following exercises:</div>' );

				print( '<div class="selected-exercises-wrap">' );

					print( '<ul id="exercise-list"></ul>' );

				print( '</div>' );

				print( '<div id="remove-btn"><a class="day-repeat-remove button" href="#">Remove Day</a></div>' );

			print( '</li>' );

		}

		echo '</ul>';

	}

}
