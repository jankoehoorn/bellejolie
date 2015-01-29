<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	Loader :: model ( 'voorraadbeheer', 'webshop' );

	$detail_id = $_POST[ 'id' ];
	$num_artikelen = $_POST[ 'value' ];

	if ( !ctype_digit ( $num_artikelen ) ) {
		$num_artikelen = 0;
	}

	Voorraadbeheer :: addToVoorraad ( $detail_id, $num_artikelen );

	echo $num_artikelen;
?>