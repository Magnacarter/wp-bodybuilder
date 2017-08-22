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
	 * Set custom meta fields
	 *
	 * define the custom fields for the CPT
	 *
	 * @since 1.0.0
	 * @return array $custom_meta_fields
	 */
	public static function set_exercise_meta_fields() {

		$prefix = 'exercise_';

		$exercise_meta_fields = array(
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
				'label' => 'Repeatable',
				'desc'  => 'A description for the field.',
				'id'    => $prefix.'repeatable',
				'type'  => 'repeatable'
			),
			array(
				'label' => 'Exercise Image',
				'desc'  => 'Add an image of the exercise being performed.',
				'id'    => $prefix.'image',
				'type'  => 'image'
			),
		);

		return $exercise_meta_fields;

	}

	/**
	 * Render text field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string/int $meta
	 * @return void
	 */
	public static function render_text_field( $field, $meta ){

		printf( '<span class="description">%s</span></br><input type="text" name="%s" id="%1$s" value="%s" size="30" />',
		esc_html( $field['desc'] ), esc_attr( $field['id'] ), esc_attr( $meta ) );

	}

	/**
	 * Render textarea field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string/int $meta
	 * @return void
	 */
	public static function render_textarea_field( $field, $meta ){

		printf( '<span class="description">%s</span><br /><textarea name="%s" id="%1$s" cols="60" rows="4">%s</textarea>',
		esc_html( $field['desc'] ), esc_attr( $field['id'] ), esc_html( $meta ) );

	}

	/**
	 * Render checkbox field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string/int $meta
	 * @return void
	 */
	public static function render_checkbox_field( $field, $meta ){

		echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '','/>
		<label for="' . $field['id'] . '">' . $field['desc'] . '</label>';

	}

	/**
	 * Render select field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string/int $meta
	 * @return void
	 */
	public static function render_select_field( $field, $meta ){

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
	 * @param string/int $meta
	 * @param int $post_id
	 * @return void
	 */
	public static function render_image_field( $field, $meta, $post_id ){

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
				<img src="<?php echo $img_src[0] ?>" alt="" style="max-width:150px; max-height: 150px;" />
			<?php endif; ?>
		</div>

		<!-- Your add & remove image links -->
		<p class="hide-if-no-js">
			<a class="upload-custom-img <?php if ( $have_img  ) { echo 'hidden'; } ?>"
			   href="<?php echo $upload_link ?>">
				<?php _e('Set custom image') ?>
			</a>
			<a class="delete-custom-img <?php if ( ! $have_img  ) { echo 'hidden'; } ?>"
			   href="#">
				<?php _e('Remove this image') ?>
			</a>
		</p>

		<!-- A hidden input to set and post the chosen image id -->
		<input class="custom-img-id" name="custom-img-id" type="hidden" value="<?php echo esc_attr( $img_id ); ?>" />

		<?php

	}

	/**
	 * Render repeater field
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string/int $meta
	 * @return void
	 */
	public static function render_repeater_field( $field, $meta ){

		echo '<span class="description">' . $field['desc'] . '</span><br/>';

		echo '<a class="repeatable-add button" href="#">+</a>

		<ul id="' . $field['id'] . '-repeatable" class="custom_repeatable">';

		$i = 0;

		if ( $meta ) {

			foreach ( $meta as $row ) {

				echo '<li><span class="sort hndle">|||</span>

					<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="' . $row . '" size="30" />

					<a class="repeatable-remove button" href="#">-</a></li>';

				$i++;

			}

		} else {

			echo '<li><span class="sort hndle">|||</span>

				<input type="text" name="' . $field['id'] . '[' . $i . ']" id="' . $field['id'] . '" value="" size="30" />

				<a class="repeatable-remove button" href="#">-</a></li>';

		}

		echo '</ul>';

	}

}
