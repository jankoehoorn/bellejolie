<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class Backup {
		public static function mail ( $attachment ) {
			require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/swift/swift_required.php';

			$msg = Swift_Message :: newInstance ( );
			$msg -> setSubject ( 'Backup Rocco Webshop' );
			$msg -> setFrom ( 'claudi.koehoorn@gmail.com' );
			$msg -> setTo ( array ( 'claudia.koehoorn@gmail.com' => 'Claudi Koehoorn' ) );
			$msg -> setBody ( 'Zie bijlage voor een backup van de Rocco Webshop' );
			$msg -> addPart ( '<p style="font: 12px Verdana;">Zie bijlage voor een backup van de Rocco Webshop</p>', 'text/html' );
			$msg -> attach ( Swift_Attachment :: fromPath ( $attachment ) );

			$mailer = Swift_Mailer :: newInstance ( Swift_MailTransport :: newInstance ( ) );
			$response = $mailer -> send ( $msg );

			return $response;
		}

		public static function tables ( $tables = '*' ) {
			$filename = $_SERVER[ 'DOCUMENT_ROOT' ] . '/files/backup/webshop_backup_' . strftime ( '%F_%T' ) . '.sql';

			if ( file_exists ( $filename ) ) {
				return;
			}

			// http://davidwalsh.name/backup-mysql-database-php
			//get all of the tables
			if ( $tables == '*' ) {
				$tables = array ( );
				$result = mysql_query ( 'SHOW TABLES' );
				while ( $row = mysql_fetch_row ( $result ) ) {
					$tables[ ] = $row[ 0 ];
				}
			}
			else {
				$tables = is_array ( $tables ) ? $tables : explode ( ',', $tables );
			}

			//cycle through
			foreach ( $tables as $table ) {
				$result = mysql_query ( 'SELECT * FROM ' . $table );
				$num_fields = mysql_num_fields ( $result );

				$return .= 'DROP TABLE ' . $table . ';';
				$row2 = mysql_fetch_row ( mysql_query ( 'SHOW CREATE TABLE ' . $table ) );
				$return .= "\n\n" . $row2[ 1 ] . ";\n\n";

				for ( $i = 0; $i < $num_fields; $i++ ) {
					while ( $row = mysql_fetch_row ( $result ) ) {
						$return .= 'INSERT INTO ' . $table . ' VALUES(';

						for ( $j = 0; $j < $num_fields; $j++ ) {
							$row[ $j ] = addslashes ( $row[ $j ] );
							$row[ $j ] = ereg_replace ( "\n", "\\n", $row[ $j ] );

							if ( isset ( $row[ $j ] ) ) { $return .= '"' . $row[ $j ] . '"';
							}
							else {
								$return .= '""';
							}

							if ( $j < ($num_fields - 1) ) {
								$return .= ',';
							}
						}

						$return .= ");\n";
					}
				}
				$return .= "\n\n\n";
			}

			// Save file
			$handle = fopen ( $filename, 'w+' );
			fwrite ( $handle, $return );
			fclose ( $handle );

			return $filename;
		}

	}
?>