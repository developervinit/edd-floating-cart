<?php
/**
 * Rendering helpers for EDD Floating Cart.
 *
 * @package EDDFloatingCart
 */

namespace EDDFloatingCart;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the floating cart.
 *
 * @return void
 */
function render_floating_cart() {
	if ( ! should_display_floating_cart() ) {
		return;
	}

	echo get_cart_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Returns floating cart markup.
 *
 * @return string
 */
function get_cart_markup() {
	if ( ! should_display_floating_cart() ) {
		return '';
	}

	$quantity     = get_cart_quantity();
	$checkout_url = get_checkout_url();

	if ( empty( $checkout_url ) ) {
		return '';
	}

	ob_start();
	?>
	<a
		class="edd-floating-cart"
		href="<?php echo esc_url( $checkout_url ); ?>"
		aria-label="<?php esc_attr_e( 'View cart and proceed to checkout', 'edd-floating-cart' ); ?>"
	>
		<span class="edd-floating-cart__icon" aria-hidden="true">&#128722;</span>
		<span
			class="edd-floating-cart__quantity"
			<?php if ( $quantity < 1 ) : ?>
				hidden
			<?php endif; ?>
		>
			<?php echo esc_html( $quantity ); ?>
		</span>
	</a>
	<?php

	return (string) ob_get_clean();
}
