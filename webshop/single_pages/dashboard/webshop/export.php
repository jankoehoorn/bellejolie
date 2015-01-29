<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
?>

<h3>Click op de button om een export te maken van het voorraadbeheer</h3>
<p>
	Het CSV bestand van deze export kun je importeren in Excel
</p>
<br />
<form method="post" action="" class="clearfix">
	<input type="submit" class="btn primary ccm-button-left" value="exporteer voorraadbeheer" />
</form>

<?php
	if ( !empty ( $attachments ) ) {
		echo '<div>';
		echo '<p>Je kunt de bestanden ook rechtstreeks downloaden door rechtermuis &gt; opslaan als.. te gebruiken</p>';
		echo '<ul>';

		foreach ( $attachments as $attachment ) {
			echo '<li><a href="/files/export/' . basename ( $attachment ) . '">' . basename ( $attachment ) . '</a></li>';
		}

		echo '</ul>';
		echo '</div>';
	}
?>