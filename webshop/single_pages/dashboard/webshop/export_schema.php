<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
?>

<ul id="nav-dashboard">
	<li>
		<a href="/dashboard/webshop/detail_overview/<?php echo $category_id; ?>">Terug naar overzicht artikelen</a>
	</li>
</ul>
<h2>Artikel</h2>

<form method="post" accept-charset="utf-8" action="">

	<div class="row">
		<?php
			echo $form -> label ( 'tablenames_multi', 'Tablenames (select one or more):' );
		?>
		<select id="tablenames_multi" name="tablenames_multi[]" multiple="multiple">
			<?php
				foreach ( $tablenames as $k => $v ) {
					$selected = '';

					if ( in_array ( $k, $tablenames_multi ) ) {
						$selected = ' selected="selected"';
					}

					echo '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
				}
			?>
		</select>
	</div>

	<div>
		<a class="btn ccm-button-v2-left" href="/dashboard/webshop/detail_overview/<?php echo $category_id; ?>/">Cancel</a>
		<input type="submit" class="btn primary ccm-button-right" value="Submit" />
	</div>
</form>
<?php
	if ( !empty ( $xml_table ) ) {
		echo '<pre class="debug">';
		echo htmlentities ( $xml_table );
		echo '</pre>';
	}
?>
