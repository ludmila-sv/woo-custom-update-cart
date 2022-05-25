( function( $ ) {
	const btns = document.querySelectorAll( '.product__qnt-buttons .btn-qnty' );
	if ( btns ) {
		btns.forEach( function( item ) {
			item.addEventListener( 'click', function () {
				const inputNumber = item.parentNode.querySelector( '.product__qnt' );
				if ( inputNumber ) {
					let inputValue = Number( inputNumber.value );
					if ( item.classList.contains( 'plus-btn' ) ) {
						inputValue = inputValue + 1;
					}
					if ( item.classList.contains( 'minus-btn' ) ) {
						if ( inputValue >> 0 ) {
							inputValue = inputValue - 1;
						}
					}
					inputNumber.value = inputValue;
					inputNumber.dispatchEvent( new Event( 'change' ) );
				}
			} );
		} );
	}

	$( '.product__qnt' ).on( 'change', function( e ) {
		e.preventDefault();
		const qty = $( this ).val();
		const cartItemKey = $( this ).data( 'product-id' );
	
		$.ajax( {
			type: 'POST',
			dataType: 'json',
			url: cart_ajax.ajaxurl,
			data: {
				action: 'ainsys_update_cart',
				cart_item_key: cartItemKey,
				qty: qty,
			},
			success( data ) {
				const fragments = data.fragments;
				if ( fragments ) {
					$.each( fragments, function( key, value ) {
						$( key ).replaceWith( value );
					} );
					$( document.body ).trigger( 'wc_fragments_loaded' );
				}
				//$(document.body).trigger('wc_fragments_refreshed');
			},
		} );
	} );

} )( jQuery );
