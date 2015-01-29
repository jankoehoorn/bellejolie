<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Use \Utilities\Registry as Registry;

	Interface VoorraadbeheerInterface {
		public static function getDatabase ( );
		public static function getArtikelen ( );
		public static function getExportVoorraadbeheer ( array $codes );
	}

	Class VoorraadbeheerFacade {
		private $artikelen;
		private $artikelen_to_order;

		public function __construct ( ) {
			$this -> compile ( );
		}

		public function compile ( ) {
			$this -> artikelen = Voorraadbeheer :: getArtikelen ( );
			$this -> artikelen_to_order = Voorraadbeheer :: getArtikelenToOrder ( );
		}

		public function getArtikelen ( ) {
			return $this -> artikelen;
		}

		public function getArtikelenToOrder ( ) {
			return $this -> artikelen_to_order;
		}

		public function getArtikelVoorraadInfo ( $detail_id = false ) {
			return Voorraadbeheer :: getArtikelVoorraadInfo ( $detail_id );
		}

		public static function getExportVoorraadBeheer ( $codes ) {
			return Voorraadbeheer :: getExportVoorraadbeheer ( $codes );
		}

	}

	Class Voorraadbeheer Implements VoorraadbeheerInterface {
		//@formatter:off
		public static $db        = null;
		const CODE_OP_VOORRAAD   = 1;
		const CODE_VERKOCHT      = 2;
		const CODE_PROMOTIE      = 3;
		const CODE_EIGEN_GEBRUIK = 4;
		const CODE_BESCHADIGD    = 5;
		const CODE_TESTER        = 6;
		const CODE_BESTELLEN     = 7;
		//@formatter:on

		public static function getDatabase ( ) {
			if ( empty ( self :: $db ) ) {
				self :: $db = Loader :: db ( );
			}
			return self :: $db;
		}

		public static function getArtikelVoorraadInfo ( $detail_id ) {

		}

		public static function getExportVoorraadbeheer ( array $codes ) {
			$db = self :: getDatabase ( );
			$sql = "
				SELECT (
					SELECT naam
					FROM webshop_categories
					WHERE category_id = d.category_id) AS category,
					d.nummer,
					d.naam,
					d.prijs_inkoop,
					COUNT( voorraad_id ) AS num_op_voorraad,
					( COUNT( voorraad_id ) * d.prijs ) AS waarde,
					d.prijs,
					c.code
				FROM webshop_voorraad AS v
				LEFT JOIN webshop_codes AS c ON ( v.code_id = c.code_id )
				LEFT JOIN webshop_details AS d ON ( v.detail_id = d.detail_id )
				WHERE v.code_id IN ( " . implode ( ',', $codes ) . " )
				GROUP BY v.detail_id
				ORDER BY category ASC, d.naam ASC
			";
			$rows = $db -> getAll ( $sql );

			return $rows;
		}

		public static function getArtikelen ( ) {
			$db = self :: getDatabase ( );
			$sql = "
				SELECT c.naam AS category,
				       d.detail_id,
				       d.category_id,
				       d.naam,
				       d.nummer,
				       d.prijs,
				       d.prijs_inkoop,
				       d.imgfile_id,
				       IF(v.detail_id IS NULL, 0, COUNT(v.detail_id)) AS num_in_voorraad
				FROM   webshop_details AS d
				       LEFT JOIN webshop_voorraad AS v ON ( d.detail_id = v.detail_id AND v.code_id = 1 )
				       LEFT JOIN webshop_categories AS c ON ( d.category_id = c.category_id )
				GROUP  BY d.detail_id
				ORDER  BY d.detail_id ASC
			";
			$rows = $db -> getAll ( $sql );

			return $rows;
		}

		public static function getArtikelenToOrder ( ) {
			$db = self :: getDatabase ( );
			$sql = "
				SELECT
					(
				    	SELECT naam
				        FROM webshop_categories
				        WHERE category_id = d.category_id
				        LIMIT 1
				    ) AS category,
					v.detail_id,
					COUNT(v.detail_id) AS qty,
					d.naam,
					d.nummer,
					d.prijs,
					d.prijs_inkoop,
					d.imgfile_id
				FROM webshop_voorraad AS v
				LEFT JOIN webshop_details AS d ON ( v.detail_id = d.detail_id )
				WHERE code_id = 7
				GROUP BY v.detail_id
			";
			$rows = $db -> getAll ( $sql );

			return $rows;
		}

		public static function addToVoorraad ( $detail_id, $num_artikelen ) {
			$db = self :: getDatabase ( );

			// Delete old records
			$sql_delete = "
				DELETE FROM webshop_voorraad
				WHERE code_id = 1
				AND detail_id = ?
			";
			$bindparams = array ( $detail_id );
			$db -> execute ( $sql_delete, $bindparams );

			if ( $num_artikelen == 0 ) {
				return true;
			}

			// Insert new records
			$sql_insert = "
				INSERT INTO webshop_voorraad
				(detail_id, code_id, doc, dlm)
				VALUES
			";
			$values = array ( );
			$doc = strftime ( '%F %T' );
			$dlm = $doc;

			// Construct query
			for ( $i = 0; $i < $num_artikelen; $i++ ) {
				$values[ ] = "(" . $detail_id . ", 1, '" . $doc . "', '" . $dlm . "')";
			}

			$sql_insert .= (implode ( ',', $values ));
			$sql_insert .= ";";
			$db -> execute ( $sql_insert );
		}

	}

	Class VoorraadbeheerPrinter {
		private $VoorraadbeheerFacade;

		public function __construct ( VoorraadbeheerFacade $VoorraadbeheerFacade ) {
			$this -> VoorraadbeheerFacade = $VoorraadbeheerFacade;
		}

		public function printTableArtikelen ( ) {
			echo '
			<table id="voorraadbeheer">
				<thead>
					<tr>
						<th>categorie</th>
						<th>naam</th>
						<th>nummer</th>
						<th>prijs</th>
						<th>prijs_inkoop</th>
						<th>afbeelding</th>
						<th>voorraad aantal</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				
				<tbody>
			';

			foreach ( $this -> VoorraadbeheerFacade -> getArtikelen( ) as $artikel ) {
				echo '<tr>';
				echo '<td>' . $artikel[ 'category' ] . '</td>';
				echo '<td><a href="detail/' . $artikel[ 'detail_id' ] . '/">' . $artikel[ 'naam' ] . '</a></td>';
				echo '<td>' . $artikel[ 'nummer' ] . '</td>';
				echo '<td>' . $artikel[ 'prijs' ] . '</td>';
				echo '<td>' . $artikel[ 'prijs_inkoop' ] . '</td>';
				echo '<td>' . $artikel[ 'imgfile_id' ] . '</td>';
				echo '<td id="' . $artikel[ 'detail_id' ] . '" class="edit">' . $artikel[ 'num_in_voorraad' ] . '</td>';
				echo '</tr>';
			}

			echo '
				</tbody>
			</table>';
		}

		public function printTableArtikelenToOrder ( ) {
			echo '
			<table id="voorraadbeheer">
				<thead>
					<tr>
						<th>categorie</th>
						<th>naam</th>
						<th>nummer</th>
						<th>prijs</th>
						<th>prijs_inkoop</th>
						<th>afbeelding</th>
						<th>aantal</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</tfoot>
				
				<tbody>
			';

			foreach ( $this -> VoorraadbeheerFacade -> getArtikelenToOrder( ) as $artikel ) {
				echo '<tr>';
				echo '<td>' . $artikel[ 'category' ] . '</td>';
				echo '<td><a href="detail/' . $artikel[ 'detail_id' ] . '/">' . $artikel[ 'naam' ] . '</a></td>';
				echo '<td>' . $artikel[ 'nummer' ] . '</td>';
				echo '<td>' . $artikel[ 'prijs' ] . '</td>';
				echo '<td>' . $artikel[ 'prijs_inkoop' ] . '</td>';
				echo '<td>' . $artikel[ 'imgfile_id' ] . '</td>';
				echo '<td id="' . $artikel[ 'detail_id' ] . '" class="edit">' . $artikel[ 'qty' ] . '</td>';
				echo '</tr>';
			}

			echo '
				</tbody>
			</table>';
		}

	}
?>