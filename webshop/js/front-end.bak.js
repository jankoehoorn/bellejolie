$ ( document ).ready ( function ( ) {
	var response = $ ( '#response pre' );
	var details = $ ( 'div.detail' );
	var details_optons = {
		appendTo : 'body',
		helper : 'clone',
		cancel : 'div.thumbnail'
	};
	var cart = $ ( '#cart' );
	var cart_items = $ ( 'div.cart-item' );
	var cart_item_options = {
		appendTo : 'body',
		stop : function ( event, ui ) {
			var data = {
				action : 'cart-remove-detail',
				detail_id : ui.helper.find ( 'p.detail_id' ).text ( )
			};

			$.ajax ( {
				url : '/index.php/tools/packages/webshop/ajax.php',
				data : data,
				dataType : 'text',
				success : function ( response ) {
					ui.helper.remove ( );
				}
			} );
		}
	};

	details.draggable ( details_optons );
	cart_items.draggable ( cart_item_options );

	cart.droppable ( {
		activeClass : 'ui-state-default',
		hoverClass : 'ui-state-hover',
		accept : details,
		drop : function ( event, ui ) {
			var data = {
				action : 'cart-add-detail',
				detail_id : $ ( ui.draggable ).find ( 'p.detail_id' ).text ( )
			};

			$.ajax ( {
				url : '/index.php/tools/packages/webshop/ajax.php',
				data : data,
				dataType : 'text',
				success : function ( response ) {
					cart.html ( response );
					$ ( 'div.cart-item' ).draggable ( cart_item_options );
					$ ( 'div.cart-item.last' ).effect ( 'pulsate', 2 );
				}
			} );
		}
	} );

	$ ( '.detail-img' ).colorbox ( {
		rel : 'detail-img',
		opacity : 0.85,
		transition : 'elastic'
	} );

	$ ( 'table.overview' ).dataTable ( {
		'aaSorting' : [ [ 1, 'asc' ] ],
		'bStateSave' : false,
		'sPaginationType' : 'full_numbers',
		'aLengthMenu' : [ [ 5, 10, 25, -1 ], [ 5, 10, 25, 'show all' ] ]
	} );

	// Keep cart in viewport with an easing effect
	var cart_container = $ ( '#cart-container' );
	var cart_container_offset = cart_container.offset ( );

	$ ( window ).scroll ( function ( ) {
		if ( $ ( window ).scrollTop ( ) > cart_container.offset ( ).top ) {
			cart_container.animate ( {
				'top' : $ ( window ).scrollTop ( ) - cart_container.offset ( ).top + 100
			}, 2000, 'easeOutElastic' );
		}
	} );
	
	alert ('front-end.js');
} );

