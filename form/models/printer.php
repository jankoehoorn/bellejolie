<?php
	Namespace Form;

		Use \DOMDocument;
		Use \DOMXPath;
		Use \Loader;

		Loader :: model ( 'factory', 'form' );
		Loader :: model ( 'printer', 'form' );
		Loader :: model ( 'validator', 'form' );

		Class FormPrinter {
			public $form;

			public function __construct ( Form $form ) {
				$this -> form = $form;
			}

			public function printForm ( ) {
				echo $this -> form;
			}

		}
?>