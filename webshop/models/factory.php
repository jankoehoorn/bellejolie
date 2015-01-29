<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Use Utilities\Registry;

	Class WebshopFactory {
		public static function createWebshop ( ) {

		}

		public static function createPrinter ( Registry $Registry ) {
			$WebshopPrinter = new WebshopPrinter;

			$WebshopPrinter -> setRegistry ( $Registry );
			$WebshopPrinter -> setDb ( Loader :: db ( ) );
			$WebshopPrinter -> setStringHelper ( Loader :: helper ( 'string' ) );
			$WebshopPrinter -> setImageHelper ( Loader :: helper ( 'image' ) );

			return $WebshopPrinter;
		}
	}
?>