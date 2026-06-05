<?php
/**
 * Plugin Name: EDD Floating Cart
 * Plugin URI:  https://example.com/
 * Description: Standalone foundation for an Easy Digital Downloads floating cart plugin.
 * Version:     0.1.0
 * Author:      DID Creatives
 * Author URI:  https://example.com/
 * Text Domain: edd-floating-cart
 * Domain Path: /languages
 *
 * @package EDDFloatingCart
 */

defined( 'ABSPATH' ) || exit;

define( 'EDD_FLOATING_CART_VERSION', '0.1.0' );
define( 'EDD_FLOATING_CART_FILE', __FILE__ );
define( 'EDD_FLOATING_CART_PATH', plugin_dir_path( __FILE__ ) );
define( 'EDD_FLOATING_CART_URL', plugin_dir_url( __FILE__ ) );

require_once EDD_FLOATING_CART_PATH . 'includes/bootstrap.php';

EDDFloatingCart\bootstrap();
