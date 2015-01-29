<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	require_once ($_SERVER[ 'DOCUMENT_ROOT' ] . '/kint/Kint.class.php');

	class DashboardWebshopVoorraadbeheerController extends DashboardBaseController {

		public $helpers = array (
			'html',
			'form'
		);

		public function on_start ( ) {
			$this -> error = Loader :: helper ( 'validation/error' );
			Loader :: model ( 'webshop', 'webshop' );
			Loader :: model ( 'voorraadbeheer', 'webshop' );

			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/dashboard.css' ) );
			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/jquery.dataTables.min.css' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.dataTables.min.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.jeditable.mini.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/voorraadbeheer.js' ) );
		}

		public function on_before_render ( ) {
			$this -> set ( 'error', $this -> error );

			if ( !empty ( $_SESSION[ 'message' ] ) ) {
				$this -> set ( 'message', $_SESSION[ 'message' ] );
				unset ( $_SESSION[ 'message' ] );
			}
		}

		public function detail ( $detail_id = false ) {
			if ( empty ( $detail_id ) ) {
				$this -> set ( 'message', '$detail_id is empty' );
			}
		}

		public function view ( ) {
			$VoorraadbeheerFacade = new VoorraadbeheerFacade;
			$this -> set ( 'VoorraadbeheerFacade', $VoorraadbeheerFacade );
		}

		public function order ( ) {
			$VoorraadbeheerFacade = new VoorraadbeheerFacade;
			$this -> set ( 'VoorraadbeheerFacade', $VoorraadbeheerFacade );
		}

	}
?>