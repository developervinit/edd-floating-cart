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
	return array(
		'position' => 'bottom-right',
	);
}

/**
 * Placeholder for future settings registration.
 *
 * @return void
 */
function register_settings() {
	// Intentionally left empty until a settings UI is added.
}
