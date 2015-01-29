<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	require_once ($_SERVER[ 'DOCUMENT_ROOT' ] . '/kint/Kint.class.php');

	Use Webshop\Preferences;
	Use Webshop\PreferencesValidator;
	Use Webshop\IoC;
	Use Utilities\Request;

	Loader :: model ( 'webshop', 'webshop' );
	Loader :: model ( 'ioc', 'webshop' );
	Loader :: model ( 'preferences', 'webshop' );
	Loader :: model ( 'utilities', 'utilities' );

	Class DashboardWebshopVoorkeurenController Extends DashboardBaseController {

		public $helpers = array (
			'html',
			'form'
		);

		public function on_start ( ) {
			$this -> error = Loader :: helper ( 'validation/error' );

			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/dashboard.css' ) );
			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/jquery.dataTables.min.css' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.dataTables.min.js' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/jquery.jeditable.mini.js' ) );

			$this -> buildIoC ( );
		}

		public function on_before_render ( ) {
			$this -> set ( 'error', $this -> error );

			if ( !empty ( $_SESSION[ 'message' ] ) ) {
				$this -> set ( 'message', $_SESSION[ 'message' ] );
				unset ( $_SESSION[ 'message' ] );
			}
		}

		public function view ( ) {
			$preferences = IoC :: make ( 'Preferences' );

			if ( Request :: isPost ( ) ) {
				$preferences -> populate ( Request :: postvars ( ) );

				if ( $preferences -> validate ( ) ) {
					$preferences -> save ( );
					$this -> set ( 'message', 'Voorkeuren opgeslagen' );
				}
				else {
					$this -> error -> add ( 'Alle velden zijn verplicht. Gebruik bij verzendkosten geen komma, maar een punt)' );
				}
			}

			$this -> set ( 'Preferences', $preferences );
		}

		public function buildIoC ( ) {
			IoC :: bind ( 'Preferences', function ( ) {
				$Preferences = new Preferences;
				$Preferences -> load ( 'preference_id = ?', array ( 1 ) );
				$Preferences -> setValidator ( IoC :: make ( 'PreferencesValidator' ) );
				return $Preferences;
			} );

			IoC :: bind ( 'PreferencesValidator', function ( ) {
				$PreferencesValidator = new PreferencesValidator;
				return $PreferencesValidator;
			} );
		}

	}
?>