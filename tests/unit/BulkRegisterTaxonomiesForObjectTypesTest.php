<?php

namespace MLWP\BulkRegisterPlugin\BulkRegisterTests;

use MLWP\BulkRegisterPlugin\BulkRegisterTaxonomiesForObjectTypes;
use WP_Mock\Tools\TestCase;
use WP_Mock;

class BulkRegisterTaxonomiesForObjectTypesTest extends TestCase {

	public $bulk_register;

	public function setUp(): void {
		$this->bulk_register = new BulkRegisterTaxonomiesForObjectTypes();
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	 /**
	 * @dataProvider invalidParamProvider
	 * @covers \BulkRegisterTaxonomiesForObjectTypes::register
	 */
	public function testMustReturnArrayIfInvalidParamsPassed( $param ) {
		$this->assertisArray( $this->bulk_register->register( $param ) );
	}

	 /**
	 * @depends testMustReturnArrayIfInvalidParamsPassed
	 * @dataProvider invalidParamProvider
	 * @covers \BulkRegisterTaxonomiesForObjectTypes::register
	 */
	public function testMustReturnEmptyArrayIfInvalidParamsPassed( $param ) {
		$this->assertEmpty( $this->bulk_register->register( $param ) );
	}

	/**
	 * @depends testMustReturnEmptyArrayIfInvalidParamsPassed
	 * @covers \BulkRegisterTaxonomiesForObjectTypes::register
	 * @dataProvider taxonomiesForObjectTypesProvider
	 */
	public function testTaxonomiesForObjectTypesRegistration( $tax_object_pairs, $expected_return_values ) {

		\WP_Mock::userFunction(
			'register_taxonomy_for_object_type',
			array(
				'times' => count( $tax_object_pairs ),
				'args' => array( "*", "*" ),
				'return_in_order' => $expected_return_values,
			)
		);

		$this->assertEqualsCanonicalizing( $expected_return_values, $this->bulk_register->register( $tax_object_pairs ) );
	}

	public function taxonomiesForObjectTypesProvider() {
		// tax_object_pairs, expected_return_values
		return [
			[
				'test successful assignment' =>
				[
					'tax_name_foo' => 'obj_name_foo',
					'tax_name_bar' => 'tax_name_baz',
				],
				[ true, true ],
			],
			[
				'test partioal successful assignment' =>
				[
					'tax_name_failure' => 'obj_name_failure',
					'tax_name_bar' => 'tax_name_baz',
				],
				[ false, true ],
			],
			[
				'test unsuccessful assignment if non assoc array element passed' =>
				[
					'string',
					0,
					false,
					true,
					null,
				],
				[ false, false, false, false, false ],
			]
		];
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
