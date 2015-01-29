<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	$helper_text = Loader :: helper ( 'text' );
	$helper_img = Loader :: helper ( 'image' );
?>

<ul id="nav-dashboard">
	<li>
		<a href="/dashboard/webshop/category/add/">Nieuwe categorie</a>
	</li>
	<li>
		<a href="/dashboard/webshop/voorraadbeheer/">Voorraadbeheer</a>
	</li>
	<li>
		<a href="/dashboard/webshop/codebeheer/">Codebeheer</a>
	</li>
	<li>
		<a href="/dashboard/webshop/kortingcodebeheer/">Kortingcodebeheer</a>
	</li>
</ul>
<a href="/dashboard/webshop/backup/">Backup Webshop</a>
<h2>Categorieën</h2>
<div id="categories">
	<div id="msg"></div>

	<?php
		echo '<p>' . $num_details . ' artikelen in ' . $num_categories . ' categorieën</p>';

		if ( isset ( $categories ) ) {

			echo PHP_EOL;
			echo '<table class="ccm-results-list categories sortable">';
			echo '<thead>';
			echo '<tr>';
			echo '<th>Bewerken</th>';
			echo '<th>Hoofdcategorie</th>';
			echo '<th>Naam</th>';
			echo '<th>Afbeelding</th>';
			echo '<th>Omschrijving</th>';
			echo '<th>Artikelen</th>';
			echo '<th>Verwijderen</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';

			foreach ( $categories as $category ) {
				$WebshopMainCategory = WebshopMainCategory :: getById ( $category -> main_category_id );
				$hrefs = array (
					'update' => '/dashboard/webshop/category/update/' . $category -> category_id,
					'detail_overview' => '/dashboard/webshop/detail_overview/' . $category -> category_id,
					'delete' => '/dashboard/webshop/category/delete/' . $category -> category_id,
				);
				$omschrijving = $helper_text -> sanitize ( $category -> omschrijving, 80 ) . ' ...';
				$imgfile = File :: getById ( $category -> imgfile_id );

				echo PHP_EOL;
				echo '<tr id="' . $category -> category_id . '">';
				echo '<td class="extrasmall"><a href="' . $hrefs[ 'update' ] . '">bewerken</a></td>';
				echo '<td class="strong">' . ucfirst ( $WebshopMainCategory -> naam ) . '</td>';
				echo '<td>' . $category -> naam . '</td>';
				echo '<td>';

				if ( !$imgfile -> isError ( ) ) {
					$helper_img -> outputThumbnail ( $imgfile, 50, 9999 );
				}

				echo '</td>';
				echo '<td>' . $omschrijving . '</td>';
				echo '<td><a href="' . $hrefs[ 'detail_overview' ] . '">artikelen</a></td>';
				echo '<td class="extrasmall"><a class="delete category" href="' . $hrefs[ 'delete' ] . '">verwijderen</a></td>';
				echo '</tr>';
			}

			echo PHP_EOL;
			echo '</tbody>';
			echo '</table>';
		}
	?>
</div>