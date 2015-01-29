<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );
	require_once ($_SERVER[ 'DOCUMENT_ROOT' ] . '/kint/Kint.class.php');

	class DashboardWebshopController extends DashboardBaseController {

		public $helpers = array (
			'html',
			'form'
		);

		public function on_start ( ) {
			Loader :: model ( 'webshop', 'webshop' );
			$this -> error = Loader :: helper ( 'validation/error' );
			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> css ( '/packages/webshop/css/dashboard.css' ) );
			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/webshop/js/dashboard.js' ) );
		}

		public function on_before_render ( ) {
			$this -> set ( 'error', $this -> error );

			if ( !empty ( $_SESSION[ 'message' ] ) ) {
				$this -> set ( 'message', $_SESSION[ 'message' ] );
				unset ( $_SESSION[ 'message' ] );
			}
		}

		public function export_schema ( ) {
			// Get tablenames from DB
			$db = Loader :: db ( );
			$tablename = '';
			$tablenames = array ( );
			$sql = "	SHOW TABLES FROM " . DB_DATABASE;
			$rows = $db -> getAll ( $sql );

			foreach ( $rows as $row ) {
				foreach ( $row as $k => $v ) {
					$tablenames[ $v ] = $v;
				}
			}

			if ( is_array ( $this -> post ( 'tablenames_multi' ) ) ) {
				$schema = new adoSchema ( $db );
				$xml_db = $schema -> extractSchema ( );

				$dom_db = new DOMDocument;
				$dom_db -> loadXML ( $xml_db );
				$nodes_table = $dom_db -> getElementsByTagname ( 'table' );

				$dom_table = new DOMDocument ( '1.0' );
				$dom_table -> formatOutput = true;

				$node_schema = $dom_table -> createElement ( 'schema' );
				$node_schema -> setAttribute ( 'version', '0.3' );

				foreach ( $nodes_table as $node_table ) {
					if ( in_array ( $node_table -> getAttribute ( 'name' ), $this -> post ( 'tablenames_multi' ) ) ) {
						$node_schema -> appendChild ( $dom_table -> importNode ( $node_table, true ) );
						$dom_table -> appendChild ( $node_schema );
					}
				}

				$this -> set ( 'xml_table', $dom_table -> saveXML ( ) );
			}

			$this -> set ( 'tablenames', $tablenames );
			$this -> set ( 'tablenames_multi', $this -> post ( 'tablenames_multi' ) );
		}

		public function backup ( ) {
			Loader :: model ( 'backup', 'webshop' );
			$attachment = '';

			if ( $this -> isPost ( ) ) {
				if ( $attachment = Backup :: tables ( array (
					'webshop_main_categories',
					'webshop_categories',
					'webshop_details',
					'webshop_codes',
					'webshop_voorraad',
				) ) ) {
					$response = Backup :: mail ( $attachment );

					if ( $response ) {
						$this -> set ( 'message', 'De backup is in een bijlage naar je gemaild' );
					}
				}
			}
		}

		public function export ( ) {
			Loader :: model ( 'voorraadbeheer', 'webshop' );
			Loader :: model ( 'export', 'webshop' );

			$attachments = array ( );

			if ( $this -> isPost ( ) ) {
				$codes = array ( Voorraadbeheer :: CODE_OP_VOORRAAD );

				if ( $attachment = Export :: exportVoorraadbeheer ( $codes, 'voorraadbeheer' ) ) {
					$attachments[ ] = $attachment;
				}

				$codes = array (
					Voorraadbeheer :: CODE_PROMOTIE,
					Voorraadbeheer :: CODE_TESTER,
				);

				if ( $attachment = Export :: exportVoorraadbeheer ( $codes, 'promotie_testers' ) ) {
					$attachments[ ] = $attachment;
				}

				$response = Export :: mail ( $attachments );

				if ( $response ) {
					$this -> set ( 'message', 'De export is in een bijlage naar je gemaild' );
					$this -> set ( 'attachments', $attachments );
				}
			}
		}

		public function view ( ) {
			$details = Webshop :: getDetails ( );
			$categories = Webshop :: getCategories ( );

			$this -> set ( 'num_categories', count ( $categories ) );
			$this -> set ( 'num_details', count ( $details ) );
			$this -> set ( 'categories', $categories );
		}

		public function category ( $action, $category_id = false ) {
			$category = new WebshopCategory;

			switch ( $action ) {
				case 'delete':
					$category -> load ( 'category_id = ?', array ( $category_id ) );
					$message = 'Categorie "' . $category -> naam . '" is verwijderd';
					$category -> delete ( );
					$this -> message ( $message, '/dashboard/webshop/' );
					break;

				// Case 'update' falls through to case 'add'
				case 'update':
					$category -> load ( 'category_id = ?', array ( $category_id ) );
				case 'add':
					if ( $this -> isPost ( ) ) {
						if ( $category -> validate ( $this -> post ( ) ) ) {
							$string_helper = Loader :: helper ( 'string' );

							//@formatter:off
							$category -> main_category_id = $this -> post ( 'main_category_id' );
							$category -> naam             = $this -> post ( 'naam' );
							$category -> slug             = $string_helper -> slugify ( $category -> naam );
							$category -> omschrijving     = $this -> post ( 'omschrijving' );
							$category -> imgfile_id       = $this -> post ( 'imgfile_id' );
							$category -> weight           = $this -> post ( 'weight' );
							//@formatter:on

							$category -> save ( );
							$this -> message ( 'De categorie is opgeslagen', '/dashboard/webshop/' );
						}
						else {
							$this -> error -> add ( 'Vul de verplichte velden in' );
						}
					}
					break;
			}

			$this -> set ( 'action', $action );
			$this -> set ( 'category_id', $category_id );
			$this -> set ( 'category', $category );
			$this -> set ( 'main_category_ids', Webshop :: getMainCategoryOptions ( ) );
		}

		public function detail_overview ( $category_id = false ) {
			if ( empty ( $category_id ) ) {
				$this -> message ( 'Geen categorie geselecteerd', '/dashboard/webshop/' );
			}

			$this -> set ( 'category_id', $category_id );
			$this -> set ( 'category', Webshop :: getCategory ( $category_id ) );
			$this -> set ( 'details', Webshop :: getDetails ( $category_id ) );
		}

		public function detail ( $action, $category_id, $detail_id = false ) {
			$detail = new WebshopDetail;
			$btw_tarieven = array (
				'0.06' => 'Laag (6%)',
				'0.21' => 'Hoog (21%)',
				'0.00' => 'Nihil (0%)',
			);

			switch ($action) {
				case 'delete':
					$detail -> load ( 'detail_id = ?', array ( $detail_id ) );
					$detail -> delete ( );
					$this -> message ( 'Het artikel is verwijderd', '/dashboard/webshop/detail_overview/' . $category_id );
					break;

				// Case 'update' falls through to case 'add'
				case 'update':
					$detail -> load ( 'detail_id = ?', array ( $detail_id ) );
				case 'add':
					if ( $this -> isPost ( ) ) {
						if ( $detail -> validate ( $this -> post ( ) ) ) {
							$string_helper = Loader :: helper ( 'string' );

							//@formatter:off
							$detail -> category_id  = $category_id;
							$detail -> naam         = $this -> post ( 'naam' );
							$detail -> slug         = $string_helper -> slugify ( $detail -> naam );
							$detail -> nummer       = $this -> post ( 'nummer' );
							$detail -> omschrijving = $this -> post ( 'omschrijving' );
							$detail -> prijs        = $this -> post ( 'prijs' );
							$detail -> prijs_inkoop = $this -> post ( 'prijs_inkoop' );
							$detail -> btw          = $this -> post ( 'btw' );
							$detail -> imgfile_id   = $this -> post ( 'imgfile_id' );
							$detail -> weight       = $this -> post ( 'weight' );
							$detail -> zichtbaar    = $this -> post ( 'zichtbaar' );
							$detail -> aanbieding   = $this -> post ( 'aanbieding' );
							$detail -> opmerkingen  = $this -> post ( 'opmerkingen' );
							//@formatter:on
							$detail -> save ( );
							$this -> message ( 'Het artikel is opgeslagen', '/dashboard/webshop/detail_overview/' . $category_id );
						}
						else {
							$this -> error -> add ( 'Vul de verplichte velden in' );
						}
					}
					break;
			}

			$this -> set ( 'action', $action );
			$this -> set ( 'category_id', $category_id );
			$this -> set ( 'detail_id', $detail_id );
			$this -> set ( 'detail', $detail );
			$this -> set ( 'btw_tarieven', $btw_tarieven );
		}

		public function message ( $message, $url = '' ) {
			$_SESSION[ 'message' ] = $message;

			if ( !empty ( $url ) ) {
				$this -> redirect ( $url );
			}
		}

	}
?>