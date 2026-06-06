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

	$quantity       = get_cart_quantity();
	$checkout_url   = get_checkout_url();
	$show_quantity  = should_show_quantity( $quantity );
	$cart_classes   = get_cart_classes();
	$aria_label     = get_cart_aria_label( $quantity );

	if ( empty( $checkout_url ) ) {
		return '';
	}

	ob_start();
	?>
	<a
		class="<?php echo esc_attr( $cart_classes ); ?>"
		href="<?php echo esc_url( $checkout_url ); ?>"
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
	>
		<span class="edd-floating-cart__icon" aria-hidden="true">&#128722;</span>
		<span
			class="edd-floating-cart__quantity"
			aria-hidden="<?php echo $show_quantity ? 'false' : 'true'; ?>"
			<?php if ( ! $show_quantity ) : ?>
				hidden
			<?php endif; ?>
		>
			<?php echo esc_html( $quantity ); ?>
		</span>
	</a>
	<?php

	$markup = (string) ob_get_clean();

	/**
	 * Filters the final floating cart markup.
	 *
	 * @param string $markup Cart markup.
	 */
	return apply_filters( 'edd_floating_cart_markup', $markup );
}
