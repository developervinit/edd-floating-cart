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

	return true;
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

	return is_string( $checkout_url ) ? $checkout_url : '';
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
	return 'position-' . sanitize_html_class( get_cart_position() );
}
