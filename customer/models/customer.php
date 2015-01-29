<?php
	Namespace Customer;
		Use stdClass;
		Use DOMDocument;
		Use Loader;
		Use ADOdb_Active_Record;
		Use Utilities\Request;
		Use Utilities\Cookie;
		Use Utilities\CookieParams;
		Use Webshop\IoC;

		Loader :: model ( 'factory', 'customer' );
		Loader :: model ( 'utilities', 'utilities' );

		Interface CustomerInterface {
		}

		Interface CustomerValidatorInterface {
		}

		Interface CustomerServiceProviderInterface {
		}

		Class Customer Extends ADOdb_Active_Record Implements CustomerInterface {
			public $_table = 'webshop_customers';
			public $validator;
			public $errs = array ( );

			public function setValidator ( CustomerValidatorInterface $CustomerValidatorInterface ) {
				$this -> validator = $CustomerValidatorInterface;
			}

			public function createCustomerNumber ( ) {
				$sql = "
					SELECT
						IFNULL(MAX(klantnummer) + 1,1000) AS klantnummer
					FROM webshop_customers
				";
				return Loader :: db ( ) -> getOne ( $sql );
			}

			public function init ( ) {
				$this -> customer_id = null;
				$this -> klantnummer = $this -> createCustomerNumber ( );
				$this -> factuurvoornaam = '';
				$this -> factuurtussenvoegsel = '';
				$this -> factuurachternaam = '';
				$this -> factuuremail = '';
				$this -> factuuradres = '';
				$this -> factuurpostcode = '';
				$this -> factuurwoonplaats = '';
				$this -> bezorgvoornaam = '';
				$this -> bezorgtussenvoegsel = '';
				$this -> bezorgachternaam = '';
				$this -> bezorgadres = '';
				$this -> bezorgpostcode = '';
				$this -> bezorgwoonplaats = '';
				$this -> cadeauservice = 0;
				$this -> tekst_cadeaukaart = '';
			}

			public function validate ( ) {
				return $this -> validator -> validate ( $this );
			}

			public function populate ( $params ) {
				foreach ( $params as $k => $v ) {
					$this -> $k = $v;
				}

				$this -> setFullname ( );
				if ( isset ( $params[ 'two_addresses' ] ) ) {
					$this -> two_addresses = 1;
				}
				else {
					$this -> two_addresses = 0;
				}
			}

			public function store ( ) {
				try {
					$response = parent :: save ( );
					return $response;
				}
				catch(Exception $e) {
					return $e;
				}
			}

			public function setFullname ( ) {
				$this -> fullname = implode ( ' ', array_filter ( array (
					$this -> factuurvoornaam,
					$this -> factuurtussenvoegsel,
					$this -> factuurachternaam,
				) ) );
			}

		}

		Class CustomerValidator Implements CustomerValidatorInterface {
			//@formatter:off
			public $validators_factuuradres = array (
				'factuurvoornaam'   => 'NotEmpty',
				'factuurachternaam' => 'NotEmpty',
				'factuuremail'      => 'Email',
				'factuuradres'      => 'NotEmpty',
				'factuurpostcode'   => 'Postcode',
				'factuurwoonplaats' => 'NotEmpty',
			);
			public $validators_bezorgadres = array (
				'bezorgvoornaam'    => 'NotEmpty',
				'bezorgachternaam'  => 'NotEmpty',
				'bezorgadres'       => 'NotEmpty',
				'bezorgpostcode'    => 'Postcode',
				'bezorgwoonplaats'  => 'NotEmpty',
			);
			//@formatter:on

			public function validate ( CustomerInterface $CustomerInterface ) {
				foreach ( $this -> validators_factuuradres as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $CustomerInterface -> $name );
					if ( !$validator -> pass ) {
						$CustomerInterface -> errs[ $name ] = 'err';
					}
				}

				if ( Request :: post ( 'two_addresses' ) ) {
					foreach ( $this -> validators_bezorgadres as $name => $type ) {
						$validator = IoC :: make ( $type . 'Validator' );
						$validator -> validate ( $CustomerInterface -> $name );
						if ( !$validator -> pass ) {
							$CustomerInterface -> errs[ $name ] = 'err';
						}
					}
				}

				return (count ( $CustomerInterface -> errs ) == 0);
			}

		}

		Class CustomerServiceProvider Implements CustomerServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'Customer', function ( $customer_id ) {
					$Customer = new Customer;
					$Customer -> setValidator ( IoC :: make ( 'CustomerValidator' ) );
					if ( $customer_id ) {
						$Customer -> load ( 'customer_id = ?', array ( $customer_id ) );
						if ( $Customer -> customer_id ) {
							$Customer -> setFullname ( );
						}
						else {
							$Customer -> init ( );
						}
					}
					else {
						$Customer -> init ( );
					}
					return $Customer;
				} );
				IoC :: bind ( 'CustomerValidator', function ( ) {
					$CustomerValidator = new CustomerValidator;
					return $CustomerValidator;
				} );
			}

		}
?>