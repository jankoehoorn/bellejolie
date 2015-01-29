<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	echo Loader :: helper ( 'concrete/dashboard' ) -> getDashboardPaneHeaderWrapper ( 'Webshop', false, 'large', true );

	switch ( $this -> controller -> getTask () ) {
		case 'detail':
			require_once ('detail.php');
			break;

		case 'order':
			require_once ('order.php');
			break;

		default:
			require_once ('overview.php');
			break;
	}

	echo Loader :: helper ( 'concrete/dashboard' ) -> getDashboardPaneFooterWrapper ( );
?>