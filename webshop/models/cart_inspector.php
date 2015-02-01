<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use Loader;
		Use Utilities\Date;
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

			public function getTotalSubTot ( ) {

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