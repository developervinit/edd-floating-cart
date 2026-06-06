( function ( $ ) {
	'use strict';

	function updatePreview( control, url ) {
		var preview = control.find( '.edd-floating-cart-media-preview' );
		var image;

		if ( ! preview.length ) {
			return;
		}

		image = preview.find( 'img' );

		if ( ! url ) {
			image.attr( 'src', '' );
			preview.hide();
			return;
		}

		image.attr( 'src', url );
		preview.show();
	}

	function openMediaFrame( button ) {
		var control = button.closest( '.edd-floating-cart-media-control' );
		var mediaIdField = control.find( '.edd-floating-cart-media-id' );
		var mediaUrlField = control.find( '.edd-floating-cart-media-url' );
		var libraryType = button.data( 'library-type' );
		var frameArgs = {
			multiple: false,
			title: button.text()
		};
		var frame;

		if ( libraryType ) {
			frameArgs.library = {
				type: libraryType
			};
		}

		frame = wp.media( frameArgs );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();

			mediaIdField.val( attachment.id || '' );
			mediaUrlField.val( attachment.url || '' );
			updatePreview( control, attachment.url || '' );
		} );

		frame.open();
	}

	$( function () {
		$( document ).on( 'click', '.edd-floating-cart-media-upload', function ( event ) {
			event.preventDefault();
			openMediaFrame( $( this ) );
		} );

		$( document ).on( 'click', '.edd-floating-cart-media-remove', function ( event ) {
			var control;

			event.preventDefault();
			control = $( this ).closest( '.edd-floating-cart-media-control' );

			control.find( '.edd-floating-cart-media-id' ).val( '' );
			control.find( '.edd-floating-cart-media-url' ).val( '' );
			updatePreview( control, '' );
		} );
	} );
}( jQuery ) );
