<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Loader :: model ( 'webshop', 'webshop' );
	Loader :: model ( 'voorraadbeheer', 'webshop' );

	require_once (getcwd ( ) . '/packages/webshop/elements/nav.php');
?>

<h2>Codebeheer</h2>

<?php
	$CodebeheerPrinter = new CodebeheerPrinter ( $CodebeheerFacade );
	$CodebeheerPrinter -> printTableArtikelen ( );
?>
