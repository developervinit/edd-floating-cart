( function ( $ ) {
	'use strict';

	function getCart() {
		return document.querySelector( '.edd-floating-cart' );
	}

	function updateQuantity( quantity ) {
		var cart = getCart();
		var quantityElement;
		var parsedQuantity;

		if ( ! cart ) {
			return;
		}

		quantityElement = cart.querySelector( '.edd-floating-cart__quantity' );

		if ( ! quantityElement ) {
			return;
		}

		parsedQuantity = parseInt( quantity, 10 );

		if ( isNaN( parsedQuantity ) || parsedQuantity < 1 ) {
			quantityElement.textContent = '0';
			quantityElement.hidden = true;
			return;
		}

		quantityElement.textContent = String( parsedQuantity );
		quantityElement.hidden = false;
	}

	$( document.body ).on( 'edd_quantity_updated', function ( event, quantity ) {
		updateQuantity( quantity );
	} );

	$( document.body ).on( 'edd_cart_item_added edd_cart_item_removed', function ( event, response ) {
		if ( response && typeof response.cart_quantity !== 'undefined' ) {
			updateQuantity( response.cart_quantity );
		}
	} );
}( jQuery ) );
