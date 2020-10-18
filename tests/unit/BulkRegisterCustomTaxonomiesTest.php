<?php

namespace MLWP\BulkRegisterPlugin\BulkRegisterTests;

use MLWP\BulkRegisterPlugin\BulkRegisterCustomTaxonomies;
use WP_Mock\Tools\TestCase;
use WP_Mock;

class BulkRegisterCustomTaxonomiesTest extends TestCase {

	public $bulk_register;

	public function setUp(): void {
		$this->bulk_register = new BulkRegisterCustomTaxonomies();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	 /**
	 * @covers \BulkRegisterCustomTaxonomies::register
	 */
	public function testMustReturnEmptyArrayIfArrayNotPassed() {
		$this->assertEmpty( $this->bulk_register->register( 'string' ) );
	}

	/**
	 * @covers \BulkRegisterCustomTaxonomies::register
	 */
	public function testMustReturnEmptyArrayIfEmptyArrayPassed() {
		$this->assertEmpty( $this->bulk_register->register( [] ) );
	}


	/**
	 * @covers \BulkRegisterCustomTaxonomies::maybe_prepare_object_types
	 */
	public function testMustReturnNullIfEmptyArrayPassed() {
		$this->assertEmpty( $this->bulk_register::maybe_prepare_object_types( [] ) );
	}

	/**
	 * @covers \BulkRegisterCustomTaxonomies::maybe_prepare_object_types
	 */
	public function testMustReturnNullIfNonArrayPassed() {
		$this->assertNull( $this->bulk_register::maybe_prepare_object_types( '' ) );
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
	 * @depends testMustReturnEmptyArrayIfArrayNotPassed
	 * @depends testMustReturnEmptyArrayIfEmptyArrayPassed
	 * @covers \BulkRegisterCustomTaxonomies::register
	 * @dataProvider registerReturnArrayDataProvider
	 */
	public function testReturnArray( $taxonomies ) {

		\WP_Mock::userFunction(
			'register_taxonomy',
			array(
				'times' => count( $taxonomies ),
				'args' => array( \WP_Mock\Functions::type( 'string' ), null, \WP_Mock\Functions::type( 'array' ) ),
				'return_in_order' => $taxonomies,
			)
		);

		$this->assertEqualsCanonicalizing( $taxonomies, $this->bulk_register->register( $taxonomies ) );
	}

	public function registerReturnArrayDataProvider() {
		$data = [
			[
				[ 'category_a' ],
			],
			[
				[ 'category_b', 'category_c' ],
			],
		];
		return $data;
	}
}
