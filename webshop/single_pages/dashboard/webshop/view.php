<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	echo Loader :: helper ( 'concrete/dashboard' ) -> getDashboardPaneHeaderWrapper ( 'Webshop', false, 'large', true );

	switch ( $this -> controller -> getTask () ) {
		case 'export_schema':
			require_once 'export_schema.php';
			break;

		case 'backup':
			require_once 'backup.php';
			break;

		case 'export':
			require_once 'export.php';
			break;

		case 'detail':
			require_once 'detail_detail.php';
			break;

		case 'detail_overview':
			require_once ('detail_overview.php');
			break;

		case 'category':
			require_once ('category_detail.php');
			break;

		default:
			require_once ('category_overview.php');
			break;
	}

	echo Loader :: helper ( 'concrete/dashboard' ) -> getDashboardPaneFooterWrapper ( );
?>