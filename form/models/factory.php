<?php
	Namespace Form;

		Use \DOMDocument;
		Use \Customer\CustomerFactory;
		Use \Utilities\Request;
		Use \Utilities\Session;

		Class FormFactory {
			public static function createField ( ValidatorInterface $Validator = NULL ) {
				$Field = new Field;
				$Field -> validator = $Validator;

				if ( $Field -> validator ) {
					$Field -> validator -> pass = true;
				}

				return $Field;
			}

			// Het is een beetje gek dat de formfactory het opslaan van de customer afhandelt
			// TODO: REFACTOR AND PLACE THIS IN CustomerFactory :: createCustomer
			public static function createForm ( $DataObject ) {
				$Form = new Form ( $DataObject -> tpl );

				if ( Request :: isPost ( ) ) {
					if ( $DataObject -> pass ) {
						$classname = get_class ( $DataObject );
						Session :: set ( $classname, $DataObject );

						if ( $DataObject -> fields -> store_customer -> value ) {
							$DataObject -> save ( );
						}
					}
				}

				$Form -> populate ( $DataObject );

				return $Form;
			}

			public static function createPrinter ( ) {
				$Customer = CustomerFactory :: createCustomer ( );
				$Form = FormFactory :: createForm ( $Customer );
				$FormPrinter = new FormPrinter ( $Form );

				return $FormPrinter;
			}

		}
?>