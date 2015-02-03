<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use Loader;
		Use Utilities\Date;
		Use Webshop;
		Use Webshop\IoC;

		Interface CartInspectorInterface {
		}

		Interface CartInspectorValidatorInterface {
		}

		Interface CartInspectorServiceProviderInterface {
		}

		Class CartInspector Implements CartInspectorInterface {
			public $validator;

			public function setValidator ( CartInspectorValidatorInterface $CartInspectorValidatorInterface ) {
				$this -> validator = $CartInspectorValidatorInterface;
			}

			public function init ( ) {
				//@formatter:off
				$this -> cartinspector_id = null;
				$this -> doc              = Date :: now ( );
				$this -> dlm              = Date :: now ( );
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

			public function getTotal ( $registry ) {
				$tot = 0;
				$btw = 0;
				$result = 0;

				foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
					$detail = Webshop :: getDetail ( $detail_id );
					$category = Webshop :: getCategory ( $detail -> category_id );
					$properties = (object)$properties;
					$num_artikelen += $properties -> qty;
					$subtot = $detail -> prijs * $properties -> qty;
					$subbtw = round ( $subtot * $detail -> btw, 2 );
					$tot += $subtot;
					$btw += $subbtw;
				}

				$result = $tot + $btw;

				if ( $result < 50 ) {
					$result += $registry -> preferences -> verzendkosten;
				}

				return $result;
			}

			public function getTotalBtw ( ) {

			}

		}

		Class CartInspectorValidator Implements CartInspectorValidatorInterface {
			public $validators = array (
				// TODO: fill this array
			);

			public function validate ( CartInspectorInterface $CartInspectorInterface ) {
				foreach ( $this -> validators as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $CartInspectorInterface -> $name );
					if ( !$validator -> pass ) {
						$CartInspectorInterface -> errs[ $name ] = 'err';
					}
				}
				$CartInspectorInterface -> pass = (count ( $CartInspectorInterface -> errs ) == 0);

				return $CartInspectorInterface -> pass;
			}

		}

		Class CartInspectorServiceProvider Implements CartInspectorServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'CartInspector', function ( ) {
					return new CartInspector;
				} );
			}

		}
?>