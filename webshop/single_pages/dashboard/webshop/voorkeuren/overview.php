<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Loader :: model ( 'webshop', 'webshop' );
	Loader :: model ( 'preferences', 'webshop' );

	require_once (getcwd ( ) . '/packages/webshop/elements/nav.php');
?>

<h2>Voorkeuren</h2>

<form method="post" accept-charset="UTF-8">
	<div class="row">
		<label>Tekst voor boodschap op homepage:</label>
		<input type="text" id="msg_homepage" name="msg_homepage" value="<?php echo $Preferences -> msg_homepage; ?>" />
	</div>

	<div class="row">
		<label>Verzendkosten:</label>
		<input type="text" id="verzendkosten" name="verzendkosten" value="<?php echo $Preferences -> verzendkosten; ?>" />
		<p class="info">
			N.B. Gebruik een punt ipv een komma
		</p>
	</div>

	<div class="row">
		<label for="mailbody_bellejolie">Standaardtekst voor de mail naar Bellejolie:</label>
		<textarea id="mailbody_bellejolie" name="mailbody_bellejolie" style="width: 600px; height: 250px;"><?php echo $Preferences -> mailbody_bellejolie; ?></textarea>
	</div>

	<div class="row">
		<label for="mailbody_customer">Standaardtekst voor de mail naar de klant:</label>
		<textarea id="mailbody_customer" name="mailbody_customer" style="width: 600px; height: 250px;"><?php echo $Preferences -> mailbody_customer; ?></textarea>
	</div>

	<div class="row">
		<input type="submit" value="opslaan" />
	</div>
</form>