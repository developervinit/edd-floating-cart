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
	if ( is_admin() || ! can_render_floating_cart() ) {
		return;
	}

	wp_enqueue_style(
		'edd-floating-cart',
		EDD_FLOATING_CART_URL . 'assets/css/floating-cart.css',
		array(),
		get_version()
	);

	wp_add_inline_style(
		'edd-floating-cart',
		sprintf(
			'.edd-floating-cart{--edd-floating-cart-offset-x:%1$dpx;--edd-floating-cart-offset-y:%2$dpx;}',
			get_horizontal_offset(),
			get_vertical_offset()
		)
	);

	wp_enqueue_script(
		'edd-floating-cart',
		EDD_FLOATING_CART_URL . 'assets/js/floating-cart.js',
		array( 'jquery' ),
		get_version(),
		true
	);

	wp_localize_script(
		'edd-floating-cart',
		'eddFloatingCart',
		array(
			'ariaLabelEmpty'   => __( 'View cart and proceed to checkout', 'edd-floating-cart' ),
			'ariaLabelSingle'  => __( 'View cart with 1 item and proceed to checkout', 'edd-floating-cart' ),
			'ariaLabelPlural'  => __( 'View cart with %d items and proceed to checkout', 'edd-floating-cart' ),
			'hideWhenEmpty'    => should_hide_cart_when_empty(),
		)
	);
}
