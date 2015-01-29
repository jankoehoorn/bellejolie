<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Loader :: model ( 'webshop', 'webshop' );
	Loader :: model ( 'voorraadbeheer', 'webshop' );

	require_once (getcwd ( ) . '/packages/webshop/elements/nav.php');
?>

<h2>Kortingcodebeheer</h2>

<form method="post" accept-charset="UTF-8" action="">
	<div id="generate">
		<p>
			N.B. gebruik een PUNT in het bedrag en géén komma!
		</p>
		<p>
			Genereer
			<select name="num_codes" style="width: 50px;">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="100">100</option>
			</select>
			kortingcodes voor dit bedrag:
			<input type="text" name="korting" value="10.00" style="width: 100px;" />
			<input type="submit" value="GO!" />
		</p>
	</div>

	<h2>Overzicht kortingcodes</h2>

	<?php
		if ( !empty ( $kortingcodes ) ) {
			$tpl = '
				<tr>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td><input type="checkbox" name="delete[]" value="%d" /></td>
				</tr>
			';
			echo '
				<table id="kortingcodebeheer">
				<thead>
					<tr>
						<th>code</th>
						<th>korting</th>
						<th>status</th>
						<th>verwijderen</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>code</th>
						<th>korting</th>
						<th>status</th>
						<th>verwijderen</th>
					</tr>
				</tfoot>
				<tbody>
			';

			foreach ( $kortingcodes as $kortingcode ) {
				$kortingcode = ( object )$kortingcode;
				
				printf (
					$tpl,
					$kortingcode -> code,
					$kortingcode -> korting,
					$form -> select ( 'status', $statussen, $kortingcode -> status, array ( 'kortingcode_id' => $kortingcode -> kortingcode_id, 'class') ),
					$kortingcode -> kortingcode_id
				);
			}

			echo '</tbody>';
			echo '</table>';
		}
	?>
</form>