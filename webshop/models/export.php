<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Loader :: model ( 'voorraadbeheer', 'webshop' );

	Class Export {
		public static function mail ( $attachments ) {
			require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/swift/swift_required.php';

			$msg = Swift_Message :: newInstance ( );
			$msg -> setSubject ( 'Export Voorraadbeheer Rocco Webshop voor Excel' );
			$msg -> setFrom ( 'claudi.koehoorn@gmail.com' );
			$msg -> setTo ( array ( 'claudia.koehoorn@gmail.com' => 'Claudi Koehoorn' ) );
			$msg -> setBody ( 'Zie bijlages voor een export voorraadbeheer van de Rocco Webshop' );
			$msg -> addPart ( '<p style="font: 12px Verdana;">Zie bijlage voor een export voorraadbeheer van de Rocco Webshop</p>', 'text/html' );

			foreach ( $attachments as $attachment ) {
				$msg -> attach ( Swift_Attachment :: fromPath ( $attachment ) );
			}

			$mailer = Swift_Mailer :: newInstance ( Swift_MailTransport :: newInstance ( ) );
			$response = $mailer -> send ( $msg );

			return $response;
		}

		public static function exportVoorraadbeheer ( $codes, $filename_prefix = '' ) {
			$rows = VoorraadbeheerFacade :: getExportVoorraadBeheer ( $codes );

			if ( empty ( $rows ) ) {
				return false;
			}

			if ( empty ( $filename_prefix ) ) {
				$filename = $_SERVER[ 'DOCUMENT_ROOT' ] . '/files/export/webshop_export_' . strftime ( '%F_%T' ) . '.csv';
			}
			else {
				$filename = $_SERVER[ 'DOCUMENT_ROOT' ] . '/files/export/webshop_export_' . $filename_prefix . '_' . strftime ( '%F_%T' ) . '.csv';
			}

			$SplFileObject = new SplFileObject ( $filename, 'w' );

			foreach ( $rows as $row ) {
				$row[ 'prijs' ]        = number_format ( $row[ 'prijs' ], 2, ',', '.' );
				$row[ 'prijs_inkoop' ] = number_format ( $row[ 'prijs_inkoop' ], 2, ',', '.' );
				$row[ 'waarde' ]       = number_format ( $row[ 'waarde' ], 2, ',', '.' );

				$SplFileObject -> fputcsv ( $row, ';' );
			}

			return $filename;
		}

	}
?>