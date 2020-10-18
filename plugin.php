<?php
/**
 * Plugin Name: ML Custom Post Types
 * Description:
 * Plugin URI: https://github.com/martinsluters
 * Author: Martins Luters
 * Author URI: https://github.com/martinsluters
 * Version: 0.1
 * License: GPL2
 */

/*
	Copyright (C) 2020  Martins Luters https://github.com/martinsluters

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require dirname( __FILE__ ) . '/vendor/autoload.php';
}


if ( ! function_exists( 'mlwp_bulk_register_custom_post_types' ) ) {

	/**
	 * API function to bulk register custom post types
	 *
	 * @param  array $post_types Array containing post type arguments.
	 * @return array
	 */
	function mlwp_bulk_register_custom_post_types( $post_types ) {
		$instance = new \MLWP\BulkRegisterPlugin\BulkRegisterCustomPostTypes();
		return $instance->register( $post_types );
	}
}

/*add_action( 'init', 'custom_post_type_register' );
function custom_post_type_register() {
	if ( function_exists( 'mlwp_bulk_register_custom_post_types' ) ) {
		mlwp_bulk_register_custom_post_types( array( 'book', 'house', 'animal' ) );
	}
}*/
