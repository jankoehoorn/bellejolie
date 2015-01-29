<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	/***
	 *	Set up the pane header
	 *	@param string $title Page Title
	 *	@param string $help Help text
	 *	@param string $span css class to use
	 *	@param boolean $includeDefaultBody use the standard divs for page layout
	 *	@param array $navigatePages array of collection objects for the context menu
	 *	@param Collection $upToPage back to main page collection object
	 */
	echo Loader :: helper ( 'concrete/dashboard' ) -> getDashboardPaneHeaderWrapper ( $pane_title, $pane_help, $pane_css_class, $pane_standard_body, $pane_contextmenu_pages, $pane_up_to_page );

	switch ( $this -> controller -> getTask () ) {
		case 'add':
			require_once ('add.php');
			break;

		case 'update':
			require_once ('update.php');
			break;

		default:
			require_once ('list.php');
			break;
	}

	if ( isset ( $debug ) ) {
		echo '<textarea class="debug">';

		if ( $vardump === true ) {
			var_dump ( $debug );
		}
		else {
			print_r ( $debug );
		}

		echo '</textarea>';
	}

	echo Loader :: helper ( 'concrete/dashboard' ) -> getDashboardPaneFooterWrapper ( );
?>