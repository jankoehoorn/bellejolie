<?php
	Namespace Webshop;

		defined ( 'C5_EXECUTE' ) or die ( 'Access Denied' );

		Use Loader;
		Use Webshop\IoC;
		Use Utilities\Date;

		Interface InventoryManagerInterface {
			const CODE_OP_VOORRAAD = 1;
			const CODE_VERKOCHT = 2;
			const CODE_PROMOTIE = 3;
			const CODE_EIGEN_GEBRUIK = 4;
			const CODE_BESCHADIGD = 5;
			const CODE_TESTER = 6;
			const CODE_BESTELLEN = 7;
		}

		Interface InventoryManagerValidatorInterface {
		}

		Interface InventoryManagerServiceProviderInterface {
		}

		Class InventoryManager Implements InventoryManagerInterface {
			public $validator;
			public $_table = 'webshop_voorraad';

			public function setValidator ( InventoryManagerValidatorInterface $InventoryManagerValidatorInterface ) {
				$this -> validator = $InventoryManagerValidatorInterface;
			}

			public function init ( ) {
				$sql = "SHOW COLUMNS FROM ?";
				$bindparams = array ( $this -> _table );
				$cols = Loader :: db ( ) -> getAll ( $sql, $bindparams );
				return $cols;
				foreach ( $cols as $k => $v ) {

				}
			}

			public function validate ( ) {
				return $this -> validator -> validate ( $this );
			}

			public function populate ( $params ) {
				foreach ( $params as $k => $v ) {
					$this -> $k = $v;
				}
			}

			public function getNumDetailsInStock ( $detail_id ) {
				$sql = "
					SELECT COUNT(detail_id) AS num_details
					FROM webshop_voorraad
					WHERE detail_id = ?
					AND code_id = 1
				";
				$bindparams = array ( $detail_id );
				return Loader :: db ( ) -> getOne ( $sql, $bindparams );
			}

			public function sellDetail ( $detail_id ) {
				$sql = "
					UPDATE webshop_voorraad
					SET code_id = 2, dlm = ?
					WHERE detail_id = ?
					AND code_id = 1
					LIMIT 1
				";
				$bindparams = array (
					Date :: now ( ),
					$detail_id,
				);
				Loader :: db ( ) -> execute ( $sql, $bindparams );
			}

			public function orderDetail ( $detail_id ) {
				$sql = "
					INSERT INTO webshop_voorraad
					( detail_id, code_id, doc, dlm )
					VALUES
					( ?, 7, ?, ? )
				";
				$bindparams = array (
					$detail_id,
					Date :: now ( ),
					Date :: now ( ),
				);
				Loader :: db ( ) -> execute ( $sql, $bindparams );
			}

		}

		Class InventoryManagerValidator Implements InventoryManagerValidatorInterface {
			public $validators = array (
				// TODO: fill this array
			);

			public function validate ( InventoryManagerInterface $InventoryManagerInterface ) {
				foreach ( $this -> validators as $name => $type ) {
					$validator = IoC :: make ( $type . 'Validator' );
					$validator -> validate ( $InventoryManagerInterface -> $name );
					if ( !$validator -> pass ) {
						$InventoryManagerInterface -> errs[ $name ] = 'err';
					}
				}
				$InventoryManagerInterface -> pass = (count ( $InventoryManagerInterface -> errs ) == 0);

				return $InventoryManagerInterface -> pass;
			}

		}

		Class InventoryManagerServiceProvider Implements InventoryManagerServiceProviderInterface {
			public static function register ( ) {
				IoC :: bind ( 'InventoryManager', function ( ) {
					return new InventoryManager;
				} );
			}

		}
?>