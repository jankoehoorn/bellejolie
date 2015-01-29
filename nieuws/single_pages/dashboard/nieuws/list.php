<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	echo '<p><a href="' . $this -> url ( '/dashboard/nieuws', 'add' ) . '">Nieuwsitem toevoegen</a></p>';

	if ( isset ( $pages ) ) {

		echo PHP_EOL;
		echo '<table class="ccm-results-list">';
		echo '<tr>';
		echo '<th>Bewerken</th>';
		echo '<th><a href="">Titel</a></th>';
		echo '<th>Publicatiedatum</th>';
		echo '<th>Verwijderen</th>';
		echo '</tr>';

		foreach ( $pages as $page ) {
			echo PHP_EOL;
			echo '<tr>';
			echo '<td class="extrasmall"><a href="/dashboard/nieuws/update/' . $page -> getCollectionId ( ) . '">bewerken</a></td>';
			echo '<td><a href="' . $page -> getCollectionPath ( ) . '">' . $page -> getCollectionName ( ) . '</a></td>';
			echo '<td class="medium">' . $page -> getCollectionDatePublic ( ) . '</td>';
			echo '<td class="extrasmall"><a class="delete" href="/dashboard/nieuws/delete/' . $page -> getCollectionId ( ) . '">verwijderen</a></td>';
			echo '</tr>';
		}

		echo PHP_EOL;
		echo '</table>';
	}
?>