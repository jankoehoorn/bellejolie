<?php
	Namespace Customer;

		Use \DOMDocument;
		Use \Form\FormFactory;
		Use \Form\NotEmptyValidator;
		Use \Form\EmailValidator;
		Use \Form\PostcodeValidator;
		Use \Loader;
		Use \Utilities\Cookie;
		Use \Utilities\Request;

		Class CustomerFactory {
			public static function createCustomer ( ) {
				//@formatter:off
				$Customer                                   = new Customer;
				$Customer -> tpl                            = $_SERVER[ 'DOCUMENT_ROOT' ] . '/packages/webshop/elements/forms/customer.html';

				$Customer -> fields -> factuurvoornaam      = FormFactory :: createField ( new NotEmptyValidator );
				$Customer -> fields -> factuurtussenvoegsel = FormFactory :: createField ( );
				$Customer -> fields -> factuurachternaam    = FormFactory :: createField ( new NotEmptyValidator );
				$Customer -> fields -> factuuremail         = FormFactory :: createField ( new EmailValidator );
				$Customer -> fields -> factuuradres         = FormFactory :: createField ( new NotEmptyValidator  );
				$Customer -> fields -> factuurpostcode      = FormFactory :: createField ( new PostcodeValidator  );
				$Customer -> fields -> factuurwoonplaats    = FormFactory :: createField ( new NotEmptyValidator  );

				$Customer -> fields -> bezorgvoornaam       = FormFactory :: createField ( );
				$Customer -> fields -> bezorgtussenvoegsel  = FormFactory :: createField ( );
				$Customer -> fields -> bezorgachternaam     = FormFactory :: createField ( );
				$Customer -> fields -> bezorgadres          = FormFactory :: createField ( );
				$Customer -> fields -> bezorgpostcode       = FormFactory :: createField ( );
				$Customer -> fields -> bezorgwoonplaats     = FormFactory :: createField ( );

				$Customer -> fields -> store_customer       = FormFactory :: createField ( );
				$Customer -> fields -> two_addresses        = FormFactory :: createField ( );
				//@formatter:on

				if ( Request :: isPost ( ) ) {
					if ( Request :: post ( 'two_addresses' ) ) {
						$Customer -> fields -> bezorgvoornaam -> validator = new NotEmptyValidator;
						$Customer -> fields -> bezorgachternaam -> validator = new NotEmptyValidator;
						$Customer -> fields -> bezorgadres -> validator = new NotEmptyValidator;
						$Customer -> fields -> bezorgpostcode -> validator = new NotEmptyValidator;
						$Customer -> fields -> bezorgwoonplaats -> validator = new NotEmptyValidator;
					}

					// $Customer -> populateFromPost ( );
				}
				// else {
					// $customer_id = Cookie :: get ( 'customer_id' );
// 
					// if ( $customer_id ) {
						// $Customer -> populateFromDb ( $customer_id );
					// }
				// }

				return $Customer;
			}

		}
?>