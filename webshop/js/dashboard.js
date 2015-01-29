$ ( document ).ready ( function ( ) {
	var fixHelper = function ( e, ui ) {
		ui.children ( ).each ( function ( ) {
			$ ( this ).width ( $ ( this ).width ( ) );
		} );
		return ui;
	};

	$ ( 'table.sortable.details tbody' ).sortable ( {
		helper : fixHelper,
		update : function ( event, ui ) {
			$.get ( '/index.php/tools/packages/webshop/ajax.php', {
				action : 'sort-details',
				ids : $ ( this ).sortable ( 'toArray' ).toString ( )
			} );

			$ ( '#msg' ).fadeIn ( 400 ).text ( 'Volgorde is opgeslagen' ).fadeOut ( 800 );
		}
	} ).disableSelection ( );

	$ ( 'table.sortable.categories tbody' ).sortable ( {
		helper : fixHelper,
		update : function ( event, ui ) {
			$.get ( '/index.php/tools/packages/webshop/ajax.php', {
				action : 'sort-categories',
				ids : $ ( this ).sortable ( 'toArray' ).toString ( )
			} );

			$ ( '#msg' ).fadeIn ( 400 ).text ( 'Volgorde is opgeslagen' ).fadeOut ( 800 );
		}
	} ).disableSelection ( );

	$ ( 'a.delete.category' ).click ( function ( e ) {
		if ( !confirm ( 'Delete Category. Are you sure?' ) ) {
			e.preventDefault ( );
		}
	} );

	$ ( 'a.delete.detail' ).click ( function ( e ) {
		if ( !confirm ( 'Delete Detail. Are you sure?' ) ) {
			e.preventDefault ( );
		}
	} );

} );
