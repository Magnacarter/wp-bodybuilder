<?php
/**
 * Class SampleTest
 *
 * @package Wp_Bodybuilder
 */

use Bodybuilder\plugin\admin\custom\Custom_Field;
use Bodybuilder\plugin\admin\custom\Exercise_Custom_Fields;

/**
 * Sample test case.
 */
class Test_Sample extends WP_UnitTestCase {

	public function test_prefix() {

		$cf = new Custom_Field();

		$this->assertEquals( $cf->prefix, $cf->get_prefix() );

	}

}
