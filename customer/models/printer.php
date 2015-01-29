<?php
	Namespace Customer;

		Use \stdClass;
		Use \DOMDocument;
		Use \Loader;
		Use \Utilities\Request;
		Use \Utilities\Cookie;
		Use \Utilities\CookieParams;

		Loader :: model ( 'factory', 'customer' );
		Loader :: model ( 'utilities', 'utilities' );

		Class CustomerPrinter {
			
		}
?>