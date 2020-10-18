<?php
namespace  MLWP\BulkRegisterPlugin;

/**
 * Abstraction of registering something
 */
abstract class BulkRegister {

	/**
	 * Main method to register something
	 *
	 * @param  mix $registerables Something we pass to register.
	 */
	abstract public function register( $registerables );

	/**
	 * Smart parse args
	 * Almost the same as wp_parse_args however it does deep recursive merge.
	 *
	 * @param  array $extra_args Passed by ref. Array to merge.
	 * @param  array $default_args  array to merge.
	 * @return array
	 */
	public static function parse_args( $extra_args, $default_args ) {

		if ( ! is_array( $extra_args ) && ! is_array( $default_args ) ) {
			return array();
		} else if ( ! is_array( $extra_args ) ) {
			return $default_args;
		} else if ( ! is_array( $default_args ) ) {
			return $extra_args;
		}

		$result = $default_args;

		if ( ! empty( $extra_args ) ) {
			foreach ( $extra_args as $k => &$v ) {
				if ( is_array( $v ) && isset( $result[ $k ] ) ) {
					$result[ $k ] = self::parse_args( $v, $result[ $k ] );
				} else {
					$result[ $k ] = $v;
				}
			}
		}
		return $result;
	}

	/**
	 * Parse label from key
	 *
	 * @param  string $key key.
	 * @return string Prepared label.
	 */
	public static function prepare_label_from_key( string $key ) {
		return ucwords( preg_replace( array( '/-/', '/_/' ), ' ', $key ) );
	}

	/**
	 * Prepares a key.
	 * If key $possible_key_a is integer then return $possible_key_b else $possible_key_a
	 *
	 * @param  mix $possible_key_a can be basically anything.
	 * @param  mix $possible_key_b can be basically anything.
	 * @return string key.
	 */
	public static function prepare_key( $possible_key_a, $possible_key_b ) {
		return is_int( $possible_key_a ) ? (string) $possible_key_b : (string) $possible_key_a;
	}

	/**
	 * If extra args array pass them back
	 *
	 * @param  mix $maybe_extra_args Expecting array or anything else.
	 * @return array|null The array of extra arguments. Null if not array.
	 */
	public static function maybe_prepare_extra_args( $maybe_extra_args ) {
		return is_array( $maybe_extra_args ) ? $maybe_extra_args : null;
	}
}
