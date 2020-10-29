<?php
namespace  MLWP\BulkRegisterPlugin;

/**
 * Implementation of registering custom taxonomy
 */
class BulkRegisterCustomTaxonomies extends BulkRegister {

	/**
	 * Default custom taxonomy arguments
	 *
	 * @var array
	 */
	public $default_arguments = array(
		'show_in_rest' => true,
	);

	/**
	 * Main registration method
	 *
	 * @param  array $custom_taxonomies Numeric array, Associative array, Multidimensional array.
	 * @return array
	 */
	public function register( $custom_taxonomies ) {
		$results = array();

		if ( empty( $custom_taxonomies ) || ! is_array( $custom_taxonomies ) ) {
			return $results;
		}

		$defaults = apply_filters( 'mlwp_bulk_register_custom_taxonomies_default_args', $this->default_arguments, $custom_taxonomies );

		foreach ( $custom_taxonomies as $key => $value ) {
			$args = $defaults;
			$taxonomy_key = self::prepare_key( $key, $value );
			$extra_args = self::maybe_extra_args( $value );
			$object_type = self::maybe_prepare_object_types( $extra_args );
			$args['label'] = self::prepare_label_from_key( $taxonomy_key );
			$args = self::parse_args( $extra_args, $args );

			$results[] = register_taxonomy(
				apply_filters( 'mlwp_bulk_register_custom_taxonomies_key', $taxonomy_key, $key, $value ),
				apply_filters( 'mlwp_bulk_register_custom_taxonomies_object_type', $object_type, $key, $value ),
				apply_filters( 'mlwp_bulk_register_custom_taxonomies_args', $args, $key, $value )
			);
		}

		return $results;
	}

	/**
	 * If args contains 'object_type' element containing objet key(s) pass back the single
	 * object type or array of object types with which the taxonomy should be associated.
	 * Otherwise null.
	 *
	 * @param  mix $extra_args Expecting array or anything else.
	 * @return array|null The array of object types. Null if not array or not set.
	 */
	public static function maybe_prepare_object_types( $extra_args ) {
		$object_type = null;
		if ( is_array( $extra_args ) ) {
			if ( isset( $extra_args['object_type'] ) ) {
				if ( is_array( $extra_args['object_type'] ) ) {
					$object_type = $extra_args['object_type'];
				} else {
					$object_type = (string) $extra_args['object_type'];
				}
			}
		}
		return $object_type;
	}
}
