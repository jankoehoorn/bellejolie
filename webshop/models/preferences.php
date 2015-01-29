<?php
	Namespace Webshop;

		Use ADOdb_Active_Record;
		Use Webshop\IoC;

		Interface PreferencesInterface {
		}

		Interface PreferencesValidatorInterface {
		}

		Interface PreferencesServiceProviderInterface {
		}

		Class Preferences Extends ADOdb_Active_Record Implements PreferencesInterface {
			public $_table = 'webshop_preferences';
			public $validator;

			public function setValidator ( PreferencesValidatorInterface $PreferencesValidatorInterface ) {
				$this -> validator = $PreferencesValidatorInterface;
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

		Class PreferencesValidator Implements PreferencesValidatorInterface {
			public function validate ( PreferencesInterface $PreferencesInterface ) {
				//@formatter:off
				return (
					is_numeric ( $PreferencesInterface -> verzendkosten ) &&
					!empty ( $PreferencesInterface -> mailbody_bellejolie ) &&
					!empty ( $PreferencesInterface -> mailbody_customer )
				);
				//@formatter:on
			}

		}

		Class PreferencesServiceProvider Implements PreferencesServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'Preferences', function ( ) {
					$Preferences = new Preferences;
					$Preferences -> load ( 'preference_id = ?', array ( 1 ) );
					return $Preferences;
				} );
			}

		}
?>