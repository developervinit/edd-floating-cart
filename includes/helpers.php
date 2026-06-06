<?php
/**
 * Shared helper functions for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the plugin version.
 *
 * @return string
 */
function get_version() {
	return EDD_FLOATING_CART_VERSION;
}

/**
 * Determines whether EDD public functions are available.
 *
 * @return bool
 */
function is_edd_available() {
	return function_exists( 'edd_get_cart_quantity' ) && function_exists( 'edd_get_checkout_uri' );
}

/**
 * Determines whether the floating cart should display.
 *
 * @return bool
 */
function should_display_floating_cart() {
	if ( is_admin() || ! is_edd_available() ) {
		return false;
	}

	if ( function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) {
		return false;
	}

	if ( function_exists( 'edd_is_success_page' ) && edd_is_success_page() ) {
		return false;
	}

	/**
	 * Filters whether the floating cart should render on the current request.
	 *
	 * @param bool $should_display Whether the cart should display.
	 */
	return (bool) apply_filters( 'edd_floating_cart_should_display', true );
}

/**
 * Returns the current EDD cart quantity.
 *
 * @return int
 */
function get_cart_quantity() {
	if ( ! is_edd_available() ) {
		return 0;
	}

	return max( 0, (int) edd_get_cart_quantity() );
}

/**
 * Returns the EDD checkout URL.
 *
 * @return string
 */
function get_checkout_url() {
	if ( ! is_edd_available() ) {
		return '';
	}

	$checkout_url = edd_get_checkout_uri();

	/**
	 * Filters the checkout URL used by the floating cart.
	 *
	 * @param string $checkout_url Checkout URL.
	 */
	return apply_filters( 'edd_floating_cart_checkout_url', is_string( $checkout_url ) ? $checkout_url : '' );
}

/**
 * Returns the configured floating cart position.
 *
 * @return string
 */
function get_cart_position() {
	$config            = get_plugin_config();
	$configured_value  = isset( $config['position'] ) ? (string) $config['position'] : '';
	$allowed_positions = get_allowed_positions();

	if ( in_array( $configured_value, $allowed_positions, true ) ) {
		return $configured_value;
	}

	return 'bottom-right';
}

/**
 * Returns the supported floating cart positions.
 *
 * @return string[]
 */
function get_allowed_positions() {
	return array(
		'top-left',
		'top-right',
		'bottom-left',
		'bottom-right',
	);
}

/**
 * Resolves the floating cart position class.
 *
 * @return string
 */
function get_position_class() {
	/**
	 * Filters the resolved position class.
	 *
	 * @param string $position_class Position class.
	 */
	return apply_filters( 'edd_floating_cart_position_class', 'position-' . sanitize_html_class( get_cart_position() ) );
}

/**
 * Returns how an empty cart should be displayed.
 *
 * @return string
 */
function get_empty_cart_display() {
	$config         = get_plugin_config();
	$display_mode   = isset( $config['empty_cart_display'] ) ? (string) $config['empty_cart_display'] : '';
	$allowed_modes  = array( 'icon-only' );

	if ( in_array( $display_mode, $allowed_modes, true ) ) {
		return $display_mode;
	}

	return 'icon-only';
}

/**
 * Determines whether the quantity badge should be visible.
 *
 * @param int $quantity Cart quantity.
 * @return bool
 */
function should_show_quantity( $quantity ) {
	$quantity = max( 0, (int) $quantity );

	if ( 0 === $quantity && 'icon-only' === get_empty_cart_display() ) {
		return false;
	}

	/**
	 * Filters whether the quantity badge should be shown.
	 *
	 * @param bool $show_quantity Whether to show quantity.
	 * @param int  $quantity      Current cart quantity.
	 */
	return (bool) apply_filters( 'edd_floating_cart_show_quantity', $quantity > 0, $quantity );
}

/**
 * Returns the CSS classes for the floating cart link.
 *
 * @return string
 */
function get_cart_classes() {
	$classes = array(
		'edd-floating-cart',
		get_position_class(),
	);

	/**
	 * Filters the cart element CSS classes.
	 *
	 * @param string[] $classes Cart CSS classes.
	 */
	$classes = apply_filters( 'edd_floating_cart_classes', $classes );

	return implode( ' ', array_map( 'sanitize_html_class', array_filter( $classes ) ) );
}

/**
 * Returns the accessible label for the floating cart link.
 *
 * @param int $quantity Cart quantity.
 * @return string
 */
function get_cart_aria_label( $quantity ) {
	$quantity = max( 0, (int) $quantity );

	if ( $quantity < 1 ) {
		return __( 'View cart and proceed to checkout', 'edd-floating-cart' );
	}

	return sprintf(
		/* translators: %d: cart quantity */
		_n( 'View cart with %d item and proceed to checkout', 'View cart with %d items and proceed to checkout', $quantity, 'edd-floating-cart' ),
		$quantity
	);
}
