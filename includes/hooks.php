<?php
/**
 * Hook registration for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

/**
 * Registers WordPress hooks for the plugin.
 *
 * @return void
 */
function register_hooks() {
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_assets' );
	add_action( 'wp_footer', __NAMESPACE__ . '\render_floating_cart' );
}
