<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	$helper_text = Loader :: helper ( 'text' );
	$helper_img = Loader :: helper ( 'image' );
?>

<ul id="nav-dashboard">
	<li>
		<a href="/dashboard/webshop/">Terug naar categorieën</a>
	</li>
	<li>
		<a href="/dashboard/webshop/detail/add/<?php echo $category_id; ?>/">Nieuw artikel</a>
	</li>
</ul>
<h2>Artikelen</h2>
<p>
	(Grijs gekleurde artikelen zijn niet zichtbaar in de webshop, donkerrood is een aanbieding, paars is een aanbieding die onzichtbaar is gemaakt)
</p>
<div id="details">
	<div id="msg"></div>

	<?php
		if ( isset ( $details ) ) {

			echo PHP_EOL;
			echo '<table class="ccm-results-list details sortable">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>Bewerken</th>';
			echo '<th>Naam</th>';
			echo '<th>Afbeelding</th>';
			echo '<th>Artikelnummer</th>';
			echo '<th>Omschrijving</th>';
			echo '<th>Prijs</th>';
			echo '<th>Prijs inkoop</th>';
			echo '<th>BTW</th>';
			echo '<th>Verwijderen</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			foreach ( $details as $detail ) {
				$hrefs = array (
					'update' => '/dashboard/webshop/detail/update/' . $category_id . '/' . $detail -> detail_id,
					'delete' => '/dashboard/webshop/detail/delete/' . $category_id . '/' . $detail -> detail_id,
				);
				$omschrijving = $helper_text -> sanitize ( $detail -> omschrijving, 80 ) . ' ...';
				$imgfile = File :: getById ( $detail -> imgfile_id );

				$tr_classes = array ( );
				if ( $detail -> zichtbaar ) {
					$tr_classes[ ] = 'visible';
				}
				else {
					$tr_classes[ ] = 'hidden';
				}
				if ( $detail -> aanbieding ) {
					$tr_classes[ ] = 'offer';
				}

				echo PHP_EOL;
				echo '<tr id="' . $detail -> detail_id . '" class="' . implode ( ' ', $tr_classes ) . '">';
				echo '<td class="extrasmall"><a href="' . $hrefs[ 'update' ] . '">bewerken</a></td>';
				echo '<td>' . $detail -> naam . '</td>';
				echo '<td>';

				if ( !$imgfile -> isError ( ) ) {
					$helper_img -> outputThumbnail ( $imgfile, 50, 9999 );
				}

				echo '</td>';
				echo '<td>' . $detail -> nummer . '</td>';
				echo '<td>' . $omschrijving . '</td>';
				echo '<td>' . $detail -> prijs . '</td>';
				echo '<td>' . $detail -> prijs_inkoop . '</td>';
				echo '<td>' . ($detail -> btw * 100) . '%</td>';
				echo '<td class="extrasmall"><a class="delete detail" href="' . $hrefs[ 'delete' ] . '">verwijderen</a></td>';
				echo '</tr>';
			}

			echo PHP_EOL;
			echo '</tbody>';
			echo '</table>';
		}
	?>
</div>