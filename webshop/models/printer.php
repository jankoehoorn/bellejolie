<?php
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Use Utilities\Registry;
	Use Utilities\StringMethods;
	Use Utilities\Request;
	Use Utilities\PDF;
	Use Webshop\IoC;
	Use Webshop\Preferences;

	Loader :: model ( 'webshop', 'webshop' );

	Interface WebshopPrinterInterface {

	}

	Class WebshopPrinter Implements WebshopPrinterInterface {
		public $registry;
		public $db;
		public $stringhelper;
		public $imagehelper;

		public function setRegistry ( Registry $Registry ) {
			$this -> registry = $Registry;
		}

		public function setDb ( $db ) {
			$this -> db = $db;
		}

		public function setStringHelper ( $StringHelper ) {
			$this -> stringhelper = $StringHelper;
		}

		public function setImageHelper ( $ImageHelper ) {
			$this -> imagehelper = $ImageHelper;
		}

		public function printOffers ( ) {
			$offers = WebshopDetail :: getOffers ( );
			$num_offers = count ( $offers );

			if ( $num_offers == 0 ) {
				return null;
			}

			echo '<div id="offers" class="clearfix">';

			if ( $num_offers == 1 ) {
				echo '<h1>Aanbieding</h1>';
			}
			else {
				echo '<h1>Aanbiedingen</h1>';
			}

			foreach ( $offers as $offer ) {
				$this -> printOffer ( $offer );
			}
			echo '</div>';
		}

		public function printOffer ( $offer ) {
			$offer = (object)$offer;

			printf ( '<a href="/webshop/%s/%s/%s/">', $offer -> main_category_slug, $offer -> category_slug, $offer -> detail_slug );
			echo '<div class="offer">';
			printf ( '<h2>%s</h2>', $offer -> naam );
			printf ( '<div class="omschrijving">%s</div>', $offer -> omschrijving );
			$this -> printThumbnail ( $offer -> imgfile_id, 200 );
			echo '</div>';
			echo '</a>';
		}

		public function printHeader ( ) {
			echo '<div id="header">';
			$this -> printTeasers ( );
			echo '<div id="cart-icon"><a href="/bestelling-afronden/"></a></div>';
			echo '<div id="cart">';
			$this -> printCartHeader ( );

			echo '
					</div>
					<div id="logo"><a href="/" title="Home"></a></div>
					<h1><a href="/" title="Home">' . $this -> registry -> page -> name . '</a></h1>
			';

			$this -> printMainCategories ( );
			$this -> printBreadCrumbs ( );

			echo '
				</div>
			';
		}

		public function printMessageHomepage ( ) {
			// msg_homepage
			$preferences = IoC :: make ( 'Preferences' );

			if ( !empty ( $preferences -> msg_homepage ) ) {
				printf ( '<div class="home msg">%s</div>', $preferences -> msg_homepage );
			}
		}

		public function printPackingSlip ( ) {
			$num_artikelen = 0;
			$html = $this -> getPDFStyle ( );
			$html .= '
				<h1>Pakbon</h1>
				<h2>Bellejolie</h2>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				
				<table cellpadding="5">
				<tr>
					<th>Product</th>
					<th></th>
					<th>Aantal</th>
				</tr>
			';

			foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
				$detail = Webshop :: getDetail ( $detail_id );
				$category = Webshop :: getCategory ( $detail -> category_id );
				$properties = (object)$properties;
				$num_artikelen += $properties -> qty;

				$html .= '
					<tr>
						<td>' . $category -> naam . '</td>
						<td>' . $detail -> naam . '</td>
						<td>' . $properties -> qty . '</td>
					</tr>
				';
			}

			$html .= '
				<tr>
					<td></td>
					<td>Totaal aantal artikelen</td>
					<td>' . $num_artikelen . '</td>
				</tr>
				</table>
			';

			return $html;
		}

		public function getPDFStyle ( ) {
			$style = '
				<style>
					h1 { font-family: Verdana; font-size: 72px; line-height: 72px; margin-top: 0px; margin-bottom: 0px; color: #48CCCD; }
					h2 { font-family: Verdana; font-size: 36px; color: #CD47CC; }
					table { border: 1px solid #EEEEEE; }
					p { font-family: Verdana; font-size: 8px; line-height: 10px; color: #666666; }
					th { font-family: Verdana; font-size: 8px; color: #333333; border-bottom: 1px solid #EEEEEE; }
					td { font-family: Verdana; font-size: 8px; color: #666666; border-bottom: 1px solid #EEEEEE; }
					td.right { text-align: right; }
				</style>
			';
			return $style;
		}

		public function printInvoice ( $Customer, $Invoice ) {
			$fullname = implode ( ' ', array (
				$Customer -> factuurvoornaam,
				$Customer -> factuurtussenvoegsel,
				$Customer -> factuurachternaam,
			) );

			$bezorgadres = '';

			if ( $Customer -> two_addresses == 1 ) {
				$cadeauservice = '';

				if ( $Customer -> cadeauservice == 1 ) {
					$cadeauservice = '<br />Tekst voor cadeaukaart: ' . $Customer -> tekst_cadeaukaart;
				}

				$bezorgadres = '
					<p style="font-size: 10px; line-height: 14px;">Bezorgadres: <br />
					' . $Customer -> bezorgadres . '<br />
					' . $Customer -> bezorgpostcode . ' ' . $Customer -> bezorgwoonplaats . '
					' . $cadeauservice . '
					</p>
				';
			}

			$factuurnummer = sprintf ( 'W%04d%04d', $Invoice -> jaar, $Invoice -> nummer );

			$html = $this -> getPDFStyle ( );
			$html .= '
				<h1>Factuur</h1>
				<h2>Bellejolie</h2>
				<table cellpadding="4">
					<tr>
						<td style="width: 270px;">
							<p style="font-size: 10px; line-height: 14px;">
							Factuurnummer: ' . $factuurnummer . '<br />
							Klantnummer: ' . $Customer -> klantnummer . '<br />
							Datum: ' . strftime ( '%F' ) . '<br />
							Aan: ' . $fullname . '<br />
							' . $Customer -> factuuradres . '<br />
							' . $Customer -> factuurpostcode . ' ' . $Customer -> factuurwoonplaats . ' </p>
							' . $bezorgadres . '
						</td>
						<td style="width: 120px;">
							<p style="color: #48CCCD;">
							Bellejolie<br>
							Van der Woudestraat 65<br>
							1815 VV  Alkmaar<br>
							NL76ABNA 0556 2081 53<br>
							KvK: 60591862<br>
							BTWnr: 1182.89.202.B.02
							</p>
						</td>
						<td style="width: 120px;">
							<p style="color: #48CCCD;">
							T:06-22922101<br>
							W: www.bellejolie.nl<br>
							E: info@bellejolie.nl<br>
							</p>
						</td>
					</tr>
				</table>

				<table cellpadding="4">
					<tr>
						<th style="width: 110px;">Product</th>
						<th style="width: 300px;"></th>
						<th style="width: 50px;">Aantal</th>
						<th style="width: 50px;">Prijs</th>
					</tr>
			';

			$tot = 0;
			$btw = 0;

			$preferences = IoC :: make ( 'Preferences' );
			$verzendkosten = $preferences -> verzendkosten;

			foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
				$detail = Webshop :: getDetail ( $detail_id );
				$category = Webshop :: getCategory ( $detail -> category_id );
				$properties = (object)$properties;
				$num_artikelen += $properties -> qty;
				$subtot = $detail -> prijs * $properties -> qty;
				$subbtw = round ( $subtot * $detail -> btw, 2 );
				$tot += $subtot;
				$btw += $subbtw;

				$html .= '
					<tr>
						<td>' . $category -> naam . '</td>
						<td>' . $detail -> naam . '</td>
						<td>' . $properties -> qty . '</td>
						<td style="text-align: right;">' . StringMethods :: formatEuro ( $subtot ) . '</td>
					</tr>
				';
			}

			if ( ($tot + $btw) > 50 ) {
				$verzendkosten = 0;
			}

			$html .= '
					<tr>
						<td colspan="4"></td>
					</tr>
					<tr>
						<td></td>
						<td class="right" colspan="2">Totaal ex BTW:</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $tot ) . '</td>
					</tr>
					<tr>
						<td></td>
						<td class="right" colspan="2">BTW:</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $btw ) . '</td>
					</tr>
					<tr>
						<td></td>
						<td class="right" colspan="2">Verzendkosten:</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $verzendkosten ) . '</td>
					</tr>
			';

			if ( empty ( $_SESSION[ 'discount' ] ) ) {
				$html .= '
					<tr>
						<td></td>
						<td class="right" colspan="2">Totaal:</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $tot + $btw + $verzendkosten ) . '</td>
					</tr>
				';
			}
			else {
				$tot_excl_discount = $tot + $btw + $verzendkosten;
				$tot_incl_discount = $tot_excl_discount - $_SESSION[ 'discount' ];
				$html .= '
					<tr>
						<td></td>
						<td class="right" colspan="2">SubTot:</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $tot_excl_discount ) . '</td>
					</tr>
					<tr>
						<td></td>
						<td class="right" colspan="2">Korting (' . $_SESSION[ 'discount_code' ] . '):</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $_SESSION[ 'discount' ] ) . '</td>
					</tr>
					<tr>
						<td></td>
						<td class="right" colspan="2">Totaal:</td>
						<td style="font-weight: bold; text-align: right;">' . StringMethods :: formatEuro ( $tot_incl_discount ) . '</td>
					</tr>
				';
			}

			$html .= '
				</table>
			';

			return $html;
		}

		public function printBreadCrumbs ( ) {
			$breadcrumbs = array ( );

			$breadcrumb = new stdClass;
			$breadcrumb -> href = '/';
			$breadcrumb -> text = 'Home';
			$breadcrumbs[ ] = $breadcrumb;

			if ( !empty ( $this -> registry -> main_category ) ) {
				$breadcrumb = new stdClass;
				$path_prev = $breadcrumb -> href = '/webshop/' . $this -> registry -> main_category . '/';
				$breadcrumb -> text = StringMethods :: deSlugify ( $this -> registry -> main_category );
				$breadcrumbs[ ] = $breadcrumb;
			}

			if ( !empty ( $this -> registry -> category ) ) {
				$breadcrumb = new stdClass;
				$breadcrumb -> href = $path_prev . $this -> registry -> category . '/';
				$breadcrumb -> text = StringMethods :: deSlugify ( $this -> registry -> category );
				$breadcrumbs[ ] = $breadcrumb;
			}

			if ( !empty ( $this -> registry -> artikel ) ) {
				$breadcrumb = new stdClass;
				$breadcrumb -> href = $this -> registry -> artikel;
				$breadcrumb -> text = $this -> registry -> artikel;
				$breadcrumbs[ ] = $breadcrumb;
			}

			$num_breadcrumb_links = count ( $breadcrumbs ) - 1;

			echo '<div id="breadcrumbs">';
			echo '<span>U bent hier: </span>';

			foreach ( $breadcrumbs as $i => $breadcrumb ) {
				if ( $i < $num_breadcrumb_links ) {
					echo '<a href="' . $breadcrumb -> href . '">' . $breadcrumb -> text . '</a>';
					echo '<span> &raquo; </span>';
				}
				else {
					echo '<span>' . $breadcrumb -> text . '</span>';
				}
			}

			echo '</div>';
		}

		public function printCartHeader ( ) {
			$qty = WebshopCart :: countItems ( );
			$text = ($qty == 1) ? ('1 artikel in winkelmandje') : ($qty . ' artikelen in winkelmandje');

			echo '<a href="/bestelling-afronden/">' . $text . '</a>';
		}

		public function printThumbnail ( $imgfile_id, $width = 50 ) {
			$imgfile = File :: getById ( $imgfile_id );

			if ( !$imgfile -> isError ( ) ) {
				$this -> imagehelper -> outputThumbnail ( $imgfile, $width, 9999 );
			}
		}

		public function printCartCompleteOrderContainer ( ) {
			echo '<div id="cart-container">';
			$this -> printCartCompleteOrder ( );
			echo '</div>';
		}

		public function printCartCompleteOrder ( ) {
			if ( is_array ( $_SESSION[ 'cart' ] ) ) {
				// TODO: check this method
				KortingCodeBeheer :: checkCode ( );
				//@formatter:off
				$tot_excl_btw  = 0;
				$tot_btw       = 0;
				$tot_incl_btw  = 0;
				$discount_class = (empty ( $_SESSION[ 'discount' ] )) ? ('') : ('discount');
				//@formatter:on

				echo PHP_EOL;
				echo '
					<h2>Uw bestelling</h2>
					<table class="cart">
						<thead>
							<tr>
								<th class="img">Afbeelding</th>
								<th class="category">Categorie</th>
								<th class="naam">Naam</th>
								<th class="qty">Aantal</th>
								<th class="buttons">Aanpassen</th>
								<th class="price">Subtot ex BTW</th>
								<th class="price">BTW</th>
								<th class="price">Subtot incl BTW</th>
							</tr>
						</thead>
						<tbody>
						';

				foreach ( $_SESSION['cart'] as $detail_id => $properties ) {
					//@formatter:off
					$detail   = Webshop :: getDetail ( $detail_id );
					$category = Webshop :: getCategory ( $detail -> category_id );
					$subtot   = $detail -> prijs * $properties[ 'qty' ];
					$btw      = round ( $subtot * $detail -> btw, 2 );
					$subtot_incl_btw = $subtot + $btw;
					//@formatter:on
					$tot_excl_btw += $subtot;
					$tot_btw += $btw;
					$tot_incl_btw += $subtot_incl_btw;

					echo PHP_EOL;
					echo '
						<tr>
							<td class="img">
					';
					$this -> printThumbnail ( $detail -> imgfile_id, 25 );
					echo '
							</td>
							<td class="category">' . $category -> naam . '</td>
							<td class="naam">' . $detail -> naam . '</td>
							<td class="qty">' . $properties[ 'qty' ] . '</td>
							<td>
								<input type="button" class="decrease" value="-" id="' . $detail -> detail_id . '" />
								<input type="button" class="increase" value="+" id="' . $detail -> detail_id . '" />
							</td>
							<td class="price">' . StringMethods :: formatEuro ( $subtot ) . '</td>
							<td class="price">' . StringMethods :: formatEuro ( $btw ) . '</td>
							<td class="price">' . StringMethods :: formatEuro ( $subtot_incl_btw ) . '</td>
						</tr>
						';
				}

				$has_verzendkosten = ($tot_incl_btw <= 50);
				$has_discount = !empty ( $_SESSION[ 'discount' ] );

				echo '
					</tbody>
					<tfoot>
						<tr class="totaal ' . $discount . '">
							<td class="img"></td>
							<td class="category"></td>
							<td class="naam"></td>
							<td class="qty"></td>
							<td class="buttons">SubTot</td>
							<td class="price">' . StringMethods :: formatEuro ( $tot_excl_btw ) . '</td>
							<td class="price">' . StringMethods :: formatEuro ( $tot_btw ) . '</td>
							<td class="price">' . StringMethods :: formatEuro ( $tot_incl_btw ) . '</td>
						</tr>
				';

				if ( $has_verzendkosten ) {
					$tot_incl_btw += $this -> registry -> preferences -> verzendkosten;

					echo '
						<tr class="totaal ' . $discount . '">
							<td class="img"></td>
							<td class="category"></td>
							<td class="naam"></td>
							<td class="qty"></td>
							<td class="buttons">Verzendkosten</td>
							<td class="price"></td>
							<td class="price"></td>
							<td class="price">' . StringMethods :: formatEuro ( $this -> registry -> preferences -> verzendkosten ) . '</td>
						</tr>';
				}

				$txt_tot_subtot = ($has_discount) ? ('SubTot') : ('Totaal');

				if ( $has_verzendkosten ) {
					echo '
						<tr>
							<td class="img"></td>
							<td class="category"></td>
							<td class="naam"></td>
							<td class="qty"></td>
							<td class="buttons">' . $txt_tot_subtot . '</td>
							<td class="price"></td>
							<td class="price"></td>
							<td class="price">' . StringMethods :: formatEuro ( $tot_incl_btw ) . '</td>
						</tr>
					';
				}

				if ( $has_discount ) {
					$tot_incl_btw -= $_SESSION[ 'discount' ];
					echo '
						<tr>
							<td class="img"></td>
							<td class="category"></td>
							<td class="naam"></td>
							<td class="qty"></td>
							<td class="buttons">Korting</td>
							<td class="price"></td>
							<td class="price"></td>
							<td class="price">' . StringMethods :: formatEuro ( $_SESSION[ 'discount' ] ) . '</td>
						</tr>
						<tr>
							<td class="img"></td>
							<td class="category"></td>
							<td class="naam"></td>
							<td class="qty"></td>
							<td class="buttons">Totaal</td>
							<td class="price"></td>
							<td class="price"></td>
							<td class="price">' . StringMethods :: formatEuro ( $tot_incl_btw ) . '</td>
						</tr>
					';
				}

				echo '
					</tfoot>
					</table>
				';

				if ( empty ( $_SESSION[ 'discount' ] ) ) {
					echo '
						<p>Heeft u een cadeaubon? Vul hier de code in</p>
						<input type="text" id="discount-code" value="" />
						<input type="button" id="check-discount-code" value="controleer code" />';
				}

				echo '
					<input type="button" id="button-order" value="bestellen" />
					<p id="discount-code-msg"></p>
				';
			}
			else {
				echo '<p>Nog geen items in uw winkelwagentje</p>';
			}
		}

		public function printMainCategories ( ) {
			$main_categories = Webshop :: getMainCategories ( );

			echo '
				<div id="menu">
					<ul class="clearer">
			';

			foreach ( $main_categories as $main_category ) {
				$class = '';

				if ( $main_category -> slug == $this -> registry -> main_category ) {
					$class = 'active';
				}

				echo '
					<li><a class="' . $class . '" href="/webshop/' . $main_category -> slug . '/">' . $main_category -> naam . '</a></li>
				';
			}

			echo '
					</ul>
				</div>
			';
		}

		public function printCategories ( ) {
			$categories = Webshop :: getCategoriesBySlug ( $this -> registry -> main_category );

			echo '<div id="category-overview" class="overview">';
			echo '<ul class="clearfix">';

			foreach ( $categories as $category ) {
				$category = (object)$category;
				$img = File :: getById ( $category -> imgfile_id );

				echo '<li>';
				echo '<a href="/webshop/' . $this -> registry -> main_category . '/' . $category -> slug . '/">';
				echo '<h3>' . $category -> naam . '</h3>';
				echo '<p>' . $category -> omschrijving . '</p>';

				if ( !$img -> isError ( ) ) {
					$this -> imagehelper -> outputThumbnail ( $img, 240, 9999 );
				}

				echo '</a>';
				echo '</li>';
			}

			echo '</ul>';
			echo '</div>';
		}

		public function printArtikelen ( ) {
			$artikelen = Webshop :: getArtikelenBySlug ( $this -> registry -> category );

			echo '<div id="artikel-overview" class="overview">';
			echo '<ul class="clearfix">';

			foreach ( $artikelen as $artikel ) {
				$artikel = (object)$artikel;
				$img = File :: getById ( $artikel -> imgfile_id );

				echo '<li>';
				echo '<a href="/webshop/' . $this -> registry -> main_category . '/' . $this -> registry -> category . '/' . $artikel -> slug . '/">';
				echo '<h3>' . $artikel -> artikel_naam . '</h3>';

				if ( !$img -> isError ( ) ) {
					$this -> imagehelper -> outputThumbnail ( $img, 240, 9999 );
				}

				echo '<div class="omschrijving">' . StringMethods :: getFirstWords ( $artikel -> omschrijving, 120 ) . '</div>';
				echo '</a>';
				echo '</li>';
			}

			echo '</ul>';
			echo '</div>';
		}

		public function printArtikel ( ) {
			$artikel = ( object )Webshop :: getArtikelBySlug ( $this -> registry -> artikel );
			$btw = round ( $artikel -> prijs * $artikel -> btw, 2 );
			$img = File :: getById ( $artikel -> imgfile_id );

			echo '<div id="artikel-detail" class="detail clearfix">';
			echo '<h2>' . $artikel -> category_naam . ' &ndash; ' . $artikel -> artikel_naam . '</h2>';

			if ( !$img -> isError ( ) ) {
				$this -> imagehelper -> outputThumbnail ( $img, 480, 9999 );
			}

			echo '<input type="button" id="add_to_cart" value="in winkelmandje" detail_id="' . $artikel -> detail_id . '" />';
			echo '<div class="omschrijving">' . $artikel -> omschrijving . '</div>';
			echo '<p class="price">' . StringMethods :: formatEuro ( $artikel -> prijs + $btw ) . '</p>';

			if ( !empty ( $artikel -> opmerkingen ) ) {
				echo '<p class="remark">(' . $artikel -> opmerkingen . ')</p>';
			}

			echo '<p class="level up"><a href="../">&laquo; Terug</a></p>';
			echo '</div>';
		}

		public function printTeasers ( ) {
			echo '
				<div id="teasers">
					<p>
						<span>Snelle levering</span>
						<span> • </span>
						<span>Veilig online betalen</span>
						<span> • </span>
						<span>Gratis verzending vanaf € 50,-</span>
					</p>
				</div>
			';
		}

		public function printCategoryCarousel ( ) {
			$categories = Webshop :: getCategories ( );

			echo '<div id="category-carousel-wrapper">';
			echo '<ul id="category-carousel">';

			foreach ( $categories as $category ) {
				$main_category = Webshop :: getMainCategory ( $category -> main_category_id );
				$imgfile = File :: getById ( $category -> imgfile_id );
				$href = $main_category -> slug . '/' . $category -> slug . '/';

				echo '<li>';
				echo '<a href="/webshop/' . $href . '" title="Bekijk de categorie &quot;' . $category -> naam . '&quot;">';

				if ( !$imgfile -> isError ( ) ) {
					$this -> imagehelper -> outputThumbnail ( $imgfile, 9999, 140 );
				}

				echo '</a>';
				echo '</li>';
			}

			echo '</ul>';
			echo '</div>';
		}

		public function printCustomerForm ( ) {

		}

		public function printIntro ( ) {
			echo '<div id="intro">';
			$a = new Area ( 'Main' );
			$a -> display ( $this -> registry -> page );
			echo '</div>';
		}

		public function printSpecial ( ) {
			echo '<div id="special">';
			$a = new Area ( 'Special' );
			$a -> display ( $this -> registry -> page );
			echo '</div>';
		}

	}
?>