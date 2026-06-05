<?php
/**
 * Asset-related helpers for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

/**
 * Registers plugin frontend assets.
 *
 * @return void
 */
function register_assets() {
	if ( is_admin() || ! should_display_floating_cart() ) {
		return;
	}

	wp_enqueue_style(
		'edd-floating-cart',
		EDD_FLOATING_CART_URL . 'assets/css/floating-cart.css',
		array(),
		get_version()
	);

	wp_enqueue_script(
		'edd-floating-cart',
		EDD_FLOATING_CART_URL . 'assets/js/floating-cart.js',
		array( 'jquery' ),
		get_version(),
		true
	);
}
