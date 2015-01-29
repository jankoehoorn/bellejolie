<?php
	Use Utilities\Registry;
	Use Webshop\IoC;
	Use Webshop\Preferences;
	Use Webshop\DiscountCode;

	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Loader :: model ( 'utilities', 'utilities' );
	Loader :: model ( 'customer', 'customer' );
	Loader :: model ( 'factory', 'webshop' );
	Loader :: model ( 'form', 'form' );
	Loader :: model ( 'inventory_manager', 'webshop' );
	Loader :: model ( 'invoice', 'webshop' );
	Loader :: model ( 'ioc', 'webshop' );
	Loader :: model ( 'kortingcodebeheer', 'webshop' );
	Loader :: model ( 'mollie', 'webshop' );
	Loader :: model ( 'order_processor', 'webshop' );
	Loader :: model ( 'preferences', 'webshop' );
	Loader :: model ( 'printer', 'form' );
	Loader :: model ( 'printer', 'webshop' );
	Loader :: model ( 'discount_code', 'webshop' );

	Interface WebshopCartPrinter {
		public static function printItems ( );
	}

	Class Webshop {
		public static function canCheckDiscountCode ( ) {
			$sql = "
				SELECT COUNT(history_id) AS num_attempts
				FROM webshop_kortingcode_history
				WHERE ip = ?
				AND ( UNIX_TIMESTAMP() - UNIX_TIMESTAMP(doc) ) < ( 900 )
			";
			$bindparams = array ( $_SERVER[ 'REMOTE_ADDR' ] );
			$num_attempts = Loader :: db ( ) -> getOne ( $sql, $bindparams );
			return ($num_attempts < 2);
		}

		public static function checkDiscountCode ( $discount_code ) {
			$DiscountCode = new DiscountCode;
			$DiscountCode -> load ( 'code = ?', array ( $discount_code ) );
			return $DiscountCode;
		}

		public static function ipBlock ( ) {
			if ( !(IP_ADDRESS_JH || IP_ADRESS_MOLLIE) ) {
				echo '<pre>Access to ' . $_SERVER[ 'REMOTE_ADDR' ] . ' denied</pre>';
				exit ;
			}
		}

		public static function clearCache ( ) {
			$files = glob ( $_SERVER[ 'DOCUMENT_ROOT' ] . DIR_REL . '/files/cache/*webshop*.cache' );

			foreach ( $files as $file ) {
				unlink ( $file );
			}
		}

		public static function setCategorySlugs ( ) {
			$categories = self :: getCategories ( );

			foreach ( $categories as $category ) {
				self :: setCategorySlug ( $category );
			}
		}

		public static function setCategorySlug ( WebshopCategory $WebshopCategory ) {
			$string_helper = Loader :: helper ( 'string' );

			$WebshopCategory -> slug = $string_helper -> slugify ( $WebshopCategory -> naam );
			$WebshopCategory -> save ( );
		}

		public static function setArikelenSlugs ( ) {
			$artikelen = self :: getArtikelen ( );

			foreach ( $artikelen as $artikel ) {
				self :: setArtikelSlug ( $artikel );
			}
		}

		public static function setArtikelSlug ( WebshopDetail $WebshopArtikel ) {
			$string_helper = Loader :: helper ( 'string' );

			$WebshopArtikel -> slug = $string_helper -> slugify ( $WebshopArtikel -> naam );
			$WebshopArtikel -> save ( );
		}

		public static function getMainCategory ( $main_category_id ) {
			$main_category = new WebshopMainCategory;
			$main_category -> load ( 'main_category_id = ?', array ( $main_category_id ) );

			return $main_category;
		}

		public static function getMainCategories ( ) {
			$db = Loader :: db ( );
			$records = $db -> GetActiveRecordsClass ( 'WebshopMainCategory', 'webshop_main_categories', '1 ORDER BY weight ASC' );

			return $records;
		}

		public static function getMainCategoryOptions ( ) {
			$options = array ( );
			$main_categories = self :: getMainCategories ( );

			foreach ( $main_categories as $main_category ) {
				$options[ $main_category -> main_category_id ] = $main_category -> naam;
			}

			return $options;
		}

		public static function getCategories ( ) {
			$db = Loader :: db ( );
			$records = $db -> GetActiveRecordsClass ( 'WebshopCategory', 'webshop_categories', '1 ORDER BY weight ASC' );

			return $records;
		}

		public static function getCategoriesBySlug ( $slug ) {
			$db = Loader :: db ( );
			$sql = "
				SELECT
					c.naam,
					c.omschrijving,
					c.imgfile_id,
					c.weight,
					c.slug
				FROM webshop_main_categories mc
				LEFT JOIN webshop_categories c
				ON ( mc.main_category_id = c.main_category_id )
				WHERE mc.slug = ?
				ORDER BY c.weight ASC			
			";
			$bindparams = array ( $slug );
			$rows = $db -> getAll ( $sql, $bindparams );

			return $rows;
		}

		public static function getArtikelen ( ) {
			$db = Loader :: db ( );
			$records = $db -> GetActiveRecordsClass ( 'WebshopDetail', 'webshop_details', '1 ORDER BY weight ASC' );

			return $records;
		}

		public static function getArtikelenBySlug ( $slug ) {
			$db = Loader :: db ( );
			$sql = "
				SELECT
					c.naam AS category_naam,
					d.detail_id,
					d.category_id,
					d.naam AS artikel_naam,
					d.slug,
					d.nummer,
					d.omschrijving,
					d.prijs,
					d.imgfile_id,
					d.opmerkingen
				FROM webshop_categories c
				LEFT JOIN webshop_details d
				ON ( c.category_id = d.category_id )
				WHERE c.slug = ?
				AND d.zichtbaar = 1
				ORDER BY d.weight ASC			
			";
			$bindparams = array ( $slug );
			$rows = $db -> getAll ( $sql, $bindparams );

			return $rows;
		}

		public static function getArtikelBySlug ( $slug ) {
			$db = Loader :: db ( );
			$sql = "
				SELECT
					c.naam AS category_naam,
					d.detail_id,
					d.category_id,
					d.naam AS artikel_naam,
					d.nummer,
					d.omschrijving,
					d.prijs,
					d.btw,
					d.imgfile_id,
					d.opmerkingen
				FROM webshop_categories c
				LEFT JOIN webshop_details d
				ON ( c.category_id = d.category_id )
				WHERE d.slug = ?
				AND d.zichtbaar = 1
				ORDER BY d.weight ASC
				LIMIT 1			
			";
			$bindparams = array ( $slug );
			$row = $db -> getRow ( $sql, $bindparams );

			return $row;
		}

		public static function getCategory ( $category_id ) {
			$category = new WebshopCategory;
			$category -> load ( 'category_id = ?', array ( $category_id ) );

			return $category;
		}

		public static function getDetails ( $category_id = false ) {
			$db = Loader :: db ( );

			if ( $category_id ) {
				$records = $db -> GetActiveRecordsClass ( 'WebshopDetail', 'webshop_details', 'category_id = ? ORDER BY weight ASC', array ( $category_id ) );
			}
			else {
				$records = $db -> GetActiveRecordsClass ( 'WebshopDetail', 'webshop_details', '1 ORDER BY weight ASC' );
			}

			return $records;
		}

		public static function getDetail ( $detail_id ) {
			$detail = new WebshopDetail;
			$detail -> load ( 'detail_id = ?', array ( $detail_id ) );

			return $detail;
		}

		public static function SaveOrderBy ( $table, $ids ) {
			$db = Loader :: db ( );
			$ids = explode ( ',', $ids );
			$num_details = count ( $ids );
			$weight = 0;
			$colname_id = array (
				'webshop_categories' => 'category_id',
				'webshop_details' => 'detail_id',
			);

			for ( $i = 0; $i < $num_details; $i++ ) {
				$sql = "
					UPDATE " . $table . "
					SET weight = ?
					WHERE " . $colname_id[ $table ] . " = ?
				";
				$bindparams = array (
					$weight,
					$ids[ $i ]
				);
				$db -> execute ( $sql, $bindparams );
				$weight += 100;
			}
		}

	}

	Class WebshopMainCategory Extends ADOdb_Active_Record {
		public $_table = 'webshop_main_categories';

		public static function getById ( $main_category_id ) {
			$WebshopMainCategory = new WebshopMainCategory;
			$WebshopMainCategory -> load ( 'main_category_id = ?', array ( $main_category_id ) );

			return $WebshopMainCategory;
		}

	}

	Class WebshopCategory Extends ADOdb_Active_Record {
		public $_table = 'webshop_categories';
		public $required = array ( 'naam' );

		public function validate ( $postvars ) {
			foreach ( $this -> required as $fieldname ) {
				if ( empty ( $postvars[ $fieldname ] ) ) {
					return false;
				}
			}

			return true;
		}

	}

	Class WebshopDetail Extends ADOdb_Active_Record {
		public $_table = 'webshop_details';
		public $required = array (
			'naam',
			'nummer',
			'prijs',
		);

		public function validate ( $postvars ) {
			foreach ( $this -> required as $fieldname ) {
				if ( empty ( $postvars[ $fieldname ] ) ) {
					return false;
				}
			}

			return true;
		}

		public static function getOffers ( ) {
			$sql = "
				SELECT
					d.naam,
					d.imgfile_id,
					d.omschrijving,
					(
						SELECT slug
						FROM webshop_main_categories
						WHERE main_category_id = c.main_category_id
						LIMIT 1
					) AS main_category_slug,
					c.slug AS category_slug,
					d.slug AS detail_slug
				FROM webshop_details AS d
				LEFT JOIN webshop_categories AS c ON ( d.category_id = c.category_id )
				WHERE d.zichtbaar = 1
				AND d.aanbieding = 1
			";
			return Loader :: db ( ) -> getAll ( $sql );
		}

	}

	Class WebshopCode Extends ADOdb_Active_Record {
		public $_table = 'webshop_codes';
	}

	Class WebshopCart Extends ADOdb_Active_Record {
		public function setDiscount ( $discount ) {
			if ( empty ( $_SESSION[ 'discount' ] ) ) {
				$_SESSION[ 'discount' ] = $discount;
			}
		}

		public function setDiscountCode ( $discount_code ) {
			if ( empty ( $_SESSION[ 'discount_code' ] ) ) {
				$_SESSION[ 'discount_code' ] = $discount_code;
			}
		}

		public function getAmount ( ) {
			$tot = 0;
			$btw = 0;

			$preferences = IoC :: make ( 'Preferences' );

			foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
				$detail = Webshop :: getDetail ( $detail_id );
				$category = Webshop :: getCategory ( $detail -> category_id );
				$properties = (object)$properties;
				$num_artikelen += $properties -> qty;
				$subtot = $detail -> prijs * $properties -> qty;
				$subbtw = round ( $subtot * $detail -> btw, 2 );
				$tot += $subtot;
				$btw += $subbtw;
			}

			return (round ( $tot + $btw + $preferences -> verzendkosten, 2 ));
		}

		public function emptyCart ( ) {
			unset ( $_SESSION[ 'cart' ] );
		}

		public function addItem ( $detail_id ) {
			if ( empty ( $_SESSION[ 'cart' ][ $detail_id ] ) ) {
				$_SESSION[ 'cart' ][ $detail_id ][ 'qty' ] = 1;
			}
			else {
				$_SESSION[ 'cart' ][ $detail_id ][ 'qty' ]++;
			}
		}

		public function removeItem ( $detail_id ) {
			unset ( $_SESSION[ 'cart' ][ $detail_id ] );
		}

		public function countItems ( ) {
			if ( empty ( $_SESSION[ 'cart' ] ) ) {
				return 0;
			}

			$qty = 0;

			foreach ( $_SESSION['cart'] as $detail ) {
				$qty += $detail[ 'qty' ];
			}

			return $qty;
		}

		public function setQty ( $detail_id, $value ) {
			$_SESSION[ 'cart' ][ $detail_id ][ 'qty' ] = $value;
		}

		public function increaseQty ( $detail_id ) {
			$_SESSION[ 'cart' ][ $detail_id ][ 'qty' ]++;
		}

		public function decreaseQty ( $detail_id ) {
			$_SESSION[ 'cart' ][ $detail_id ][ 'qty' ]--;

			if ( $_SESSION[ 'cart' ][ $detail_id ][ 'qty' ] == 0 ) {
				unset ( $_SESSION[ 'cart' ][ $detail_id ] );
			}
		}

	}

	Class WebshopCartFrontEndPrinter Implements WebshopCartPrinter {
		public static function printItems ( ) {
			if ( is_array ( $_SESSION[ 'cart' ] ) ) {
				$tot = 0;

				echo PHP_EOL;
				echo '
					<table class="cart">
						<thead>
							<tr>
								<th>Naam</th>
								<th class="qty">Aantal</th>
								<th class="price">Subtot</th>
							</tr>
						</thead>
						<tbody>
						';

				foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
					$detail = Webshop :: getDetail ( $detail_id );
					$subtot = number_format ( $detail -> prijs * $properties[ 'qty' ], 2, ',', '.' );
					$tot += $subtot;

					echo PHP_EOL;
					echo '
						<tr>
							<td>' . $detail -> naam . '</td>
							<td class="qty">' . $properties[ 'qty' ] . '</td>
							<td class="price">€ ' . $subtot . '</td>
						</tr>
						';
				}

				$tot = number_format ( $tot, 2, ',', '.' );

				echo '
					</tbody>
					<tfoot>
						<tr>
							<td></td>
							<td class="qty">Tot</td>
							<td class="price">€ ' . $tot . '</td>
						</tr>
					</tfoot>
					</table>
				';
			}
			else {
				echo '<p>Nog geen items in uw winkelwagentje</p>';
			}

		}

	}

	Class WebshopCartEmailHTMLPrinter Implements WebshopCartPrinter {
		public static function printItems ( ) {
			$body = '
				<table style="width: 700px; font: 12px Verdana; border-collapse: separate; border-spacing: 1px; border: 1px dotted #ccc;">
					<thead>
						<tr>
							<th style="padding: 15px 15px 15px 15px; text-align: left; vertical-align: top;">Categorie</th>
							<th style="padding: 15px 15px 15px 15px; text-align: left; vertical-align: top;">Naam</th>
							<th style="padding: 15px 15px 15px 15px; text-align: right; vertical-align: top;">Aantal</th>
							<th style="padding: 15px 15px 15px 15px; text-align: right; vertical-align: top;">Subtot</th>
						</tr>
					</thead>
					<tbody>
			';

			foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
				$detail = Webshop :: getDetail ( $detail_id );
				$category = Webshop :: getCategory ( $detail -> category_id );
				// TODO: CORRECT CALCULATIONS
				$subtot = number_format ( $detail -> prijs * $properties[ 'qty' ], 2, ',', '.' );
				$tot += $subtot;

				$body .= '
					<tr>
						<td style="padding: 15px 15px 15px 15px; text-align: left; vertical-align: top;">' . $category -> naam . '</td>
						<td style="padding: 15px 15px 15px 15px; text-align: left; vertical-align: top;">' . $detail -> naam . '</td>
						<td style="padding: 15px 15px 15px 15px; text-align: right; vertical-align: top;">' . $properties[ 'qty' ] . '</td>
						<td style="padding: 15px 15px 15px 15px; text-align: right; vertical-align: top;">&euro; ' . $subtot . '</td>
					</tr>
				';
			}

			$tot = number_format ( $tot, 2, ',', '.' );

			$body .= '
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td style="padding: 15px 15px 15px 15px; text-align: right; vertical-align: top;">Tot</td>
						<td style="padding: 15px 15px 15px 15px; text-align: right; vertical-align: top;">&euro; ' . $tot . '</td>
					</tr>
				</tfoot>
				</table>
			';

			return $body;
		}

	}

	Class WebshopCartEmailPlainTextPrinter Implements WebshopCartPrinter {
		public static function printItems ( ) {
			$body = '';

			foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
				$detail = Webshop :: getDetail ( $detail_id );
				$category = Webshop :: getCategory ( $detail -> category_id );

				$body .= PHP_EOL;
				$body .= $properties[ 'qty' ];
				$body .= PHP_EOL;
				$body .= $category -> naam;
				$body .= PHP_EOL;
				$body .= $detail -> naam;
				$body .= PHP_EOL;
			}

			return $body;
		}

	}
?>