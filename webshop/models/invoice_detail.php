<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use ADOdb_Active_Record;
		Use Loader;
		Use Utilities\Date;
		Use Webshop\IoC;

		Interface InvoiceDetailInterface {
		}

		Interface InvoiceDetailValidatorInterface {
		}

		Interface InvoiceDetailServiceProviderInterface {
		}

		Class InvoiceDetail Extends ADOdb_Active_Record Implements InvoiceDetailInterface {
			public $_table = 'webshop_factuurregels';

			public function setValidator ( InvoiceDetailValidatorInterface $InvoiceDetailValidatorInterface ) {
				$this -> validator = $InvoiceDetailValidatorInterface;
			}

			public function init ( ) {
				//@formatter:off
				$this -> factuurregel_id = null;
				$this -> doc             = Date :: now ( );
				$this -> dlm             = Date :: now ( );
				//@formatter:on
			}

			public function validate ( ) {
				return $this -> validator -> validate ( $this );
			}

			public function populate ( $params ) {
				foreach ( $params as $k => $v ) {
					$this -> $k = $v;
				}
			}

		}

		Class InvoiceDetailValidator Implements InvoiceDetailValidatorInterface {
			public $validators = array (
				// TODO: fill this array
			);

			public function validate ( InvoiceDetailInterface $InvoiceDetailInterface ) {
				foreach ( $this -> validators as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $InvoiceDetailInterface -> $name );
					if ( !$validator -> pass ) {
						$InvoiceDetailInterface -> errs[ $name ] = 'err';
					}
				}
				$InvoiceDetailInterface -> pass = (count ( $InvoiceDetailInterface -> errs ) == 0);

				return $InvoiceDetailInterface -> pass;
			}

		}

		Class InvoiceDetailServiceProvider Implements InvoiceDetailServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'InvoiceDetail', function ( ) {
					return new InvoiceDetail;
				} );
			}

		}
?>