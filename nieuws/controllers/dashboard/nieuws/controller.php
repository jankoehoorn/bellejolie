<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class DashboardNieuwsController extends DashboardBaseController {

		public $helpers = array (
			'html',
			'form'
		);

		public function on_start ( ) {
			Loader :: model ( 'nieuws', 'nieuws' );
			Loader :: model ( 'page_list' );
			$this -> error = Loader :: helper ( 'validation/error' );

			$this -> set ( 'pane_title', 'Nieuws' );
			$this -> set ( 'pane_help', 'Package voor nieuwsberichten' );
			$this -> set ( 'pane_css_class', false );
			$this -> set ( 'pane_standard_body', true );
			$this -> set ( 'pane_contextmenu_pages', array ( Page :: getByPath ( '/dashboard/nieuws' ) ) );
			$this -> set ( 'pane_up_to_page', Page :: getByPath ( '/dashboard' ) );

			$this -> set ( 'var_dump', true );

			$this -> addFooterItem ( Loader :: helper ( 'html' ) -> javascript ( '/packages/nieuws/js/nieuws.js' ) );
		}

		public function view ( ) {
			$page_list = new PageList ( );

			$page_list -> filterByCollectionTypeHandle ( 'nieuws_item' );
			$page_list -> sortByPublicDateDescending ( );

			$this -> set ( 'pages', $page_list -> get ( ) );
		}

		public function add ( ) {
			$this -> form_item_init ( );

			if ( $this -> isPost ( ) ) {
				$this -> validate_item ( );

				if ( !$this -> error -> has ( ) ) {
					Nieuws :: saveItem ( $this -> post ( ) );
					$this -> redirect ( '/dashboard/nieuws', 'item_added' );
				}
				else {
					$this -> set ( 'errors', $this -> error -> getList ( ) );
				}
			}
		}

		public function update ( $id ) {
			$this -> form_item_init ( Page :: getById ( $id ) );

			if ( $this -> isPost ( ) ) {
				$this -> validate_item ( );

				if ( !$this -> error -> has ( ) ) {
					Nieuws :: saveItem ( $this -> post ( ), $id );
					$this -> redirect ( '/dashboard/nieuws', 'item_updated' );
				}
			}
		}

		public function delete ( $id ) {
			Nieuws :: deleteItem ( $id );
			$this -> redirect ( '/dashboard/nieuws', 'item_deleted' );
		}

		public function form_item_init ( $page = false ) {
			$this -> addHeaderItem ( Loader :: helper ( 'html' ) -> javascript ( 'tiny_mce/tiny_mce.js' ) );

			if ( !$page ) {
				// create new item
				$this -> set ( 'id', 0 );
				$this -> set ( 'name', 'titel' );
				$this -> set ( 'description', 'beschrijving/inleiding' );
				$this -> set ( 'body', '<p>Hier de tekst van het nieuwsitem</p>' );
			}
			else {
				// update existing item
				$blocks = $page -> getBlocks ( 'Main' );

				// if nothing found, getBlocks () returns an empty array
				if ( !empty ( $blocks ) ) {
					$body = $blocks[ 0 ] -> getInstance ( ) -> getContent ( );
				}
				else {
					$body = '<p>Hier de tekst van het nieuwsitem</p>';
				}

				$this -> set ( 'id', $page -> getCollectionId ( ) );
				$this -> set ( 'name', $page -> getCollectionName ( ) );
				$this -> set ( 'date_public', $page -> getCollectionDatePublic ( ) );
				$this -> set ( 'description', $page -> getCollectionDescription ( ) );
				$this -> set ( 'body', $body );
			}
		}

		public function validate_item ( ) {
			$helper_validation_strings = Loader :: helper ( 'validation/strings' );

			if ( !$helper_validation_strings -> notempty ( $this -> post ( 'name' ) ) ) {
				$this -> error -> add ( 'Geef een titel op' );
			}

			$this -> set ( 'error', $this -> error );
		}

		public function item_added ( ) {
			$this -> set ( 'message', 'Item toegevoegd' );
			$this -> view ( );
		}

		public function item_updated ( ) {
			$this -> set ( 'message', 'Item opgeslagen' );
			$this -> view ( );
		}

		public function item_deleted ( ) {
			$this -> set ( 'message', 'Item verwijderd' );
			$this -> view ( );
		}

	}
?>