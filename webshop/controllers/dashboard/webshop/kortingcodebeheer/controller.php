<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	require_once ($_SERVER[ 'DOCUMENT_ROOT' ] . '/kint/Kint.class.php');

	class DashboardWebshopKortingCodeBeheerController extends DashboardBaseController {

		public $helpers = array (
			'html',
			'form'
		);

		public function on_start ( ) {
			$this -> error = Loader :: helper ( 'validation/error' );
			Loader :: model ( 'webshop', 'webshop' );
			Loader :: model ( 'kortingcodebeheer', 'webshop' );

			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/dashboard.css' ) );
			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/jquery.dataTables.min.css' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.dataTables.min.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.jeditable.mini.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/kortingcodebeheer.js' ) );
		}

		public function on_before_render ( ) {
			$this -> set ( 'error', $this -> error );

			if ( !empty ( $_SESSION[ 'message' ] ) ) {
				$this -> set ( 'message', $_SESSION[ 'message' ] );
				unset ( $_SESSION[ 'message' ] );
			}
		}

		public function view ( ) {
			if ( $this -> isPost ( ) ) {
				$korting = $this -> post ( 'korting' );
				$num_codes = $this -> post ( 'num_codes' );
				$delete = $this -> post ( 'delete' );

				if ( !empty ( $delete ) ) {
					$db = Loader :: db ( );
					$sql = "
						DELETE FROM webshop_kortingcodes
						WHERE kortingcode_id IN (" . implode ( ', ', $delete ) . ")
					";
					$db -> execute ( $sql );
				}

				if ( !empty ( $korting ) && !empty ( $num_codes ) ) {
					KortingCodebeheer :: generateRandomCodes ( $korting, $num_codes );
				}
				else {
					$this -> set ( 'message', 'Vul alle velden in' );
				}
			}

			$this -> set ( 'kortingcodes', KortingCodebeheer :: getAll ( ) );
			//@formatter:off
			$this -> set ( 'statussen', array (
				'created'     => 'created',
				'distributed' => 'distributed',
				'cashed'      => 'cashed',
			) );
			//@formatter:on
		}

	}
?>