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
		WP_Mock::setUp();

		// Create a new instance from the abstract BulkRegister Class
		$this->anonymousClassFromBulkRegister = new class extends BulkRegister {
			public function register( $registerables  ) {
				return $this;
			}
		};
	}

	public function tearDown(): void
	{
		WP_Mock::tearDown();
	}

   /**
	* @covers \BulkRegister::parse_args
	*/
	public function testMustReturnEmptyArrayIfBothNonArraysPassed() {
		$this->assertEmpty( $this->anonymousClassFromBulkRegister->parse_args( 'string', 'string2' ) );
	}

   /**
	* @covers \BulkRegister::parse_args
	*/
	public function testMustReturnFirstArrayIfOtherNonArrayPassed() {
		$first_array = [ 'something' => 'else' ];

		$this->assertJsonStringEqualsJsonString(
			json_encode( $first_array ),
			json_encode( $this->anonymousClassFromBulkRegister->parse_args( $first_array, 'string' ) )
		);
	}

	/**
	 * @dataProvider argsProvider
	 * @covers \BulkRegister::parse_args
	 */
	public function testParseArgs( $extra_args, $default_args, $expected ) {
		$this->assertEquals( $expected, $this->anonymousClassFromBulkRegister->parse_args( $extra_args, $default_args ) );
	}


     /**
     * @covers \BulkRegister::prepare_label_from_key
     */
    public function testConvertsUnderscoreToSpaceWithCamelcase()
    {
       	$this->assertSame( 'Foo Bar', $this->anonymousClassFromBulkRegister->prepare_label_from_key( 'foo_bar' ) );
    }

     /**
     * @covers \BulkRegister::prepare_label_from_key
     */
    public function testConvertsHyphenToSpace()
    {
       	$this->assertSame( 'Foo Bar Baz', $this->anonymousClassFromBulkRegister->prepare_label_from_key( 'foo-bar-baz' ) );
    }

    /**
     * @covers \BulkRegister::prepare_label_from_key
     */
    public function testConvertsWordsCamelcase()
    {
       	$this->assertSame( 'Foo Bar', $this->anonymousClassFromBulkRegister->prepare_label_from_key( 'foo_bar' ) );
    }


	 /**
	 * @covers \BulkRegister::prepare_key
	 */
	public function testReturnsString() {
		$this->assertIsString( $this->anonymousClassFromBulkRegister->prepare_key( 0, 'foo' ) );
		$this->assertIsString( $this->anonymousClassFromBulkRegister->prepare_key( 'foo', 'bar' ) );
	}

	 /**
	 * @covers \BulkRegister::prepare_key
	 */
	public function testReturnsKeyBifKeyAisInteger() {
		$this->assertSame( 'foobar', $this->anonymousClassFromBulkRegister->prepare_key( 0, 'foobar' ) );
	}

	 /**
	 * @covers \BulkRegister::prepare_key
	 */
	public function testReturnsKeyAifKeyAisNotInteger() {
		$this->assertSame( 'foobar', $this->anonymousClassFromBulkRegister->prepare_key( 'foobar', null ) );
	}

	/**
	 * @covers \BulkRegister::maybe_prepare_extra_args
	 */
	public function testMustReturnNullIfArrayNotPassed() {
		$this->assertNull( $this->anonymousClassFromBulkRegister->maybe_prepare_extra_args( 'foobarbaz' ) );
	}

	/**
	 * @covers \BulkRegister::maybe_prepare_extra_args
	 */
	public function testMustReturnSameArray() {
		$this->assertJsonStringEqualsJsonString( json_encode( [ 'jane' => 'doe' ] ), json_encode( $this->anonymousClassFromBulkRegister->maybe_prepare_extra_args( [ 'jane' => 'doe' ] ) ) );
	}

	public function argsProvider() {
		// extra args, default args, expected
		return [
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
