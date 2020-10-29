<?php

namespace MLWP\BulkRegisterPlugin\BulkRegisterTests;

use MLWP\BulkRegisterPlugin\BulkRegisterCustomTaxonomies;
use WP_Mock\Tools\TestCase;
use WP_Mock;
use Mockery;

class BulkRegisterCustomTaxonomiesTest extends TestCase {

	public $bulk_register;

	public function setUp(): void {
		$this->bulk_register = new BulkRegisterCustomTaxonomies();
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
	 * @covers \BulkRegisterCustomTaxonomies::register
	 */
	public function testMustReturnArrayIfInvalidParamsPassed( $param ) {
		$this->assertisArray( $this->bulk_register->register( $param ) );
	}

	 /**
	 * @depends testMustReturnArrayIfInvalidParamsPassed
	 * @dataProvider invalidParamProvider
	 * @covers \BulkRegisterCustomTaxonomies::register
	 */
	public function testMustReturnEmptyArrayIfInvalidParamsPassed( $param ) {
		$this->assertEmpty( $this->bulk_register->register( $param ) );
	}


	/**
	 * @dataProvider invalidParamProvider
	 * @covers \BulkRegisterCustomTaxonomies::maybe_prepare_object_types
	 */
	public function testMustReturnNullIfInvalidParamsPassed( $param ) {
		$this->assertEmpty( $this->bulk_register::maybe_prepare_object_types( $param ) );
	}

	/**
	 * @covers \BulkRegisterCustomTaxonomies::maybe_prepare_object_types
	 */
	public function testMustReturnNullIfMissingArrayElementWithKeyObjectType() {
		$this->assertNull( $this->bulk_register::maybe_prepare_object_types( [ 'foo' => 'bar', 'jane' ] ) );
	}

	/**
	 * @covers \BulkRegisterCustomTaxonomies::maybe_prepare_object_types
	 */
	public function testMustReturnArrayOfMultipleObjectTypesKeysIfArrayPassedWithArrayOfKeys () {
		$this->assertEqualsCanonicalizing( [ 'cpt_key_1', 'cpt_key_2' ], $this->bulk_register::maybe_prepare_object_types( [ 'object_type' => [ 'cpt_key_1', 'cpt_key_2' ] ] ) );
	}

	/**
	 * @covers \BulkRegisterCustomTaxonomies::maybe_prepare_object_types
	 */
	public function testMustReturnStringOfObjectTypeKeysIfStringPassed () {
		$this->assertSame( 'cpt_key_1', $this->bulk_register::maybe_prepare_object_types( [ 'object_type' => 'cpt_key_1' ] ) );
	}

	/**
	 * @depends testMustReturnEmptyArrayIfInvalidParamsPassed
	 * @covers \BulkRegisterCustomTaxonomies::register
	 * @dataProvider registerReturnArrayDataProvider
	 */
	public function testReturnArray( $taxonomies, $expected_return ) {

		$this->expectSanitizeKeyPassthru( count( $taxonomies ) );

		\WP_Mock::userFunction(
			'register_taxonomy',
			array(
				'times' => count( $taxonomies ),
				'args' => array( \WP_Mock\Functions::type( 'string' ), "*", \WP_Mock\Functions::type( 'array' ) ),
				'return_in_order' => $expected_return,
			)
		);

		$this->assertEqualsCanonicalizing( $expected_return, $this->bulk_register->register( $taxonomies ) );
	}

	public function registerReturnArrayDataProvider() {

		$wp_taxonomy_instance = Mockery::mock( '\WP_Taxonomy' );
		$wp_error_instance = Mockery::mock( '\WP_Error' );

		$data = [
			[
				[ 'category_a' => [ 'object_type' => 'object_name' ] ],
				[ $wp_taxonomy_instance ],
			],
			[
				[ 'category_b', 'category_c' ],
				[ $wp_taxonomy_instance, $wp_taxonomy_instance ],
			],
			[
				[ 'category_invalid_foo', 'category_invalid_bar' ],
				[ $wp_error_instance, $wp_error_instance ],
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
