/***
 * nieuws.js
 * JavaScript for nieuws package
 */

$ ( document ).ready ( function ( ) {
	$ ( 'a.delete' ).click ( function ( e ) {
		if ( !confirm ( 'Nieuws-item verwijderen. Weet u het zeker?' ) ) {
			e.preventDefault ( );
		}
	} );
} );
