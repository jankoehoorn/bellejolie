<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use Exception;
		Use Loader;
		Use Utilities\Date;
		Use Utilities\Mail;
		Use Utilities\Request;
		Use Utilities\StringMethods;
		Use Webshop;
		Use Webshop\IoC;

		Interface OrderProcessorInterface {
		}

		Interface OrderProcessorValidatorInterface {
		}

		Interface OrderProcessorServiceProviderInterface {
		}

		Class OrderProcessor Implements OrderProcessorInterface {
			public $btw;
			public $cart;
			public $customer;
			public $inventory_manager;
			public $invoice;
			public $invoice_details = array ( );
			public $mail_bellejolie;
			public $mail_customer;
			public $mollie;
			public $pdf_invoice;
			public $pdf_invoice_path;
			public $pdf_packing_slip;
			public $pdf_packing_slip_path;
			public $postvars;
			public $prefereneces;
			public $subtot;
			public $testmode = false;
			public $tot;
			public $validator;

			public function setTestmode ( $testmode ) {
				$this -> testmode = $testmode;
			}

			public function setInventoryManager ( $InventoryManager ) {
				$this -> inventory_manager = $InventoryManager;
			}

			public function setValidator ( OrderProcessorValidatorInterface $OrderProcessorValidatorInterface ) {
				$this -> validator = $OrderProcessorValidatorInterface;
			}

			public function setMollie ( MollieInterface $MollieInterface ) {
				$this -> mollie = $MollieInterface;
			}

			public function setMolliePayment ( ) {
				return $this -> mollie -> setPayment ( $this -> invoice );
			}

			public function setPreferences ( PreferencesInterface $PreferencesInterface ) {
				$this -> preferences = $PreferencesInterface;
				$this -> preferences -> pdf_dir = Request :: documentRoot ( ) . 'files/pdfs/';
			}

			public function setPostvars ( $postvars ) {
				$this -> postvars = $postvars;
			}

			public function setCustomer ( $CustomerInterface ) {
				$this -> customer = $CustomerInterface;
				if ( !empty ( $this -> postvars ) ) {
					$this -> customer -> populate ( $this -> postvars );
				}
			}

			public function setCart ( $cart ) {
				$this -> cart = $cart;
			}

			public function emptyCart ( ) {
				unset ( $_SESSION[ 'cart' ] );
				unset ( $_SESSION[ 'discount' ] );
				unset ( $_SESSION[ 'discount_code' ] );
				unset ( $this -> cart );
			}

			public function setInvoice ( InvoiceInterface $InvoiceInterface ) {
				if ( empty ( $this -> customer ) ) {
					Throw new Exception ( 'Customer is not set. Call OrderProcessor -> setCustomer first' );
				}

				$this -> invoice = $InvoiceInterface;
				if ( !$InvoiceInterface -> _saved ) {
					$this -> invoice -> init ( );
				}
				$this -> invoice -> setCustomer ( $this -> customer );
				$this -> invoice -> save ( );
			}

			public function setInvoiceDetails ( ) {
				if ( empty ( $this -> cart ) ) {
					Throw new Exception ( 'Cart is not set. Call OrderProcessor -> setCart first' );
				}

				foreach ( $this -> cart as $detail_id => $properties ) {
					//@formatter:off
					$detail                            = Webshop :: getDetail ( $detail_id );
					$category                          = Webshop :: getCategory ( $detail -> category_id );
					$properties                        = (object)$properties;

					$invoice_detail                    = IoC :: make ( 'InvoiceDetail' );
					$invoice_detail -> factuurregel_id = null;
					$invoice_detail -> factuur_id      = $this -> invoice -> factuur_id;
					$invoice_detail -> categorie_naam  = $category -> naam;
					$invoice_detail -> detail_id       = $detail -> detail_id;
					$invoice_detail -> detail_naam     = $detail -> naam;
					$invoice_detail -> qty             = $properties -> qty;
					$invoice_detail -> prijs           = $detail -> prijs * $properties -> qty;
					$invoice_detail -> btw             = round ( $invoice_detail -> prijs * $detail -> btw, 2 );
					$invoice_detail -> prijs_incl_btw  = $invoice_detail + $invoice_detail -> btw;
					$invoice_detail -> doc             = Date :: now ( );
					$invoice_detail -> dlm             = Date :: now ( );
					
					$this -> invoice -> subtot += $invoice_detail -> prijs;
					$this -> invoice -> btw += $invoice_detail -> btw;
					//@formatter:on
					$invoice_detail -> save ( );

					$this -> addInvoiceDetail ( $invoice_detail );
					unset ( $invoice_detail );
				}

				if ( $this -> invoice -> subtot + $this -> invoice -> btw > 50 ) {
					$this -> invoice -> verzendkosten = 0;
				}
				else {
					$this -> invoice -> verzendkosten = $this -> preferences -> verzendkosten;
				}

				$this -> invoice -> tot = $this -> invoice -> subtot + $this -> invoice -> btw + $this -> invoice -> verzendkosten;

				if ( !empty ( $_SESSION[ 'discount' ] ) ) {
					$this -> invoice -> tot -= $_SESSION[ 'discount' ];
				}
			}

			public function addInvoiceDetail ( $invoice_detail ) {
				$this -> invoice_details[ ] = $invoice_detail;
			}

			public function updateInventory ( ) {
				foreach ( $this -> cart as $detail_id => $properties ) {
					$properties = (object)$properties;
					$num_items_in_stock = $this -> inventory_manager -> getNumDetailsInStock ( $detail_id );
					if ( $properties -> qty <= $num_items_in_stock ) {
						$num_items_to_sell = $properties -> qty;
						$num_items_to_order = 0;
					}
					else {
						$num_items_to_sell = $num_items_in_stock;
						$num_items_to_order = $properties -> qty - $num_items_in_stock;
					}
					for ( $i = 0; $i < $num_items_to_sell; $i++ ) {
						$this -> inventory_manager -> sellDetail ( $detail_id );
					}
					for ( $i = 0; $i < $num_items_to_order; $i++ ) {
						$this -> inventory_manager -> orderDetail ( $detail_id );
					}
				}
			}

			public function createPackingSlipPdf ( $pdf, $html ) {
				$this -> pdf_packing_slip_path = sprintf ( '%spacking-slip-%04d-%04d.pdf', $this -> preferences -> pdf_dir, $this -> invoice -> jaar, $this -> invoice -> nummer );
				$this -> pdf_packing_slip = $pdf;
				$this -> pdf_packing_slip -> writeHTML ( $html, true, false, true, false, '' );
				$this -> pdf_packing_slip -> image ( $this -> preferences -> pdf_dir . 'logo.png', 150, 10, 50, 38, 'PNG', 'http://www.bellejolie.nl/', '', true, 300 );
				$this -> pdf_packing_slip -> output ( $this -> pdf_packing_slip_path, 'F' );
			}

			public function createInvoicePdf ( $pdf, $html ) {
				$this -> pdf_invoice_path = sprintf ( '%sinvoice-%04d-%04d.pdf', $this -> preferences -> pdf_dir, $this -> invoice -> jaar, $this -> invoice -> nummer );
				$this -> pdf_invoice = $pdf;
				$this -> pdf_invoice -> writeHTML ( $html, true, false, true, false, '' );
				$this -> pdf_invoice -> image ( $this -> preferences -> pdf_dir . 'logo.png', 150, 10, 50, 38, 'PNG', 'http://www.bellejolie.nl/', '', true, 300 );
				$this -> pdf_invoice -> output ( $this -> pdf_invoice_path, 'F' );
			}

			public function setMailbodyBelleJolie ( ) {
				$mailbody = str_replace ( '{{naam}}', $this -> customer -> fullname, $this -> preferences -> mailbody_bellejolie );
				$this -> mail_bellejolie -> setBodyPlainText ( $this -> createMailbody );
				$this -> mail_bellejolie -> setBodyHTML ( nl2br ( $mailbody ) );
			}

			public function setMailbodyCustomer ( ) {
				$mailbody = str_replace ( '{{naam}}', $this -> customer -> fullname, $this -> preferences -> mailbody_customer );
				$this -> mail_customer -> setBodyPlainText ( $this -> createMailbody );
				$this -> mail_customer -> setBodyHTML ( nl2br ( $mailbody ) );
			}

			public function sendMailToBelleJolie ( ) {
				$this -> mail_bellejolie = new Mail;
				$this -> mail_bellejolie -> setTo ( 'claudia.koehoorn@gmail.com', 'Bellejolie - Claudia Koehoorn' );
				$this -> mail_bellejolie -> setSubject ( 'Pakbon en Factuur Bellejolie' );
				$this -> mail_bellejolie -> addAttachment ( $this -> pdf_packing_slip_path );
				$this -> mail_bellejolie -> addAttachment ( $this -> pdf_invoice_path );
				$this -> setMailbodyBelleJolie ( );
				if ( $this -> testmode == false ) {
					$this -> mail_bellejolie -> send ( );
				}
			}

			public function sendMailToCustomer ( ) {
				$this -> mail_customer = new Mail;
				$this -> mail_customer -> setTo ( $this -> customer -> factuuremail, StringMethods :: filterAndImplode ( array (
					$this -> customer -> factuurvoornaam,
					$this -> customer -> factuurtussenvoegsel,
					$this -> customer -> factuurachternaam,
				) ) );
				$this -> mail_customer -> setSubject ( 'Factuur Bellejolie' );
				$this -> mail_customer -> addAttachment ( $this -> pdf_invoice_path );
				$this -> setMailbodyCustomer ( );
				if ( $this -> testmode == false ) {
					$this -> mail_customer -> send ( );
				}
			}

			public function init ( ) {
				//@formatter:off
				$this -> OrderProcessor_id = null;
				$this -> doc           = Date :: now ( );
				$this -> dlm           = Date :: now ( );
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

		Class OrderProcessorValidator Implements OrderProcessorValidatorInterface {
			public $validators = array ( );

			public function validate ( OrderProcessorInterface $OrderProcessorInterface ) {
				foreach ( $this -> validators as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $OrderProcessorInterface -> $name );
					if ( !$validator -> pass ) {
						$OrderProcessorInterface -> errs[ $name ] = 'err';
					}
				}
				$OrderProcessorInterface -> pass = (count ( $OrderProcessorInterface -> errs ) == 0);

				return $OrderProcessorInterface -> pass;
			}

		}

		Class OrderProcessorServiceProvider Implements OrderProcessorServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'OrderProcessor', function ( ) {
					return new OrderProcessor;
				} );
			}

		}
?>