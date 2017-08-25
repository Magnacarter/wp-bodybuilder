<?php
/**
 * Class Bodybuilder Tests
 *
 * @package Wp_Bodybuilder
 */

use Bodybuilder\plugin\admin\custom\Custom_Field;
use Bodybuilder\plugin\admin\custom\Exercise_Custom_Fields;

/**
 * Bodybuilder test cases
 */
class Bodybuilder_Tests extends WP_UnitTestCase {

	public function test_exercise_meta() {

		$cf = new Custom_Field();

		$prefix = $cf->prefix;

		$set_meta = $cf->set_exercise_meta_fields($prefix);

		$this->assertEquals( $set_meta, $cf->get_exercise_meta_fields() );

	}

}
