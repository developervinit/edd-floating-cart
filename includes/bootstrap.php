<?php
/**
 * Bootstrap loader for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

require_once EDD_FLOATING_CART_PATH . 'includes/helpers.php';
require_once EDD_FLOATING_CART_PATH . 'includes/hooks.php';
require_once EDD_FLOATING_CART_PATH . 'includes/renderer.php';
require_once EDD_FLOATING_CART_PATH . 'includes/assets.php';
require_once EDD_FLOATING_CART_PATH . 'includes/settings.php';

/**
 * Boots the plugin modules.
 *
 * @return void
 */
function bootstrap() {
	register_hooks();
}
