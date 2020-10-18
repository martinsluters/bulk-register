<?php
namespace  MLWP\BulkRegisterPlugin;

/**
 * Implementation of registering custom post type
 */
class BulkRegisterCustomPostTypes extends BulkRegister {

	/**
	 * Default custom post type arguments
	 *
	 * @var array
	 */
	public $default_arguments = array(
		'show_in_rest' => true,
	);

	/**
	 * Main registration method
	 *
	 * @param  array $custom_post_types Numeric array, Associative array, Multidimensional array.
	 * @return array
	 */
	public function register( $custom_post_types ) {
		$results = array();

		if ( empty( $custom_post_types ) || ! is_array( $custom_post_types ) ) {
			return $results;
		}

		$default_args = apply_filters( 'mlwp_bulk_register_custom_post_types_default_args', $this->default_arguments, $custom_post_types );

		foreach ( $custom_post_types as $key => $value ) {
			$args = $default_args;
			$post_type_key = self::prepare_key( $key, $value );
			$args['label'] = self::prepare_label_from_key( $post_type_key );
			$args = self::parse_args( self::maybe_prepare_extra_args( $value ), $args );

			$results[] = register_post_type(
				apply_filters( 'mlwp_bulk_register_custom_post_types_key', $post_type_key, $key, $value ),
				apply_filters( 'mlwp_bulk_register_custom_post_types_args', $args, $key, $value )
			);
		}

		return $results;
	}
}
