( function( $ ) {
	const btns = document.querySelectorAll( '.product__qnt-buttons .btn-qnty' );

	let initialValue = 0;

	if ( btns ) {
		btns.forEach( function( item ) {
			item.addEventListener( 'click', function() {
				$( '.woocommerce-notices-wrapper' ).html( '' );

				const inputNumber = item.parentNode.querySelector( '.product__qnt' );
				if ( inputNumber ) {
					let inputValue = Number( inputNumber.value );
					initialValue = inputValue;

					if ( item.classList.contains( 'plus-btn' ) ) {
						inputValue = inputValue + 1;
					}
					if ( item.classList.contains( 'minus-btn' ) ) {
						if ( inputValue > 0 ) {
							inputValue = inputValue - 1;
						}
					}
					inputNumber.value = inputValue;
					inputNumber.dispatchEvent( new Event( 'change' ) );
				}
			} );
		} );
	}

	$( '.products__category-list li' ).each( function() {
		if ( $( this ).find( '.children' ).length > 0 ) {
			$( this ).addClass( 'has-children' );
		}
	} );

	$( '.product__qnt' ).on( 'change', function( e ) {
		e.preventDefault();
		const qty = $( this ).val();
		const cartItemKey = $( this ).data( 'product-id' );
		const input = $( this );

		$.ajax( {
			type: 'POST',
			dataType: 'json',
			url: cart_ajax.ajaxurl,
			data: {
				action: 'ainsys_update_cart',
				cart_item_key: cartItemKey,
				qty,
			},
			success( data ) {
				const fragments = data.fragments;
				if ( fragments ) {
					$.each( fragments, function( key, value ) {
						$( key ).replaceWith( value );
					} );
					$( document.body ).trigger( 'wc_fragments_loaded' );
				}
			},
			error( data ) {
				$( '.woocommerce-notices-wrapper' ).html( data.responseText );
				input.val( initialValue );
			},
		} );
	} );
} )( jQuery );
