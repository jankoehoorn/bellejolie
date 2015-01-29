
	$ ( document ).ready ( function ( ) {
		var wrapper_bezorgadres = $ ( '#wrapper_bezorgadres' );
		var two_addresses = $ ( '#two_addresses' );
	
		if ( two_addresses.prop ( 'checked' ) ) {
			wrapper_bezorgadres.fadeIn ( 1000 );
		}
	
		two_addresses.click ( function ( ) {
			if ( two_addresses.prop ( 'checked' ) ) {
				wrapper_bezorgadres.fadeIn ( 1000 );
			}
			else {
				wrapper_bezorgadres.fadeOut ( 1000 );
			}
		} );
	} );