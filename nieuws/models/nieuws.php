<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	class Nieuws {

		public static function saveItem ( $postvars, $id = 0 ) {
			$data = array (
				'cName' => $postvars[ 'name' ],
				'cDescription' => $postvars[ 'description' ],
				'cDatePublic' => Loader :: helper ( 'form/date_time' ) -> translate ( 'date_public', $postvars )
			);

			if ( $id ) {
				// update existing page
				$page = Page :: getById ( $id );
				$page -> update ( $data );
			}
			else {
				// create new page
				$page_news_list = Page :: getById ( 159 );
				$page_type = CollectionType :: getByHandle ( 'nieuws_item' );
				$page = $page_news_list -> add ( $page_type, $data );
			}

			self :: saveContent ( $page, $postvars[ 'body' ] );
		}

		public function saveContent ( $page, $content ) {
			$blocks = $page -> getBlocks ( 'Main' );

			foreach ( $blocks as $block ) {
				$block -> deleteBlock ( );
			}

			$blocktype_content = BlockType :: getByHandle ( 'content' );
			$data = array ( 'content' => $content );
			$page -> addBlock ( $blocktype_content, 'Main', $data );
		}

		public static function deleteItem ( $id ) {
			$page = Page :: getById ( $id );
			$page -> delete ( );
		}

		public static function getMostRecentItems ( $num_items = 5 ) {
			Loader :: model ( 'page_list' );

			$page_list = new PageList ( );
			$page_list -> filterByCollectionTypeHandle ( 'nieuws_item' );
			$page_list -> sortByPublicDateDescending ( );

			return $page_list -> get ( $num_items );
		}

	}
?>