<?php
	session_start ( );
	defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

	Use \Utilities\Debug;
	Use \Utilities\Request;
	Use \Utilities\Session;

	Loader :: model ( 'mail', 'webshop' );
	Loader :: model ( 'utilities', 'utilities' );
	Loader :: model ( 'webshop', 'webshop' );
	Debug :: loadKint ( );

	if ( Request :: isPost ( ) ) {
		$action = Request :: post ( 'action' );
	}
	else {
		$action = Request :: get ( 'action', 'default' );
	}

	$WebshopPrinter = unserialize ( Session :: get ( 'webshop_printer' ) );

	switch ( $action ) {
		case 'check-discount-code':
			if ( Webshop :: canCheckDiscountCode ( ) ) {
				$DiscountCode = Webshop :: checkDiscountCode ( Request :: post ( 'discount_code' ) );
				if ( $DiscountCode -> status == 'distributed' ) {
					$CartInspector = unserialize ( Session :: get ( 'cart_inspector' ) );

					if ( $CartInspector -> getTotal ( $WebshopPrinter -> registry ) >= $DiscountCode -> korting ) {
						$DiscountCode -> cash ( );
						WebshopCart :: setDiscount ( $DiscountCode -> korting );
						WebshopCart :: setDiscountCode ( $DiscountCode -> code );
						echo 'redraw-cart';
					}
					else {
						echo 'Uw kortingscode is geldig, maar het totaalbedrag in uw winkelwagentje moet groter zijn dan het bedrag van uw cadeaubon.';
					}
				}
				else {
					$sql = "
						INSERT INTO webshop_kortingcode_history
						( ip, code, status, doc, dlm )
						VALUES
						( ?, ?, ?, ?, ? )
					";
					$bindparams = array (
						$_SERVER[ 'REMOTE_ADDR' ],
						Request :: post ( 'discount_code' ),
						'wrong discount code',
						strftime ( '%F %T' ),
						strftime ( '%F %T' ),
					);
					Loader :: db ( ) -> execute ( $sql, $bindparams );
					echo 'De ingevulde kortingcode is onjuist. Controleer de code op uw cadeaubon.';
				}
			}
			else {
				echo 'U heeft twee keer een foute code ingevuld. Probeer het over 15 minuten nog eens.';
			}

			break;

		case 'sort-categories':
			Webshop :: SaveOrderBy ( 'webshop_categories', $_GET[ 'ids' ] );
			break;

		case 'sort-details':
			Webshop :: SaveOrderBy ( 'webshop_details', $_GET[ 'ids' ] );
			break;

		case 'cart-add-detail':
			WebshopCart :: AddItem ( Request :: get ( 'detail_id' ) );
			WebshopPrinter :: printCartHeader ( );
			break;

		case 'cart-remove-detail':
			WebshopCart :: RemoveItem ( $_GET[ 'detail_id' ] );
			break;

		case 'change-qty':
			if ( ctype_digit ( Request :: post ( 'value' ) ) ) {
				$value = (int)Request :: post ( 'value' );
				WebshopCart :: setQty ( Request :: post ( 'id' ), $value );
				echo $value;
			}
			break;

		case 'decrease-qty':
			WebshopCart :: decreaseQty ( Request :: post ( 'detail_id' ) );
			$WebshopPrinter -> printCartCompleteOrder ( );
			break;

		case 'increase-qty':
			WebshopCart :: increaseQty ( Request :: post ( 'detail_id' ) );
			$WebshopPrinter -> printCartCompleteOrder ( );
			break;

		case 'redraw-cart':
			$WebshopPrinter -> printCartCompleteOrder ( );
			break;

		case 'show-customer-form':
			$WebshopPrinter -> printCustomerForm ( );
			break;

		case 'email-order':
			$Mail = new Mail;
			$Mail -> setBodyHTML ( WebshopCartEmailHTMLPrinter :: printItems ( ) );
			$Mail -> setBodyPlainText ( WebshopCartEmailPlainTextPrinter :: printItems ( ) );
			// $Mail -> send ( );

			echo '<p>De mail is verzonden</p>';
			break;

		default:
			echo 'Default GET case triggered';
			break;
	}
?>