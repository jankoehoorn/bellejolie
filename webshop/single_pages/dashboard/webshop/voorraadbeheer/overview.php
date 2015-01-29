<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Loader :: model ( 'webshop', 'webshop' );
	Loader :: model ( 'voorraadbeheer', 'webshop' );

	require_once (getcwd ( ) . '/packages/webshop/elements/nav.php');
?>

<a href="/dashboard/webshop/export/">Export to Excel</a>

<?php
	$VoorraadbeheerPrinter = new VoorraadbeheerPrinter ( $VoorraadbeheerFacade );
	$VoorraadbeheerPrinter -> printTableArtikelen ( );
?>