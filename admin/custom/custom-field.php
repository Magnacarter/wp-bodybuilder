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

use Bodybuilder\plugin\admin\custom\Post_Custom_Fields;
use Bodybuilder\plugin\admin\custom\Custom_Tables;

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
	 * Check whitespace or empty
	 *
	 * Callback for array_filter to filter an array's values
	 * for whitespace or empty strings and remove it
	 *
	 * @since 1.0.0
	 * @param string $val
	 * @return bool
	 */
	public function check_whitespace_or_empty( $val ) {

		return $val != '' || preg_match( '/\S/', $val );

	}

	/**
	 * Sanitize array values
	 *
	 * Sanatize each value in an array before updating the db
	 *
	 * @since 1.0.0
	 * @param string $val
	 * @return string $val
	 */
	public function sanitize_array_values( $val ) {

		return sanitize_text_field( $val );

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
	public function set_workout_meta_fields( $prefix ) {

		$this->workout_meta_fields = array(
			array(
				'label' => 'Workout Name',
				'desc'  => 'Name the workout',
				'id'    => $prefix . 'name',
				'type'  => 'text'
			),
			array(
				'label' => 'Author',
				'desc'  => 'Who designed this workout?',
				'id'    => $prefix . 'author',
				'type'  => 'text'
			),
			array(
				'label' => 'Description',
				'desc'  => 'Give a brief overview of the workout (SEO Enhancement)',
				'id'    => $prefix . 'description',
				'type'  => 'wysiwyg'
			),
			array(
				'label' => 'Intensity',
				'desc'  => 'Average target heartrate (SEO Enhancement)',
				'id'    => $prefix . 'intensity',
				'type'  => 'text'
			),
			array(
				'label' => 'Rest Periods',
				'desc'  => 'How many rest days are there per week? (SEO Enhancement)',
				'id'    => $prefix . 'rest_periods',
				'type'  => 'text'
			),
			array(
				'label' => 'Repetitions',
				'desc'  => 'How many weeks should one do this workout? (SEO Enhancement)',
				'id'    => $prefix . 'repetitions',
				'type'  => 'text'
			),
			array(
				'label' => 'Workload',
				'desc'  => 'What is the energy expenditure? e.g., calories burned (SEO Enhancement)',
				'id'    => $prefix . 'workload',
				'type'  => 'text'
			),
			array(
				'label' => 'Category',
				'desc'  => 'Add a category that the exercise belongs to. Examples: "Core", "Legs", "Yoga", etc... (SEO Enhancement)',
				'id'    => $prefix . 'category',
				'type'  => 'text'
			),
			array(
				'label' => 'Average Workout Time',
				'desc'  => 'Average time it takes to complete each workout (SEO Enhancement)',
				'id'    => $prefix . 'duration',
				'type'  => 'text'
			),
			array(
				'label' => 'Workout Directions',
				'desc'  => 'Add instructions for performing the workout. Skip to new line for each new instruction. **Do not number** (SEO Enhancement)',
				'id'    => $prefix . 'instructions',
				'type'  => 'textarea'
			),
			array(
				'label' => 'Workout Image',
				'desc'  => 'Add an image of the exercise being performed. (SEO Enhancement)',
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

		return $this->set_workout_meta_fields( $this->workout_prefix );

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
			esc_html( $field['desc'] ), esc_attr( $field['id'] ), stripslashes_deep( esc_attr( $meta ) ) );

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
			esc_html( $field['desc'] ), esc_attr( $field['id'] ), stripslashes( esc_html( $meta ) ) );

	}

	public static function render_wysiwyg_field( $field, $meta ) {

		// Form fields
		?>

		<span class="description"><?php echo esc_html( $field['desc'] ); ?></span><br />

		<?php

		$settings = array(
			'editor_height' => 200,
			'wpautop'       => false,
			'media_buttons' => false,
			'quicktags'     => array( 'buttons' => 'strong,em,del,ul,ol,li,close' )
		);

		$list_field = wp_editor( stripslashes( $meta ), $field['id'], $settings );

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

		print( '</select>' );

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
		$post_type   = get_post_type( $post_id );

		// See if there's a media id already saved as post meta
		if( $post_type == 'exercise' ) {

			$img_id = get_post_meta( $post_id, $field['id'], true );

		} else {

			$img_id = Custom_Tables::get_workout_meta( $post_id, $field['id'] );

		}

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

		printf( '<span class="description">%s</span><br/>', $field['desc'] );

		print( '<a class="repeatable-add button" href="#">+</a>' );

		printf( '<ul data-post-id="%s" id="%s-repeatable" class="custom_repeatable">', esc_attr( $post_id ), $field['id'] );

		$i = 0;

		if ( $meta ) {

			foreach ( $meta as $row ) {

				printf( '<input type="text" name="%s" id="%s" value="%s" size="30" />', $field['id'][$i], $field['id'], $row  );

				print( '<a class="repeatable-remove button" href="#">-</a></li>' );

				$i ++;

			}

		} else {

			printf( '<input type="text" name="%s" id="%s" value="" size="30" />', $field['id'][$i], $field['id'] );

			print( '<a class="repeatable-remove button" href="#">-</a></li>' );

		}

		print( '</ul>' );

	}

	/**
	 * Load new day
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_new_day( $exercise_posts ) {

		?>
		<li id="day-list-item" class="day-exercises">
			<div class="day-header"><span><h3>Day 1</h3></div>
			<div id="remove-btn"><a class="day-repeat-remove button" href="#">Remove Day</a></div>
			<div class="selected-exercises-wrap">
				<ul id="exercise-list"></ul>
			</div>
			<div class="exercise-title"><p>Add Exercises</p></div>
			<select class="wpbb-exercise-selection">
				<option>Add Exercise</option>
				<?php foreach ( $exercise_posts as $exercise_post ) :

					printf( '<option value="%s">%s</option>', esc_attr( $exercise_post->ID ), esc_html( $exercise_post->post_title ) );

				endforeach; ?>
			</select>
		</li>
		<?php

	}

	/**
	 * Load saved workout
	 *
	 * @since 1.0.0
	 * @param $exercise_posts
	 * @param $workout
	 * @return void
	 */
	public function load_saved_workout( $exercise_posts, $workout ) {

		$d = 0;

		foreach ( $workout as $day ) {

			?>
			<li id="day-list-item" class="day-exercises">
				<div class="day-header"><span><h3><?php echo esc_html( $workout[$d]->day ); ?></h3></div>
				<div id="remove-btn"><a class="day-repeat-remove button" href="#">Remove Day</a></div>
				<div class="selected-exercises-wrap">
					<ul id="exercise-list">
						<?php
						$exercises = $workout[$d]->exercises;

						$this->load_exercises( $exercises );
						?>
					</ul>
				</div>
				<div class="exercise-title"><p>Add Exercises</p></div>
				<select class="wpbb-exercise-selection">
					<option>Add Exercise</option>
					<?php foreach ( $exercise_posts as $exercise_post ) :

						printf( '<option value="%s">%s</option>', esc_attr( $exercise_post->ID ), esc_html( $exercise_post->post_title ) );

					endforeach; ?>
				</select>
			</li>
			<?php

			$d++;

		}

	}

	/**
	 * Load exercises
	 *
	 * @since 1.0.0
	 * @param array $exercises
	 * @return void
	 */
	public function load_exercises( $exercises ) {

		if( ! isset( $exercises ) )
			return;

		foreach( $exercises as $exercise ) {

			$sets  = $exercise[1]->sets;
			$reps  = $exercise[1]->reps;
			$rest  = $exercise[1]->rest;
			$e_id  = intval( $exercise[0]->id );
			$title = get_the_title( $e_id );

			?>
			<li class="list" data-exercise-id="<?php echo esc_attr( $e_id ); ?>">
				<h4><strong><?php echo esc_html( $title ); ?> :</strong></h4>
				<div class="set-input float-left">
					<label for="sets">Sets</label>
					<input type="text" id="sets" name="sets" value="<?php echo esc_attr( $sets ); ?>"/>
				</div>
				<div class="rep-input float-left">
					<label for="reps">Reps/Duration</label>
					<input type="text" id="reps" name="reps" value="<?php echo esc_attr( $reps ); ?>"/>
				</div>
				<div class="rest-input float-left">
					<label for="rest">Rest Between Set</label>
					<input type="text" id="rest" name="rest" value="<?php echo esc_attr( $rest ); ?>"/>
				</div>
				<div class="float-left">
					<a href="" class="remove-exercise"></a>
				</div>
			</li>
			<div class="clear"></div>
			<?php

		}

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
		$post_id        = $this->get_global_id();
		$workout        = Post_Custom_Fields::get_workout();

		printf( '<span class="description">%s</span><br/>', $field['desc'] );

		printf( '<ul data-post-id="%s" id="%s-repeatable" class="day_repeat">', esc_attr( $post_id ), $field['id'] );

		$i = 0;

		if ( $meta ) {

			foreach ( $meta as $row ) {

				printf( '<li id="item-%s>', $field['id'] );

				printf( '<input type="text" name="%s" id="%s" value="%s" size="30" />', $field['id'][ $i ], $field['id'], $row );

				print( '<a class="day-repeat-remove button" href="#">-</a></li>' );

				$i ++;

			}

		} elseif( ! empty( $workout ) ) {

			$workout = json_decode( $workout[0]->workout );

			$this->load_saved_workout( $exercise_posts, $workout );

		} else {

			$this->load_new_day( $exercise_posts );

		}

		print( '</ul>' );

		print( '<a class="day-repeat-add button" href="#">Add Day</a>' );

	}

}
