var registry = {};

$ ( document ).ready ( function ( ) {
	var ajax_url = '/index.php/tools/packages/webshop/ajax.php';
	var cart_container = $ ( '#cart-container' );
	var button_order = $ ( '#button-order' );
	var check_discount_code = $ ( '#check-discount-code' );
	var discount_code = $ ( '#discount-code' );
	var discount_code_msg = $ ( '#discount-code-msg' );

	check_discount_code.click ( function ( ) {
		if ( discount_code.val ( ).length == 8 ) {
			$.ajax ( {
				data : {
					action : 'check-discount-code',
					discount_code : discount_code.val ( )
				},
				url : ajax_url,
				type : 'POST',
				dataType : 'html',
				beforeSend : function ( ) {
					discount_code_msg.hide ( 'slow' );
					discount_code_msg.text ( '' );
				},
				success : function ( html ) {
					if ( html == 'redraw-cart' ) {
						$.ajax ( {
							data : {
								action : 'redraw-cart'
							},
							url : ajax_url,
							type : 'POST',
							dataType : 'html',
							success : function ( html ) {
								cart_container.html ( html );
								discount_code.hide ( 'slow' );
								discount_code_msg.hide ( 'slow' );
							}
						} );
					}
					else {
						discount_code_msg.text ( html );
						discount_code_msg.show ( 'slow' );
						discount_code.val ( '' );
					}
				}
			} );
		}
	} );

	$ ( document ).on ( 'click', '#button-order', function ( ) {
		window.location.href = '/persoonlijke-gegevens/';
	} );

	$ ( document ).on ( 'click', '.decrease', function ( ) {
		var button = $ ( this );
		$.ajax ( {
			data : {
				action : 'decrease-qty',
				detail_id : button.attr ( 'id' )
			},
			url : ajax_url,
			type : 'POST',
			dataType : 'html',
			success : function ( html ) {
				cart_container.html ( html );
			}
		} );
	} );

	$ ( document ).on ( 'click', '.increase', function ( ) {
		var button = $ ( this );
		$.ajax ( {
			data : {
				action : 'increase-qty',
				detail_id : button.attr ( 'id' )
			},
			url : ajax_url,
			type : 'POST',
			dataType : 'html',
			success : function ( html ) {
				cart_container.html ( html );
			}
		} );
	} );
} );
