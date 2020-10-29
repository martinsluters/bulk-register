<?php

namespace MLWP\BulkRegisterPlugin\BulkRegisterTests;

use MLWP\BulkRegisterPlugin\BulkRegisterCustomPostTypes;
use WP_Mock\Tools\TestCase;
use WP_Mock;

class BulkRegisterCustomPostTypesTest extends TestCase {

	public $bulk_register_cpt;

	public function setUp(): void {
		$this->bulk_register_cpt = new BulkRegisterCustomPostTypes();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	public function expectSanitizeKeyPassthru( $times ) {
		\WP_Mock::passthruFunction(
			'sanitize_key',
			array(
				'times' => $times,
			)
		);
	}

	 /**
	 * @dataProvider invalidParamProvider
	 * @covers \BulkRegisterCustomPostTypes::register
	 */
	public function testMustReturnArrayIfInvalidParamsPassed( $param ) {
		$this->assertisArray( $this->bulk_register_cpt->register( $param ) );
	}

	 /**
	 * @depends testMustReturnArrayIfInvalidParamsPassed
	 * @dataProvider invalidParamProvider
	 * @covers \BulkRegisterCustomPostTypes::register
	 */
	public function testMustReturnEmptyArrayIfInvalidParamsPassed( $param ) {
		$this->assertEmpty( $this->bulk_register_cpt->register( $param ) );
	}

	/**
	 * @depends testMustReturnEmptyArrayIfInvalidParamsPassed
	 * @covers \BulkRegisterCustomPostTypes::register
	 * @dataProvider registerReturnArrayDataProvider
	 */
	public function testPostTypeRegistration( $post_types ) {
		$expected_return_value = $post_types;

		$this->expectSanitizeKeyPassthru( count( $post_types ) );

		\WP_Mock::userFunction(
			'register_post_type',
			array(
				'times' => count( $post_types ),
				'args' => array( \WP_Mock\Functions::type( 'string' ), \WP_Mock\Functions::type( 'array' ) ),
				'return_in_order' => $post_types,
			)
		);

		$this->assertEqualsCanonicalizing( $expected_return_value, $this->bulk_register_cpt->register( $post_types ) );
	}

	public function registerReturnArrayDataProvider() {
		$data = [
			[
				[ 'car' ],
			],
			[
				[ 'car', 'car_shop' ],
			],
		];
		return $data;
	}

	public function invalidParamProvider() {
		return [
			[ 'string' ],
			[ null ],
			[ false ],
			[ true ],
			[ 1 ],
			[ 0 ],
			[ [] ],
			[ new \stdClass() ],
		];
	}
}
