<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	Loader :: model ( 'kortingcodebeheer', 'webshop' );

	if ( isset ( $_POST[ 'params' ] ) ) {
		Kortingcodebeheer :: updateStatus ( $_POST[ 'params' ] );
	}
?>