<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use ADOdb_Active_Record;
		Use Loader;
		Use Utilities\Date;
		Use Webshop\IoC;

		Interface DiscountCodeInterface {
		}

		Interface DiscountCodeValidatorInterface {
		}

		Interface DiscountCodeServiceProviderInterface {
		}

		Class DiscountCode Extends ADOdb_Active_Record Implements DiscountCodeInterface {
			public $_table = 'webshop_kortingcodes';
			public $validator;

			public function setValidator ( DiscountCodeValidatorInterface $DiscountCodeValidatorInterface ) {
				$this -> validator = $DiscountCodeValidatorInterface;
			}

			public function init ( $code = false ) {
				$this -> kortingcode_id = null;
				$this -> code = $this -> generateRandomString ( );
				$this -> korting = 0;
				$this -> status = 'created';
			}

			public function validate ( ) {
				return $this -> validator -> validate ( $this );
			}

			public function populate ( $params ) {
				foreach ( $params as $k => $v ) {
					$this -> $k = $v;
				}
			}

			public function generateRandomString ( $length = 8 ) {
				$alphabeth = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$alphabeth = str_shuffle ( $alphabeth );

				return substr ( $alphabeth, 0, $length );
			}

			public function cash ( ) {
				$this -> status = 'cashed';
				$this -> save ( );
			}

		}

		Class DiscountCodeValidator Implements DiscountCodeValidatorInterface {
			public $validators = array (
				// TODO: fill this array
			);

			public function validate ( DiscountCodeInterface $DiscountCodeInterface ) {
				foreach ( $this -> validators as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $DiscountCodeInterface -> $name );
					if ( !$validator -> pass ) {
						$DiscountCodeInterface -> errs[ $name ] = 'err';
					}
				}
				$DiscountCodeInterface -> pass = (count ( $DiscountCodeInterface -> errs ) == 0);

				return $DiscountCodeInterface -> pass;
			}

		}

		Class DiscountCodeServiceProvider Implements DiscountCodeServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'DiscountCode', function ( $code = false ) {
					$DiscountCode = new DiscountCode;
					if ( $code ) {
						$DiscountCode -> load ( 'code = ?', array ( $code ) );
					}
					else {
						$DiscountCode -> init ( );
					}
					return $DiscountCode;
				} );
			}

		}
?>