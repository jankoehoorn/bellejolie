<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class NieuwsPackage extends Package {

		protected $pkgHandle = 'nieuws';
		protected $appVersionRequired = '5.4.0';
		protected $pkgVersion = '1.0';

		public function getPackageDescription ( ) {
			return t ( 'Nieuws' );
		}

		public function getPackageName ( ) {
			return t ( 'Nieuws' );
		}

		public function install ( ) {
			$pkg = parent :: install ( );
			Loader :: model ( 'single_page' );
			Loader :: model ( 'collection_types' );
			Loader :: model ( 'attribute/categories/collection' );

			// Install single pages
			$def = SinglePage :: add ( '/dashboard/nieuws', $pkg );
			$def -> update ( array (
				'cName' => 'Nieuws',
				'cDescription' => t ( 'Nieuwsberichten op uw website' )
			) );

			// Install Page Types, if necessary
			if ( !is_object ( CollectionType :: getByHandle ( 'nieuws_item' ) ) ) {
				$data1 = array ( );
				$data1[ 'ctHandle' ] = 'nieuws_item';
				$data1[ 'ctName' ] = t ( 'Nieuws Item' );
				$data1[ 'ctIcon' ] = 'template1.png';
				$data1[ 'uID' ] = $this -> installData[ 'USER_SUPER_ID' ];
				$dt1 = CollectionType :: add ( $data1 );
			}

			if ( !is_object ( CollectionType :: getByHandle ( 'nieuws_list' ) ) ) {
				$data2 = array ( );
				$data2[ 'ctHandle' ] = 'nieuws_list';
				$data2[ 'ctName' ] = t ( 'Nieuws List' );
				$data2[ 'ctIcon' ] = 'template1.png';
				$data2[ 'uID' ] = $this -> installData[ 'USER_SUPER_ID' ];
				$dt2 = CollectionType :: add ( $data2 );
			}

		}

		public function update ( ) {
			parent :: update ( );
		}

	}
?>