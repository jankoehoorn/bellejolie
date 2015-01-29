<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class WebshopPackage extends Package {
		protected $pkgHandle = 'webshop';
		protected $appVersionRequired = '5.4.0';
		protected $pkgVersion = '1.008';

		public function getPackageDescription ( ) {
			return t ( 'Webshop Rocco van Claudia Hendrix' );
		}

		public function getPackageName ( ) {
			return t ( 'Webshop' );
		}

		public function install ( ) {
			$pkg = parent :: install ( );
			$this -> buildPackage ( $pkg );
		}

		public function upgrade ( ) {
			$pkg = $this;
			parent :: upgrade ( );
			$this -> buildPackage ( $pkg );
		}

		public function buildPackage ( $pkg ) {
			$this -> installPageTypes ( $pkg );
			$this -> installSinglePages ( $pkg );
		}

		public function installPageTypes ( $pkg ) {
			Loader :: model ( 'collection_types' );

			$pagetypes = array (
				'webshop_cart' => 'Webshop Cart',
				'webshop_customer' => 'Webshop Customer',
				'webshop_detail' => 'Webshop Detail',
				'webshop_home' => 'Webshop Home',
				'webshop_order' => 'Webshop Order',
				'webshop_overview' => 'Webshop Overview',
				'webshop_payment' => 'Webshop Payment',
				'webshop_text' => 'Webshop Text',
			);

			foreach ( $pagetypes as $handle => $name ) {
				if ( !is_object ( CollectionType :: getByHandle ( $handle ) ) ) {
					CollectionType :: add ( array (
						'ctHandle' => $handle,
						'ctName' => t ( $name )
					), $pkg );
				}
			}
		}

		public function installSinglePages ( $pkg ) {
			Loader :: model ( 'single_page' );

			// Install single pages
			$def = SinglePage :: add ( '/dashboard/webshop', $pkg );
			if ( is_object ( $def ) ) {
				$def -> update ( array (
					'cName' => 'Webshop',
					'cDescription' => t ( 'Rocco Webshop van Claudia Hendrix' )
				) );
			}

			$def = SinglePage :: add ( '/dashboard/webshop/voorraadbeheer', $pkg );
			if ( is_object ( $def ) ) {
				$def -> update ( array (
					'cName' => 'Voorraadbeheer',
					'cDescription' => t ( 'Voorraadbeheer van Rocco Webshop van Claudia Hendrix' )
				) );
			}

			$def = SinglePage :: add ( '/dashboard/webshop/codebeheer', $pkg );
			if ( is_object ( $def ) ) {
				$def -> update ( array (
					'cName' => 'Codebeheer',
					'cDescription' => t ( 'Codebeheer van Rocco Webshop van Claudia Hendrix' )
				) );
			}

			$def = SinglePage :: add ( '/dashboard/webshop/kortingcodebeheer', $pkg );
			if ( is_object ( $def ) ) {
				$def -> update ( array (
					'cName' => 'Kortingcodebeheer',
					'cDescription' => t ( 'Kortingcodebeheer van Rocco Webshop van Claudia Hendrix' )
				) );
			}

			$def = SinglePage :: add ( '/dashboard/webshop/voorkeuren', $pkg );
			if ( is_object ( $def ) ) {
				$def -> update ( array (
					'cName' => 'Voorkeuren',
					'cDescription' => t ( 'Voorkeuren van Rocco Webshop van Claudia Hendrix' )
				) );
			}
		}

	}
?>