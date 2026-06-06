<?php
/**
 * Settings module for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

/**
 * Returns the plugin configuration defaults.
 *
 * This central location is intentionally simple so it can be replaced by
 * database-backed settings later without changing rendering code.
 *
 * @return array<string, string>
 */
function get_plugin_config() {
	$config = array(
		'position' => 'bottom-right',
		'empty_cart_display' => 'icon-only',
	);

	/**
	 * Filters the base plugin configuration.
	 *
	 * This keeps the current implementation simple while leaving a clean
	 * extension point for future settings storage.
	 *
	 * @param array<string, string> $config Plugin configuration.
	 */
	return apply_filters( 'edd_floating_cart_config', $config );
}

/**
 * Placeholder for future settings registration.
 *
 * @return void
 */
function register_settings() {
	// Intentionally left empty until a settings UI is added.
}
