<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class CustomerPackage extends Package {
		protected $pkgHandle = 'customer';
		protected $appVersionRequired = '5.4.0';
		protected $pkgVersion = '1.000';

		public function getPackageName ( ) {
			return t ( 'Customer' );
		}

		public function getPackageDescription ( ) {
			return t ( 'Customer Package per 2014-10-05' );
		}

		public function install ( ) {
			$pkg = parent :: install ( );
		}

		public function upgrade ( ) {
			parent :: upgrade ( );
		}

	}
?>