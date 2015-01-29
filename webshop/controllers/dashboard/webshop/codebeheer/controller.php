<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	require_once ($_SERVER[ 'DOCUMENT_ROOT' ] . '/kint/Kint.class.php');

	class DashboardWebshopCodeBeheerController extends DashboardBaseController {

		public $helpers = array (
			'html',
			'form'
		);

		public function on_start ( ) {
			$this -> error = Loader :: helper ( 'validation/error' );
			Loader :: model ( 'webshop', 'webshop' );
			Loader :: model ( 'codebeheer', 'webshop' );

			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/dashboard.css' ) );
			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/jquery.dataTables.min.css' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.dataTables.min.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.jeditable.mini.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/codebeheer.js' ) );
		}

		public function on_before_render ( ) {
			$this -> set ( 'error', $this -> error );

			if ( !empty ( $_SESSION[ 'message' ] ) ) {
				$this -> set ( 'message', $_SESSION[ 'message' ] );
				unset ( $_SESSION[ 'message' ] );
			}
		}

		public function view ( ) {
			$CodebeheerFacade = new CodebeheerFacade;

			$this -> set ( 'CodebeheerFacade', $CodebeheerFacade );
		}

	}
?>