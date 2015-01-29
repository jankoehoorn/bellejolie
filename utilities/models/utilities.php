<?php
	Namespace Utilities;

		Use TCPDF;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
		require_once (Request :: documentRoot ( ) . '/tcpdf/config/tcpdf_config.php');
		require_once (Request :: documentRoot ( ) . '/tcpdf/tcpdf.php');

		Class PDF {
			public static function create ( ) {
				$pdf = new TCPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
				$pdf -> SetCreator ( PDF_CREATOR );
				$pdf -> SetAuthor ( 'Belle Jolie' );
				$pdf -> SetTitle ( 'Pakbon' );
				$pdf -> SetSubject ( 'Bestelnummer 12345' );
				$pdf -> SetKeywords ( 'Belle Jolie, Pakbon' );
				$pdf -> setPrintHeader ( false );
				// $pdf -> setPrintFooter ( false );
				$pdf -> SetDefaultMonospacedFont ( PDF_FONT_MONOSPACED );
				$pdf -> SetMargins ( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
				$pdf -> SetAutoPageBreak ( TRUE, PDF_MARGIN_BOTTOM );
				$pdf -> AddPage ( );

				return $pdf;
			}

		}

		Class Date {
			public static function now ( ) {
				return strftime ( '%F %T' );
			}

		}

		Class Mail {
			public $mailer;
			public $msg;

			public function __construct ( ) {
				require_once (Request :: documentRoot ( ) . '/swift/swift_required.php');

				$this -> setMailer ( );
				$this -> setMsg ( );
			}

			public function setMailer ( ) {
				$transport = \Swift_SmtpTransport :: newInstance ( 'mail.webreus.nl', 2525 );
				$transport -> setUsername ( 'info@bellejolie.nl' );
				$transport -> setPassword ( 'fpx7nqy8==a' );
				$this -> mailer = \Swift_Mailer :: newInstance ( $transport );
			}

			public function setMsg ( ) {
				$this -> msg = \Swift_Message :: newInstance ( );
				$this -> msg -> setFrom ( 'info@bellejolie.nl', 'Webshop Bellejolie' );
			}

			public function setTo ( $email, $name ) {
				$this -> msg -> setTo ( array ( $email => $name ) );
			}

			public function setSubject ( $subject ) {
				$this -> msg -> setSubject ( $subject );
			}

			public function setBodyPlainText ( $text ) {
				$this -> msg -> setBody ( $text );
			}

			public function setBodyHTML ( $html ) {
				$this -> msg -> addPart ( $html, 'text/html' );
			}

			public function addAttachment ( $path ) {
				$this -> msg -> attach ( \Swift_Attachment :: fromPath ( $path ) );
			}

			public function send ( ) {
				if ( JH_ENVIRONMENT === 'LIVE' ) {
					return $this -> mailer -> send ( $this -> msg );
				}

				return null;
			}

		}

		Class Debug {
			public static function displayErrors ( ) {
				ini_set ( 'display_errors', true );
			}

			public static function loadKint ( ) {
				require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/kint/Kint.class.php';
			}

			public static function comment ( $comment ) {
				echo PHP_EOL;
				echo '<!-- ';
				echo htmlentities ( $comment );
				echo ' -->';
				echo PHP_EOL;
			}

		}

		Class StringMethods {
			public static function deSlugify ( $str ) {
				return ( ucfirst ( str_replace ( '-', ' ', $str ) ));
			}

			public static function formatEuro ( $double ) {
				return ('€ ' . number_format ( $double, 2, ',', '.' ));
			}

			public static function getFirstWords ( $input, $length = 180 ) {
				if ( strlen ( $input ) > $length ) {
					$pos = strpos ( $input, ' ', $length );
					return substr ( $input, 0, $pos ) . '&hellip;';
				}
				return $input;
			}

			public static function sanitize ( $mixed ) {
				if ( is_string ( $mixed ) ) {
					$mixed = trim ( $mixed );
				}

				return $mixed;
			}

			public static function filterAndImplode ( array $array = array(), $glue = ' ' ) {
				return (implode ( $glue, array_filter ( $array ) ));
			}

			public static function getFullname ( array $pieces = array(), $glue = ' ' ) {
				$pieces = array_filter ( $pieces );
				$fullname = implode ( $glue, $pieces );
				return $fullname;
			}

		}

		Class Registry {
			public $vals = array ( );

			public function __get ( $key ) {
				return ($this -> vals[ $key ]);
			}

			public function __set ( $key, $value ) {
				$this -> vals[ $key ] = $value;
			}

			public function __isset ( $key ) {
				return isset ( $this -> vals[ $key ] );
			}

			public function __unset ( $key ) {
				unset ( $this -> vals[ $key ] );
			}

		}

		Class Cookie {
			public static function set ( CookieParams $CookieParams ) {
				setcookie ( $CookieParams -> name, $CookieParams -> value, time ( ) + (365 * 3600 * 24), '/' );
			}

			public static function get ( $name ) {
				return $_COOKIE[ $name ];
			}

			public static function destroy ( $name ) {
				setcookie ( $name, 0, time ( ) - (365 * 3600 * 24), '/' );
			}

		}

		Class CookieParams {
			public $name;
			public $value;

			public function __construct ( $name, $value ) {
				$this -> name = $name;
				$this -> value = $value;
			}

		}

		Class Session {
			public static function start ( ) {
				if ( session_status ( ) == PHP_SESSION_NONE ) {
					session_start ( );
				}
			}

			public static function set ( $name, $value ) {
				if ( !is_string ( $value ) ) {
					$value = serialize ( $value );
				}

				$_SESSION[ $name ] = $value;
			}

			public static function get ( $name ) {
				return $_SESSION[ $name ];
			}

		}

		Class Request {
			public static function postvars ( ) {
				return array_map ( array (
					'\Utilities\StringMethods',
					'sanitize',
				), $_POST );
			}

			public static function get ( $name, $default = '' ) {
				if ( !empty ( $_GET[ $name ] ) ) {
					return $_GET[ $name ];
				}

				return $default;
			}

			public static function post ( $name, $default = '' ) {
				if ( !empty ( $_POST[ $name ] ) ) {
					return $_POST[ $name ];
				}

				return $default;
			}

			public static function isPost ( ) {
				return ($_SERVER[ 'REQUEST_METHOD' ] == 'POST');
			}

			public static function server ( $name, $default = '' ) {
				if ( !empty ( $_SERVER[ $name ] ) ) {
					return $_SERVER[ $name ];
				}

				return $default;
			}

			public static function documentRoot ( ) {
				return static :: server ( 'DOCUMENT_ROOT' );
			}

			public static function redirect ( $url, $msg = '' ) {
				if ( !empty ( $msg ) ) {
					Session :: set ( 'msg', $msg );
				}

				header ( 'Location: ' . $url );
				exit ;
			}

		}
?>