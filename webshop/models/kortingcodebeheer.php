<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Use \Utilities\Registry;
	Use \Utilities\Request;

	Interface KortingCodebeheerInterface {
	}

	Class KortingCodebeheer Implements KortingCodebeheerInterface {
		public static function generateRandomCodes ( $korting, $num_codes = 100 ) {
			$db = Loader :: db ( );
			$sql = "
				INSERT IGNORE INTO webshop_kortingcodes
				( code, korting )
				VALUES
				( ?, ? )
			";

			for ( $i = 0; $i < $num_codes; $i++ ) {
				$bindparams = array (
					self :: generateRandomString ( ),
					$korting
				);
				$db -> execute ( $sql, $bindparams );
			}
		}

		public static function generateRandomString ( $length = 8 ) {
			$alphabeth = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$alphabeth = str_shuffle ( $alphabeth );

			return substr ( $alphabeth, 0, $length );
		}

		public static function getAll ( ) {
			$db = Loader :: db ( );
			$sql = "
				SELECT
					kortingcode_id,
					code,
					status,
					korting
				FROM webshop_kortingcodes
				ORDER BY korting ASC
			";
			$rows = $db -> getAll ( $sql );

			return $rows;
		}

		public static function checkCode ( ) {
			if ( Request :: post ( 'action' ) == 'check-code' ) {
				$db = Loader :: db ( );
				$sql = "
					SELECT
						korting
					FROM webshop_kortingcodes
					WHERE code = ?
					LIMIT 1
				";
				$bindparams = array ( Request :: post ( 'code' ) );
				$korting = $db -> getOne ( $sql, $bindparams );

				if ( !empty ( $korting ) ) {
					s ( $_SESSION );
				}
			}
		}

		public static function updateStatus ( $params ) {
			$params = (object)$params;
			$sql = "
				UPDATE webshop_kortingcodes
				SET status = ?
				WHERE kortingcode_id = ?
				LIMIT 1
			";
			$bindparams = array (
				$params -> status,
				$params -> kortingcode_id,
			);
			Loader :: db ( ) -> execute ( $sql, $bindparams );
		}

	}
?>