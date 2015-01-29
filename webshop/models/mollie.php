<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use Loader;
		Use Mollie_API_Client;
		Use Utilities\Date;
		Use Utilities\Request;

		require_once (Request :: documentRoot ( ) . '/libraries/Mollie/API/Autoloader.php');

		Interface MollieInterface {
		}

		Interface MollieServiceProviderInterface {
		}

		Class Mollie Implements MollieInterface {
			public $api;
			public $payment;
			public $webhookUrl;

			public function __construct ( ) {
				$this -> api = new Mollie_API_Client;

				if ( JH_ENVIRONMENT === 'MA_HOOD' ) {
					$this -> api -> setApiKey ( MOLLIE_API_KEY_TEST );
					$this -> webhookUrl = 'http://www.bellejolie.bb/betaling-verwerken/';
				}
				else {
					$this -> api -> setApiKey ( MOLLIE_API_KEY_LIVE );
					$this -> webhookUrl = 'http://www.bellejolie.nl/betaling-verwerken/';
				}
			}

			public function populate ( $params ) {
				foreach ( $params as $k => $v ) {
					$this -> $k = $v;
				}
			}

			public function setPayment ( $invoice ) {
				$protocol = isset ( $_SERVER[ 'HTTPS' ] ) && strcasecmp ( 'off', $_SERVER[ 'HTTPS' ] ) !== 0 ? "https" : "http";
				$hostname = $_SERVER[ 'HTTP_HOST' ];
				$path = dirname ( isset ( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : $_SERVER[ 'PHP_SELF' ] );

				$this -> payment = $this -> api -> payments -> create ( array (
					'amount' => $invoice -> tot,
					'description' => 'Bellejolie betaling',
					'redirectUrl' => $this -> webhookUrl . $invoice -> unique_id,
					'metadata' => array ( 'order_id' => $invoice -> factuur_id, ),
					'webhookUrl' => $this -> webhookUrl,
				) );

				$invoice -> mollie_payment_id = $this -> payment -> id;
				$invoice -> save ( );

				return $this -> payment -> getPaymentUrl ( );
			}

		}

		Class MollieServiceProvider Implements MollieServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'Mollie', function ( ) {
					return new Mollie;
				} );
			}

		}
?>