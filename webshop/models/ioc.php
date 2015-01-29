<?php
	Namespace Webshop;

		Use Closure;
		Use Exception;

		Interface IoCInterface {
			public static function bind ( $name, Closure $Closure );
			public static function bound ( $name );
			public static function make ( $name, $id = false );
		}

		Class IoC Implements IoCInterface {
			protected static $registry = array ( );

			public static function bind ( $name, Closure $Closure ) {
				static :: $registry[ $name ] = $Closure;
			}

			public static function bound ( $name ) {
				return array_key_exists ( $name, static :: $registry );
			}

			public static function make ( $name, $id = false ) {
				if ( static :: bound ( $name ) ) {
					$name = static :: $registry[ $name ];
					return $name ( $id );
				}
				else {
					Throw new Exception ( 'Nothing bound to ' . $name );
				}
			}

		}
?>