$ ( document ).ready ( function ( ) {
	var kortingcodebeheer = $ ( '#kortingcodebeheer' ).dataTable ( {
		stateSave : true,
		pageLength : 25
	} );

	var ajax_url = CCM_REL + '/index.php/tools/packages/webshop/kortingcodebeheer.php';

	$ ( document ).on ( 'change', 'table#kortingcodebeheer td select', function ( ) {
		var select = $ ( this );
		var data = {
			params : {
				kortingcode_id : select.attr ( 'kortingcode_id' ),
				status : select.val ( )
			}
		};

		$.ajax ( {
			url : ajax_url,
			data : data,
			type : 'POST'
		} );

	} );
} );
