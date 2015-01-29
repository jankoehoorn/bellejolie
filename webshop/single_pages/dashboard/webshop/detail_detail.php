<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	$helper_asset_library = Loader :: helper ( 'concrete/asset_library' );
?>

<ul id="nav-dashboard">
	<li>
		<a href="/dashboard/webshop/detail_overview/<?php echo $category_id; ?>">Terug naar overzicht artikelen</a>
	</li>
</ul>
<h2>Artikel</h2>

<form method="post" accept-charset="utf-8" action="<?php echo $this -> action ( $this -> controller -> getTask ( ), $action, $category_id, $detail_id ); ?>">

	<div class="row">
		<?php
			echo $form -> checkbox ( 'zichtbaar', '1', $detail -> zichtbaar );
			echo $form -> label ( 'zichtbaar', 'Artikel zichtbaar in de webshop', array ( 'class' => 'inline' ) );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> checkbox ( 'aanbieding', '1', $detail -> aanbieding );
			echo $form -> label ( 'aanbieding', 'Aanbieding (verschijnt op homepage)', array ( 'class' => 'inline' ) );
		?>
	</div>

	<div class="row">
		<?php
			$file = empty ( $detail -> imgfile_id ) ? null : File :: getByID ( $detail -> imgfile_id );
			echo $form -> label ( 'imgfile_id', 'Afbeelding: ' );
			echo $helper_asset_library -> image ( 'imgfile_id', 'imgfile_id', 'Select Thumbnail', $file );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'naam', 'Naam artikel: *' );
			echo $form -> text ( 'naam', $detail -> naam );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'nummer', 'Artikelnummer: *' );
			echo $form -> text ( 'nummer', $detail -> nummer );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'prijs', 'Prijs verkoop: *' );
			echo $form -> text ( 'prijs', $detail -> prijs, array ( 'style' => 'height: 40px; font-size: 36px; line-height: 40px; color: #009FDA;' ) );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'prijs_inkoop', 'Prijs inkoop: *' );
			echo $form -> text ( 'prijs_inkoop', $detail -> prijs_inkoop, array ( 'style' => 'height: 40px; font-size: 36px; line-height: 40px; color: #009FDA;' ) );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'btw', 'BTW: *' );
			echo $form -> select ( 'btw', $btw_tarieven, $detail -> btw );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'omschrijving', 'Omschrijving:' );

			Loader :: element ( 'editor_init' );
			Loader :: element ( 'editor_config' );
			Loader :: element ( 'editor_controls', array ( 'mode' => 'full' ) );

			echo $form -> textarea ( 'omschrijving', $detail -> omschrijving, array (
				'style' => 'width: 95%; height: 150px',
				'class' => 'ccm-advanced-editor'
			) );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'opmerkingen', 'Opmerkingen <em>(bijv: drie tot vijf dagen levertijd)</em>' );
			echo $form -> textarea ( 'opmerkingen', $detail -> opmerkingen, array ( 'class' => 'large' ) );
		?>
	</div>

	<div class="row">
		<a class="btn ccm-button-v2-left" href="/dashboard/webshop/detail_overview/<?php echo $category_id; ?>/">Cancel</a>
		<input type="submit" class="btn primary ccm-button-right" value="Submit" />
		<?php
			echo $form -> hidden ( 'weight', $detail -> weight );
		?>
	</div>

</form>