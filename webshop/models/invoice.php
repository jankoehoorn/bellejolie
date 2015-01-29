<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use ADOdb_Active_Record;
		Use Loader;
		Use Utilities\Date;
		Use Webshop\IoC;

		Interface InvoiceInterface {
		}

		Interface InvoiceValidatorInterface {
		}

		Interface InvoiceServiceProviderInterface {
		}

		Class Invoice Extends ADOdb_Active_Record Implements InvoiceInterface {
			public $_table = 'webshop_facturen';

			public function setValidator ( InvoiceValidatorInterface $InvoiceValidatorInterface ) {
				$this -> validator = $InvoiceValidatorInterface;
			}

			public function init ( ) {
				//@formatter:off
				$this -> factuur_id            = null;
				$this -> unique_id             = uniqid ( );
				$this -> customer_id           = null;
				$this -> mollie_payment_id     = '';
				$this -> mollie_payment_status = 'open';
				$this -> jaar                  = 0;
				$this -> nummer                = 0;
				$this -> datumtijd             = Date :: now ( );
				$this -> subtot                = 0;
				$this -> btw                   = 0;
				$this -> verzendkosten         = 0;
				$this -> tot                   = 0;
				$this -> doc                   = Date :: now ( );
				$this -> dlm                   = Date :: now ( );
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

			public function createInvoiceNumber ( ) {
				$sql = "
					SELECT
						IFNULL(MAX(nummer) + 1,1) AS nummer,
						YEAR(CURDATE()) AS jaar
					FROM webshop_facturen
					WHERE jaar = YEAR(CURDATE())
				";
				$row = Loader :: db ( ) -> getRow ( $sql );
				$this -> jaar = $row[ 'jaar' ];
				$this -> nummer = $row[ 'nummer' ];
			}

			public function setCustomer ( $customer ) {
				//@formatter:off
				$this -> customer_id       = $customer -> customer_id;
				$this -> fullname          = $customer -> fullname;
				$this -> factuuradres      = $customer -> factuuradres;
				$this -> factuurpostcode   = $customer -> factuurpostcode;
				$this -> factuurwoonplaats = $customer -> factuurwoonplaats;
				//@formatter:on
			}

			// TODO: check if this one is needed
			public function calculate ( $invoice_details ) {
				foreach ( $invoice_details as $invoice_detail ) {

				}
			}

			public function isPaid ( ) {
				return ($this -> mollie_payment_status == 'paid');
			}

		}

		Class InvoiceValidator Implements InvoiceValidatorInterface {
			public $validators = array (
				// TODO: fill this array
			);

			// N.B. Run ValidatorServiceProvider :: register ( ) in the
			// PageType Controller on_start method before you can use the IoC Container
			public function validate ( InvoiceInterface $InvoiceInterface ) {
				foreach ( $this -> validators as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $InvoiceInterface -> $name );
					if ( !$validator -> pass ) {
						$InvoiceInterface -> errs[ $name ] = 'err';
					}
				}
				$InvoiceInterface -> pass = (count ( $InvoiceInterface -> errs ) == 0);

				return $InvoiceInterface -> pass;
			}

		}

		Class InvoiceServiceProvider Implements InvoiceServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'Invoice', function ( ) {
					return new Invoice;
				} );
			}

		}
?>