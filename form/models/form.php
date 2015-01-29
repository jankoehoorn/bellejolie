<?php
	Namespace Form;
		Use DOMDocument;
		Use DOMXPath;
		Use Loader;
		Use Customer\CustomerInterface;
		Use Utilities\Request;
		Use Webshop\IoC;

		Loader :: model ( 'factory', 'form' );
		Loader :: model ( 'validator', 'form' );

		Class CustomerForm {
			public $dom;
			public $xpath;

			public function __construct ( $filename ) {
				$this -> dom = new DOMDocument;
				$this -> dom -> preserveWhiteSpace = true;
				$this -> dom -> validateOnParse = true;
				$this -> dom -> formatOutput = true;
				$this -> dom -> loadHTMLFile ( $filename );
				$this -> xpath = new DOMXPath ( $this -> dom );
			}

			public function __toString ( ) {
				$html = PHP_EOL . PHP_EOL;
				$html .= preg_replace ( '~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $this -> dom -> saveHTML ( ) );
				$html .= PHP_EOL . PHP_EOL;

				return $html;
			}

			public function populate ( CustomerInterface $CustomerInterface ) {
				foreach ( $CustomerInterface -> getAttributeNames ( ) as $name ) {
					$domelement = $this -> getElementById ( $name );
					$this -> setDOMElementValue ( $domelement, $CustomerInterface -> $name );

					if ( !empty ( $CustomerInterface -> errs[ $name ] ) ) {
						$this -> addDOMElementClass ( $domelement, $CustomerInterface -> errs[ $name ] );
					}
				}

				foreach ( array('two_addresses','store_customer','cadeauservice') as $checkbox ) {
					if ( Request :: post ( $checkbox ) ) {
						$domelement = $this -> getElementById ( $checkbox );
						$this -> setDOMElementValue ( $domelement, true );
					}
				}
			}

			public function getElementById ( $id ) {
				return $this -> xpath -> query ( "//*[@id='$id']" ) -> item ( 0 );
			}

			public function setDOMElementValue ( $domelement, $value ) {
				switch ($domelement -> tagName) {
					case 'select':
						$option_nodes = $domelement -> getElementsByTagName ( 'option' );

						foreach ( $option_nodes as $option_node ) {
							$option_value = $option_node -> getAttribute ( 'value' );

							if ( $value == $option_value ) {
								$option_node -> setAttribute ( 'selected', 'selected' );
							}
							else {
								$option_node -> removeAttribute ( 'selected' );
							}
						}

						break;

					case 'textarea':
						$domelement -> nodeValue = $value;
						break;

					case 'input':
						switch ($domelement -> getAttribute('type')) {
							case 'text':
								$domelement -> setAttribute ( 'value', $value );
								break;

							case 'checkbox':
								if ( empty ( $value ) ) {
									$domelement -> removeAttribute ( 'checked' );
								}
								else {
									$domelement -> setAttribute ( 'checked', 'checked' );
								}
								break;
						}
						break;
				}
			}

			public function addDOMElementClass ( $domelement, $class ) {
				$classes = explode ( ' ', $domelement -> getAttribute ( 'class' ) );
				$classes[ ] = $class;
				$classes = array_unique ( $classes );
				$classes = array_filter ( $classes );
				$str_classes = implode ( ' ', $classes );

				$domelement -> setAttribute ( 'class', $str_classes );
			}

		}

		Class CustomerFormServiceProvider {
			public static function register ( ) {
				IoC :: bind ( 'Form', function ( $Customer ) {
					$path = Request :: server ( 'DOCUMENT_ROOT' ) . '/packages/webshop/elements/forms/customer.html';
					$Form = new CustomerForm ( $path );
					$Form -> populate ( $Customer );
					return $Form;
				} );

			}

		}
?>