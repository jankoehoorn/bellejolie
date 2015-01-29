$ ( document ).ready ( function ( ) {
	var voorraadbeheer = $ ( '#voorraadbeheer' ).dataTable ( {
		stateSave : true,
		pageLength : 25,
		fnDrawCallback : function ( ) {
			$ ( '#voorraadbeheer tbody td.edit' ).editable ( '/index.php/tools/packages/webshop/voorraadbeheer.php', {
				callback : function ( value, settings ) {
					/* Redraw the table from the new data on the server */
					// oTable.fnDraw ( );
				}
			} );
		}
	} );

} );
