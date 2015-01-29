$ ( document ).ready ( function ( ) {
	var form_errors = $ ( 'form .err' );

	if ( form_errors.length > 0 ) {
		form_errors.get ( 0 ).focus ( );
	}
} );
