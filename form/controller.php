<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class FormPackage extends Package {
		protected $pkgHandle = 'form';
		protected $appVersionRequired = '5.4.0';
		protected $pkgVersion = '1.000';

		public function getPackageName ( ) {
			return t ( 'Form' );
		}

		public function getPackageDescription ( ) {
			return t ( 'Form Package per 2014-10-05' );
		}

		public function install ( ) {
			$pkg = parent :: install ( );
		}

		public function upgrade ( ) {
			parent :: upgrade ( );
		}

	}
?>