<?php
	/**
	 *	Модуль авторизации
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage ModLogin
	 */

	/**
	 * 	Модуль авторизации
	 */
	class CHModLogin extends CHandler {
		private $hSuperAdmin = NULL;
		private $hAdmin = NULL;
		private $hClient = NULL;
		private $hCommon = NULL;
		
		public function InitHandlers( ) {
			global $objCMS;
			$this->hCommon = new CFlexHandler( );
			$this->hCommon->Create( array( "database" => $objCMS->database ) );
		} // function InitHandlers
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			if ( !session_id( ) ) {
				session_start( );
			}
			$modInstall = new CHModInstall( );
			if ( $modInstall->IsSystemInstalled( ) /*&& preg_match( '/\/admin\//', $szQuery )*/ ) {
				return true;
			}
			if ( isset( $_SESSION[ "logged" ] ) ) {
				unset( $_SESSION[ "logged" ] );
			}
			return false;
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			global $objCMS, $objCurrent, $mxdCurrentData, $szCurrentMode, $arrErrors;
			$this->InitHandlers( );
			//
			if ( isset( $_SESSION[ "logged" ] ) ) {
				$iId = intval( $_SESSION[ "logged" ][ "id" ] );
				$iVId = intval( $_SESSION[ "logged" ][ "v_id" ] );
				$iRank = intval( $_SESSION[ "logged" ][ "rank" ] );
				$szLogin = $_SESSION[ "logged" ][ "login" ];
				$objSysAcc = new CSystemAccount( );
				$objSysAcc->Create( array( "id" => $iId, "v_id" => $iVId, "rank" => $iRank, "login" => $szLogin ) );
				$objCMS->SetUser( $objSysAcc );
				
				if ( preg_match( '/^\/admin\/exit\//', $szQuery ) ) {
					unset( $_SESSION[ "logged" ] );
					Redirect( $objCMS->GetPath( "root_relative" )."/admin/" );
					exit;
				}
			} else {
				if ( count( $_POST ) && isset( $_POST[ "login" ], $_POST[ "password" ] ) ) {
					$arrData[ "login" ] = $_POST[ "login" ];
					$arrData[ "password" ] = $_POST[ "password" ];
					$modUser = new CHModUser( );
					$tmp = $modUser->GetSuperAdmin( );
					if ( $tmp->HasResult( ) ) {
						$tmp = $tmp->GetResult( );
						$tmp = current( $tmp );
						if ( $tmp->IsPasswordEqual( $arrData[ "password" ] ) ) {
							$szLastLoginIndex = $tmp->GetAttributeIndex( "last_login" );
							$tmp->Create( array( $szLastLoginIndex => date( "Y-m-d" ) ) );
							$tmp1 = array( );
							$tmp1[ "id" ] = $tmp->id;
							$tmp1[ "v_id" ] = $tmp->graph_vertex_id;
							$tmp1[ "login" ] = $arrData[ "login" ];
							$this->hCommon->UpdObject( array( $tmp ), array( FHOV_ONLYATTR => array( "id", "last_login" ), FHOV_TABLE => "ud_admin", FHOV_INDEXATTR => "id" ) );
							$tmp1[ "rank" ] = SUR_SUPERADMIN;
							$_SESSION[ "logged" ] = $tmp1;
						}
					}
					
					Redirect( $objCMS->GetPath( "root_relative" )."/admin/" );
				}
				
				if ( preg_match( '/\/admin\//', $szQuery ) ) {
					$objCurrent = "Login";
					$szCurrentMode = "Form";
					// передаем управление приложению
					$szFolder = $objCMS->GetPath( "root_application" );
					if ( $szFolder !== false && file_exists( $szFolder."/index.php" ) ) {
						include_once( $szFolder."/index.php" );
					}
					return true;
				}
			}
			
			return false;
		} // function Process
		
	} // class CHModLogin
	
?>