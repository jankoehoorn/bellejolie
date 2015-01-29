<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	$helper_datetime = Loader :: helper ( 'form/date_time' );
	$helper_interface = Loader :: helper ( 'concrete/interface' );
?>

<form method="post" action="<?php echo $this -> action ( $this -> controller -> getTask ( ), $id ); ?>">
	<div class="row">
		<?php
			echo $form -> label ( 'name', 'Titel:' );
			echo $form -> text ( 'name', $name );
		?>
	</div>
	<div class="row">
		<?php
			echo $form -> label ( 'description', 'Introductie:' );
			echo $form -> textarea ( 'description', $description, array ( 'class' => 'extralarge' ) );
		?>
	</div>
	<div class="row">
		<?php
			echo $form -> label ( 'date_public', 'Publicatiedatum:' );
			echo $helper_datetime -> datetime ( 'date_public', $date_public );
		?>
	</div>
	<div class="row">
		<?php
			Loader :: element ( 'editor_init' );
			Loader :: element ( 'editor_config' );
			Loader :: element ( 'editor_controls', array ( 'mode' => 'full' ) );

			echo $form -> textarea ( 'body', $body, array (
				'style' => 'width: 95%; height: 150px',
				'class' => 'ccm-advanced-editor'
			) );
		?>
	</div>
	<div class="row">
		<a class="btn ccm-button-v2-left" href="/index.php/dashboard/nieuws/">Cancel</a>
		<input type="submit" class="btn primary ccm-button-right" value="Submit" />
	</div>
</form>