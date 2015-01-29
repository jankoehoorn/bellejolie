$ ( document ).ready ( function ( ) {

	var button_add_to_cart = $ ( '#add_to_cart' );
	var ajax_url = '/index.php/tools/packages/webshop/ajax.php';
	var cart = $ ( '#cart' );

	button_add_to_cart.click ( function ( ) {
		var data = {
			action : 'cart-add-detail',
			detail_id : button_add_to_cart.attr ( 'detail_id' )
		};

		$.ajax ( {
			url : ajax_url,
			data : data,
			type : 'GET',
			dataType : 'html',
			success : function ( html ) {
				button_add_to_cart.effect ( 'transfer', {
					to : cart
				}, 500 );
				cart.html ( html );
			}
		} );

	} );

	$ ( '#category-carousel' ).bxSlider ( {
		pager : false,
		controls : false,
		slideWidth : 140,
		minSlides : 7,
		maxSlides : 7,
		slideMargin : 10,
		auto : true,
		autoHover : true
	} );
} );
