<?php
namespace  MLWP\BulkRegisterPlugin;

/**
 * Implementation of registering taxonomies for object types
 */
class BulkRegisterTaxonomiesForObjectTypes extends BulkRegister {

	/**
	 * Main registration method
	 *
	 * @param  array $tax_object_pairs
	 * @return array
	 */
	public function register( $tax_object_pairs ) {
		$results = array();

		if ( empty( $tax_object_pairs ) || ! is_array( $tax_object_pairs ) ) {
			return $results;
		}

		$tax_object_pairs = apply_filters( 'mlwp_bulk_register_taxonomies_for_object_types', $tax_object_pairs );

		foreach ( $tax_object_pairs as $tax_key => $obj_key ) {
			$results[] = register_taxonomy_for_object_type( $tax_key, $obj_key );
		}

		return $results;
	}
}
