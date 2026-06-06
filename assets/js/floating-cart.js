( function ( $ ) {
	'use strict';

	var EVENT_NAMESPACE = '.eddFloatingCart';

	function getCart() {
		return document.querySelector( '.edd-floating-cart' );
	}

	function getAriaLabel( quantity ) {
		if ( quantity < 1 ) {
			return eddFloatingCart.ariaLabelEmpty;
		}

		if ( quantity === 1 ) {
			return eddFloatingCart.ariaLabelSingle;
		}

		return eddFloatingCart.ariaLabelPlural.replace( '%d', quantity );
	}

	function updateQuantity( quantity ) {
		var cart = getCart();
		var quantityElement;
		var parsedQuantity;
		var isEmpty;
		var shouldHide;

		if ( ! cart ) {
			return;
		}

		quantityElement = cart.querySelector( '.edd-floating-cart__quantity' );

		if ( ! quantityElement ) {
			return;
		}

		parsedQuantity = parseInt( quantity, 10 );
		isEmpty = isNaN( parsedQuantity ) || parsedQuantity < 1;
		shouldHide = !! eddFloatingCart.hideWhenEmpty && isEmpty;

		if ( isEmpty ) {
			if ( shouldHide && cart.hidden && quantityElement.hidden && quantityElement.getAttribute( 'aria-hidden' ) === 'true' ) {
				return;
			}

			cart.hidden = shouldHide;
			cart.setAttribute( 'aria-hidden', shouldHide ? 'true' : 'false' );
			quantityElement.textContent = '0';
			quantityElement.hidden = true;
			quantityElement.setAttribute( 'aria-hidden', 'true' );
			cart.setAttribute( 'aria-label', getAriaLabel( 0 ) );
			return;
		}

		if ( cart.hidden ) {
			cart.hidden = false;
		}

		if ( cart.getAttribute( 'aria-hidden' ) !== 'false' ) {
			cart.setAttribute( 'aria-hidden', 'false' );
		}

		if ( quantityElement.textContent !== String( parsedQuantity ) ) {
			quantityElement.textContent = String( parsedQuantity );
		}

		if ( quantityElement.hidden ) {
			quantityElement.hidden = false;
		}

		if ( quantityElement.getAttribute( 'aria-hidden' ) !== 'false' ) {
			quantityElement.setAttribute( 'aria-hidden', 'false' );
		}

		cart.setAttribute( 'aria-label', getAriaLabel( parsedQuantity ) );
	}

	$( document.body ).off( EVENT_NAMESPACE );

	$( document.body ).on( 'edd_quantity_updated' + EVENT_NAMESPACE, function ( event, quantity ) {
		updateQuantity( quantity );
	} );

	$( document.body ).on( 'edd_cart_item_added' + EVENT_NAMESPACE + ' edd_cart_item_removed' + EVENT_NAMESPACE, function ( event, response ) {
		if ( response && typeof response.cart_quantity !== 'undefined' ) {
			updateQuantity( response.cart_quantity );
		}
	} );
}( jQuery ) );
