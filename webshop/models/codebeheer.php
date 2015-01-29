<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Use \Utilities\Registry as Registry;

	Interface CodebeheerInterface {
		public static function getDatabase ( );
		public static function getCodeOptions ( );
		public static function getArtikelen ( );
	}

	Class CodebeheerFacade {
		private $artikelen;
		private $code_options;

		public function __construct ( ) {
			$this -> compile ( );
		}

		public function compile ( ) {
			$this -> artikelen = Codebeheer :: getArtikelen ( );
			$this -> code_options = Codebeheer :: getCodeOptions ( );
		}

		public function getArtikelen ( ) {
			return $this -> artikelen;
		}

		public function getCodeOptions ( ) {
			return $this -> code_options;
		}

		public function getArtikelCodeInfo ( $detail_id = false ) {
			return Codebeheer :: getArtikelCodeInfo ( $detail_id );
		}

	}

	Class Codebeheer Implements CodebeheerInterface {
		public static $db = null;

		public static function getDatabase ( ) {
			if ( empty ( self :: $db ) ) {
				self :: $db = Loader :: db ( );
			}
			return self :: $db;
		}

		public static function updateCode ( array $params = array() ) {
			$db = self :: getDatabase ( );
			$sql = "
				UPDATE webshop_voorraad
				SET code_id = ?
				WHERE voorraad_id = ?
			";
			$bindparams = array (
				$params[ 'code_id' ],
				$params[ 'voorraad_id' ],
			);
			$db -> execute ( $sql, $bindparams );
		}

		public static function getCodeOptions ( ) {
			$db = self :: getDatabase ( );
			$sql = "
				SELECT code_id, description
				FROM webshop_codes
			";
			$rows = $db -> getAll ( $sql );
			$options = array ( );

			foreach ( $rows as $row ) {
				$options[ $row[ 'code_id' ] ] = $row[ 'description' ];
			}

			return $options;
		}

		public static function getArtikelen ( ) {
			// See: https://www.simple-talk.com/sql/sql-training/subqueries-in-sql-server/
			// Good article on subquery syntax
			$db = self :: getDatabase ( );
			$sql = "
				SELECT (SELECT naam
				        FROM   webshop_categories
				        WHERE  category_id = d.category_id) AS category,
				       v.voorraad_id,
				       d.detail_id,
				       d.naam,
				       d.nummer,
				       d.prijs,
				       d.prijs_inkoop,
				       c.code_id,
				       c.description
				FROM   webshop_voorraad AS v
				       LEFT JOIN webshop_details AS d
				              ON ( v.detail_id = d.detail_id )
				       LEFT JOIN webshop_codes AS c
				              ON ( v.code_id = c.code_id )
				ORDER  BY category ASC,
						  d.naam ASC,
				          c.description ASC
			";
			$rows = $db -> getAll ( $sql );

			return $rows;
		}

	}

	Class CodebeheerPrinter {
		private $CodebeheerFacade;

		public function __construct ( CodebeheerFacade $CodebeheerFacade ) {
			$this -> CodebeheerFacade = $CodebeheerFacade;
		}

		public function printTableArtikelen ( ) {
			echo '
			<table id="codebeheer">
				<thead>
					<tr>
						<th>categorie</th>
						<th>naam</th>
						<th>nummer</th>
						<th>prijs</th>
						<th>prijs_inkoop</th>
						<th>code</th>
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
					</tr>
				</tfoot>
				
				<tbody>
			';

			foreach ( $this -> CodebeheerFacade -> getArtikelen( ) as $artikel ) {
				$select = $this -> printSelectCode ( $artikel );

				echo '<tr>';
				echo '<td>' . $artikel[ 'category' ] . '</td>';
				echo '<td><a href="detail/' . $artikel[ 'detail_id' ] . '/">' . $artikel[ 'naam' ] . '</a></td>';
				echo '<td>' . $artikel[ 'nummer' ] . '</td>';
				echo '<td>' . $artikel[ 'prijs' ] . '</td>';
				echo '<td>' . $artikel[ 'prijs_inkoop' ] . '</td>';
				echo '<td>' . $select . '</td>';
				echo '</tr>';
			}

			echo '
				</tbody>
			</table>';
		}

		public function printSelectCode ( $artikel ) {
			$html = '';

			$html .= '<select id="' . $artikel[ 'voorraad_id' ] . '">';

			foreach ( $this -> CodebeheerFacade -> GetCodeOptions() as $value => $text ) {
				$selected = ($artikel[ 'code_id' ] == $value) ? (' selected="selected"') : ('');
				$html .= '
					<option' . $selected . ' value="' . $value . '">' . $text . '</option>
				';
			}

			$html .= '</select>';

			return $html;
		}

	}
?>