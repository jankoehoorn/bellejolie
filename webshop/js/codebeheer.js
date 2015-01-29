
	$ ( document ).ready ( function ( ) {
		var voorraadbeheer = $ ( '#codebeheer' ).dataTable ( {
			stateSave : true,
			pageLength : 25
		} );
	
		var ajax_url = CCM_REL + '/index.php/tools/packages/webshop/codebeheer.php';
	
		$ ( document ).on ( 'change', 'table#codebeheer td select', function ( ) {
			var data = {
				params : {
					voorraad_id : $ ( this ).attr ( 'id' ),
					code_id : $ ( this ).val ( )
				}
			};
	
			$.ajax ( {
				url : ajax_url,
				data : data,
				type : 'POST'
			} );
	
		} );
	} );