<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class UtilitiesPackage extends Package {
		protected $pkgHandle = 'utilities';
		protected $appVersionRequired = '5.4.0';
		protected $pkgVersion = '1.000';

		public function getPackageName ( ) {
			return t ( 'Utility Classes' );
		}

		public function getPackageDescription ( ) {
			return t ( 'Utility Classes' );
		}

		public function install ( ) {
			$pkg = parent :: install ( );
		}

		public function upgrade ( ) {
			parent :: upgrade ( );
		}

	}
?>