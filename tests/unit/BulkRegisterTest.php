<?php
namespace MLWP\BulkRegisterPlugin\BulkRegisterTests;

use MLWP\BulkRegisterPlugin\BulkRegister;
use WP_Mock\Tools\TestCase;
use WP_Mock;

/**
 * Testing the abstract class.
 */
class BulkRegisterTest extends TestCase {

	/**
	 * Instance of BulkRegister class
	 *
	 * @var BulkRegister
	 */
	protected $anonymous_class_from_bulk_register;

	public function setUp(): void {

		// Create a new instance from the abstract BulkRegister Class
		$this->anonymous_class_from_bulk_register = new class extends BulkRegister {
			public function register( $registerables ) {
				return $this;
			}
		};

		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	/**
	 * @dataProvider parseArgsProvider
	 * @covers \BulkRegister::parse_args
	 */
	public function testParseArgs( $extra_args, $default_args, $expected ) {
		$this->assertEquals( $expected, $this->anonymous_class_from_bulk_register->parse_args( $extra_args, $default_args ) );
	}

	/**
	 * @dataProvider prepareLabelFromKeyProvider
	 * @covers \BulkRegister::prepare_label_from_key
	 */
	public function testPrepareLabelFromKey( $key, $expected ) {
		$this->assertSame( $expected, $this->anonymous_class_from_bulk_register->prepare_label_from_key( $key ) );
	}

	/**
	 * @dataProvider prepareKeyProvider
	 * @covers \BulkRegister::prepare_key
	 */
	function testPrepareKey( $possible_key_a, $possible_key_b, $expected ) {

		\WP_Mock::userFunction(
			'sanitize_key',
			array(
				'times' => 1,
				'args' => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $expected,
			)
		);

		$this->assertSame( $expected, $this->anonymous_class_from_bulk_register->prepare_key( $possible_key_a, $possible_key_b ) );
	}

	/**
	 * @covers \BulkRegister::maybe_extra_args
	 */
	public function testMustReturnNullIfArrayNotPassed() {
		$this->assertNull( $this->anonymous_class_from_bulk_register->maybe_extra_args( 'foobarbaz' ) );
	}

	/**
	 * @covers \BulkRegister::maybe_extra_args
	 */
	public function testMustReturnSameArray() {
		$this->assertJsonStringEqualsJsonString( json_encode( [ 'jane' => 'doe' ] ), json_encode( $this->anonymous_class_from_bulk_register->maybe_extra_args( [ 'jane' => 'doe' ] ) ) );
	}

	public function prepareKeyProvider() {
		// possible_key_a, possible_key_b, expected
		return [
			'must return sanitized key' =>
			[
				'foo bar baz',
				'willnotbeused',
				'foobarbaz',
			],

			'must return key "B" if key "A" is an integer' =>
			[
				0,
				'foobar',
				'foobar',
			],

			'must return key "A" if key "A" is not an integer' =>
			[
				'foobar',
				'willnotbeused',
				'foobar',
			],
		];
	}

	public function prepareLabelFromKeyProvider() {
		// key, expected
		return [
			'must convert underscore to space with camelcase' =>
			[
				'foo_bar',
				'Foo Bar',
			],
			'must convert hyphen to space' =>
			[
				'foo-bar',
				'Foo Bar',
			],
			'must convert words camelcsase' =>
			[
				'foo-bar-baz',
				'Foo Bar Baz',
			],
		];
	}

	public function parseArgsProvider() {
		// extra args, default args, expected
		return [
			'must return extra args array if default args passed is not an array'  =>
			[
				[ 'extra' => 'args' ],
				'baz',
				[ 'extra' => 'args' ],
			],
			'must return empty array if extra args and default args are not arrays'  =>
			[
				'foo',
				'bar',
				[],
			],
			'extra args must take over default args if 1 dimension array'  =>
			[
				[ 'foo' => 1, ],
				[ 'foo' => 2, ],
				[ 'foo' => 1, ],
			],
			'extra arg must take over default args if multi dimension array'  =>
			[
				[ 'foo' => [ 'bar' => 1, ], ],
				[ 'foo' => [ 'bar' => 2, ], ],
				[ 'foo' => [ 'bar' => 1, ], ],
			],

			'must merge both args (extra and default) and does not remove elements in default args'  =>
			[
				[ 'foobar' => [ 'foo' => 'bar', ], ],
				[ 'foobar' => [ 'jane' => 'doe', ], ],
				[ 'foobar' => [ 'foo' => 'bar', 'jane' => 'doe', ], ],
			],
		];
	}
}
