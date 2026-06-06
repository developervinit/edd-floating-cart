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
	if ( ! can_render_floating_cart() ) {
		return false;
	}

	if ( should_hide_cart_when_empty() && get_cart_quantity() < 1 ) {
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
 * Determines whether the floating cart shell can render on this request.
 *
 * @return bool
 */
function can_render_floating_cart() {
	if ( is_admin() || ! is_edd_available() || ! is_floating_cart_enabled() ) {
		return false;
	}

	if ( ! should_display_on_all_pages() ) {
		return false;
	}

	if ( should_hide_on_checkout() && function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) {
		return false;
	}

	if ( should_hide_on_success_page() && function_exists( 'edd_is_success_page' ) && edd_is_success_page() ) {
		return false;
	}

	/**
	 * Filters whether the floating cart shell can render.
	 *
	 * @param bool $can_render Whether the shell can render.
	 */
	return (bool) apply_filters( 'edd_floating_cart_can_render', true );
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
	$configured_value  = (string) get_plugin_setting( 'position' );
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
	$display_mode   = (string) get_plugin_setting( 'empty_cart_display' );

	if ( in_array( $display_mode, array( 'icon-only', 'hide-cart' ), true ) ) {
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
 * @param int|null $quantity Optional cart quantity.
 * @return string
 */
function get_cart_classes( $quantity = null ) {
	$quantity = null === $quantity ? get_cart_quantity() : max( 0, (int) $quantity );
	$classes = array(
		'edd-floating-cart',
		get_position_class(),
	);

	if ( should_hide_cart_when_empty() && $quantity < 1 ) {
		$classes[] = 'is-hidden-when-empty';
	}

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

/**
 * Returns whether the floating cart is enabled.
 *
 * @return bool
 */
function is_floating_cart_enabled() {
	return ! empty( get_plugin_setting( 'enabled' ) );
}

/**
 * Returns whether the cart should display on general frontend pages.
 *
 * @return bool
 */
function should_display_on_all_pages() {
	return ! empty( get_plugin_setting( 'display_on_all_pages' ) );
}

/**
 * Returns whether checkout should be hidden.
 *
 * @return bool
 */
function should_hide_on_checkout() {
	return ! empty( get_plugin_setting( 'hide_checkout' ) );
}

/**
 * Returns whether the success page should be hidden.
 *
 * @return bool
 */
function should_hide_on_success_page() {
	return ! empty( get_plugin_setting( 'hide_success' ) );
}

/**
 * Returns whether the floating cart should hide completely when empty.
 *
 * @return bool
 */
function should_hide_cart_when_empty() {
	return 'hide-cart' === get_empty_cart_display();
}

/**
 * Returns the horizontal offset in pixels.
 *
 * @return int
 */
function get_horizontal_offset() {
	return max( 0, absint( get_plugin_setting( 'horizontal_offset' ) ) );
}

/**
 * Returns the vertical offset in pixels.
 *
 * @return int
 */
function get_vertical_offset() {
	return max( 0, absint( get_plugin_setting( 'vertical_offset' ) ) );
}

/**
 * Returns icon configuration data.
 *
 * @return array{type:string,url:string}
 */
function get_icon_config() {
	$icon_type = (string) get_plugin_setting( 'icon_type' );
	$url       = '';

	if ( 'custom-svg' === $icon_type ) {
		$url = (string) get_plugin_setting( 'custom_svg_url' );
	}

	if ( 'custom-image' === $icon_type ) {
		$url = (string) get_plugin_setting( 'custom_image_url' );
	}

	if ( empty( $url ) || ! in_array( $icon_type, array( 'custom-svg', 'custom-image' ), true ) ) {
		return array(
			'type' => 'default',
			'url'  => '',
		);
	}

	return array(
		'type' => $icon_type,
		'url'  => $url,
	);
}

/**
 * Returns the icon markup for the floating cart.
 *
 * @return string
 */
function get_icon_markup() {
	$icon = get_icon_config();

	if ( 'default' === $icon['type'] ) {
		$markup = '<span class="edd-floating-cart__icon" aria-hidden="true">&#128722;</span>';
	} else {
		$markup = sprintf(
			'<img class="edd-floating-cart__icon edd-floating-cart__icon--custom" src="%1$s" alt="" aria-hidden="true" />',
			esc_url( $icon['url'] )
		);
	}

	/**
	 * Filters the icon markup used by the floating cart.
	 *
	 * @param string               $markup Icon markup.
	 * @param array{type:string,url:string} $icon   Icon configuration.
	 */
	return apply_filters( 'edd_floating_cart_icon_markup', $markup, $icon );
}
