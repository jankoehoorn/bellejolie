<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	Loader :: model ( 'codebeheer', 'webshop' );

	if ( isset ( $_POST[ 'params' ] ) ) {
		Codebeheer :: updateCode ( $_POST[ 'params' ] );
	}
?>