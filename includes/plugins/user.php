<?php
	/**
	 *	Модуль пользователей
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage ModUser
	 */

	require( "user.user.php" );
	require( "user.admin.php" );
	
	/**
	 *	Перехватчик для модуля User
	 */
	class CHModUser extends CHandler {
		private $hClient = NULL;
		private $hAdmin = NULL;
		private $hCommon = NULL;
		
		/**
		 * 	Инициализация обработчиков
		 */
		public function InitObjectHandler( ) {
			global $objCMS;
			$arrIni = array( "database" => $objCMS->database );
			$this->hCommon = new CFlexHandler( );
			$this->hCommon->Create( $arrIni );
			// админы
			$this->hAdmin = $this->hCommon;
			$this->hAdmin->CheckTable( array( FHOV_TABLE => "ud_admin", FHOV_OBJECT => "CAdmin" ) );
		} // funciton InitObjectHandler
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			return false;
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			return false;
		} // function Process
		
		/**
		 * 	Получение аккаунта суперадмина
		 * 	@return CResult
		 */
		public function GetSuperAdmin( ) {
			if ( $this->hAdmin === NULL ) {
				$this->InitObjectHandler( );
			}
			
			$objRet = new CResult( );
			$objAdmin = new CAdmin( );
			$szRankIndex = $objAdmin->GetAttributeIndex( "rank", NULL, FLEX_FILTER_DATABASE );
			$tmp = $this->hAdmin->GetObject( array( FHOV_WHERE => "`".$szRankIndex."`=".UR_SUPERADMIN, FHOV_LIMIT => "1", FHOV_TABLE => "ud_admin", FHOV_INDEXATTR => "id", FHOV_OBJECT => "CAdmin" ) );
			if ( $tmp->HasResult( ) ) {
				$tmp = $tmp->GetResult( );
				$tmp = current( $tmp );
				$objRet->AddResult( $tmp, "superadmin" );
			}
			return $objRet;
		} // function GetSuperAdmin
		
		/**
		 * 	Проверяет существование суперадмина
		 * 	@return bool
		 */
		public function CheckSuperAdmin( ) {
			$bRet = false;
			$objAdmin = new CAdmin( );
			$szRankIndex = $objAdmin->GetAttributeIndex( "rank", NULL, FLEX_FILTER_DATABASE );
			$tmp = $this->hAdmin->CountObject( array( FHOV_WHERE => "`".$szRankIndex."`=".UR_SUPERADMIN, FHOV_TABLE => "ud_admin" ) );
			if ( $tmp->HasResult( ) ) {
				$tmp = intval( $tmp->GetResult( "count" ) );
				if ( $tmp ) {
					$bRet = true;
				}
			}
			return $bRet;
		} // function CheckSuperAdmin
		
	} // class CHModUser
	
	
?>