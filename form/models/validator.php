<?php
	Namespace Form;

		Use DOMDocument;
		Use Utilities\Request;
		Use Webshop\IoC;

		Interface ValidatorInterface {
			public function validate ( $value );
		}

		Interface ValidatorServiceProviderInterface {
		}

		Abstract Class Validator Implements ValidatorInterface {
			public $pass;

			public function validate ( $value ) {
			}

		}

		Class NotEmptyValidator Extends Validator {
			public function validate ( $value ) {
				$this -> pass = ( !empty ( $value ));
			}

		}

		Class EmailValidator Extends Validator {
			public function validate ( $value ) {
				$this -> pass = filter_var ( $value, FILTER_VALIDATE_EMAIL );
			}

		}

		Class PostcodeValidator Extends Validator {
			public function validate ( $value ) {
				$this -> pass = preg_match ( '/[1-9]{1}[0-9]{3}\s?[a-zA-Z]{2}/', $value );
			}

		}

		Class ValidatorServiceProvider Implements ValidatorServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'NotEmptyValidator', function ( ) {
					return new NotEmptyValidator;
				} );

				IoC :: bind ( 'EmailValidator', function ( ) {
					return new EmailValidator;
				} );

				IoC :: bind ( 'PostcodeValidator', function ( ) {
					return new PostcodeValidator;
				} );
			}

		}
?>