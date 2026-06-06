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
	if ( ! can_render_floating_cart() ) {
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
	if ( ! can_render_floating_cart() ) {
		return '';
	}

	$quantity       = get_cart_quantity();
	$checkout_url   = get_checkout_url();
	$show_quantity  = should_show_quantity( $quantity );
	$cart_classes   = get_cart_classes( $quantity );
	$aria_label     = get_cart_aria_label( $quantity );
	$hide_cart      = should_hide_cart_when_empty() && $quantity < 1;
	$icon_markup    = get_icon_markup();

	if ( empty( $checkout_url ) ) {
		return '';
	}

	ob_start();
	?>
	<a
		class="<?php echo esc_attr( $cart_classes ); ?>"
		href="<?php echo esc_url( $checkout_url ); ?>"
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
		aria-hidden="<?php echo $hide_cart ? 'true' : 'false'; ?>"
		<?php if ( $hide_cart ) : ?>
			hidden
		<?php endif; ?>
	>
		<?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
