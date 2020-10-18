<?php

namespace MLWP\BulkRegisterPlugin\BulkRegisterTests;

use MLWP\BulkRegisterPlugin\BulkRegisterCustomPostTypes;
use WP_Mock\Tools\TestCase;
use WP_Mock;

class BulkRegisterCustomPostTypesTest extends TestCase {

	public $bulk_register;

	public function setUp(): void {
		$this->bulk_register = new BulkRegisterCustomPostTypes();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	/**
	 * @covers \BulkRegisterCustomPostTypes::register
	 */
	public function testMustReturnEmptyArrayIfArrayNotPassed() {
		$this->assertEmpty( $this->bulk_register->register( 'string' ) );
	}


	/**
	 * @covers \BulkRegisterCustomPostTypes::register
	 */
	public function testMustReturnEmptyArrayIfEmptyArrayPassed() {
		$this->assertEmpty( $this->bulk_register->register( [] ) );
	}

	/**
	 * @depends testMustReturnEmptyArrayIfArrayNotPassed
	 * @depends testMustReturnEmptyArrayIfEmptyArrayPassed
	 * @covers \BulkRegisterCustomPostTypes::register
	 * @dataProvider registerReturnArrayDataProvider
	 */
	public function testReturnArray( $post_types ) {
		$expected_return_value = $post_types;

		\WP_Mock::userFunction(
			'register_post_type',
			array(
				'times' => count( $post_types ),
				'args' => array( \WP_Mock\Functions::type( 'string' ), \WP_Mock\Functions::type( 'array' ) ),
				'return_in_order' => $post_types,
			)
		);

		$this->assertEqualsCanonicalizing( $expected_return_value, $this->bulk_register->register( $post_types ) );
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
}
