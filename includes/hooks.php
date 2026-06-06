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
	register_settings();

	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\register_assets' );
	add_action( 'wp_footer', __NAMESPACE__ . '\render_floating_cart' );
	add_action( 'admin_notices', __NAMESPACE__ . '\maybe_render_admin_notice' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_settings_assets' );
}

/**
 * Renders an admin notice when EDD is not available.
 *
 * @return void
 */
function maybe_render_admin_notice() {
	if ( is_edd_available() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	?>
	<div class="notice notice-warning">
		<p>
			<?php esc_html_e( 'EDD Floating Cart requires Easy Digital Downloads to be active before it can render on the frontend.', 'edd-floating-cart' ); ?>
		</p>
	</div>
	<?php
}
