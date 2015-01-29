<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	$helper_asset_library = Loader :: helper ( 'concrete/asset_library' );
?>

<ul id="nav-dashboard">
	<li>
		<a href="/dashboard/webshop/">Terug naar overzicht categorieën</a>
	</li>
</ul>
<h2>Categorie</h2>

<form method="post" accept-charset="utf-8" action="<?php echo $this -> action ( $this -> controller -> getTask ( ), $action, $category_id ); ?>">

	<div class="row">
		<?php
			echo $form -> label ( 'main_category_id', 'Hoofdcategorie:' );
			echo $form -> select ( 'main_category_id', $main_category_ids, $category -> main_category_id );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'naam', 'Naam: *' );
			echo $form -> text ( 'naam', $category -> naam );
		?>
	</div>

	<div class="row">
		<?php
			$file = empty ( $category -> imgfile_id ) ? null : File :: getByID ( $category -> imgfile_id );
			echo $form -> label ( 'imgfile_id', 'Afbeelding: ' );
			echo $helper_asset_library -> image ( 'imgfile_id', 'imgfile_id', 'Select Thumbnail', $file );
		?>
	</div>

	<div class="row">
		<?php
			echo $form -> label ( 'omschrijving', 'Omschrijving:' );

			Loader :: element ( 'editor_init' );
			Loader :: element ( 'editor_config' );
			Loader :: element ( 'editor_controls', array ( 'mode' => 'full' ) );

			echo $form -> textarea ( 'omschrijving', $category -> omschrijving, array (
				'style' => 'width: 95%; height: 150px',
				'class' => 'ccm-advanced-editor'
			) );
		?>
	</div>

	<div class="row">
		<a class="btn ccm-button-v2-left" href="/dashboard/webshop/">Cancel</a>
		<input type="submit" class="btn primary ccm-button-right" value="Submit" />
		<?php
			echo $form -> hidden ( 'weight', $category -> weight );
		?>
	</div>

</form>