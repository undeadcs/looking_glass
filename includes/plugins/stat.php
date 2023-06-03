<?php
	/**
	 *	Статистика
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Stat
	 */

	require( "stat.log.php" );
	require( "stat.filter.php" );

	/**
	 * 	Статистика
	 */
	class CHStat extends CHandler {
		private $hCommon = NULL;
		
		/**
		 * 	Инициализация обработчиков
		 */
		private function InitHandlers( ) {
			global $objCMS;
			$this->hCommon = new CFlexHandler( );
			$this->hCommon->Create( array( "database" => $objCMS->database ) );
			$this->hCommon->CheckTable( array( FHOV_TABLE => "ud_log", FHOV_OBJECT => "CLog" ) );
		} // function InitHandlers
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			if ( preg_match( '/^\/admin\/$\//', $szQuery ) ) {
				return false;
			}
			return ( preg_match( '/^\/admin\/stat\//', $szQuery ) ? true : false );
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			global $objCMS, $objCurrent, $mxdCurrentData, $szCurrentMode, $arrErrors;
			$this->InitHandlers( );
			$objCMS->SetWGI( WGI_STAT );
			$objCMS->SetWGIState( MF_THIS );
			$objCurrent = "Stat";
			$szCurrentMode = "List";
			$arrErrors = array( );
			$mxdCurrentData = array(
				"log_list" => array( ),
				"filter" => NULL,
				"pager" => NULL
			);
			
			$arrOptions = array( FHOV_ORDER => "`log_cr_date` DESC", FHOV_TABLE => "ud_log", FHOV_OBJECT => "CLog" );
			
			$objFilter = new CLogFilter( );
			$objFilter->Create( $_GET, FLEX_FILTER_FORM );
			$szWhere = $objFilter->GetWhere( );
			if ( $szWhere !== "" ) {
				$arrOptions[ FHOV_WHERE ] = $szWhere;
			}
			$mxdCurrentData[ "filter" ] = $objFilter;
			
			$szUrl = $objFilter->GetUrlAttr( );
			if ( $szUrl === "" ) {
				$szUrl = $objCMS->GetPath( "root_relative" )."/admin/stat/?";
			} else {
				$szUrl = $objCMS->GetPath( "root_relative" )."/admin/stat/?".$szUrl."&";
			}
			
			$iCount = $this->hCommon->CountObject( $arrOptions );
			$iCount = $iCount->GetResult( "count" );
			$objPager = new CPager( );
			$arrData = array(
				"url" => $szUrl,
				"page" => @$_GET[ "page" ],
				"page_size" => 15,
				"total" => $iCount
			);
			$objPager->Create( $arrData, FLEX_FILTER_FORM );
			$szLimit = $objPager->GetSQLLimit( );
			if ( $szLimit !== "" ) {
				$arrOptions[ FHOV_LIMIT ] = $szLimit;
			}
			$mxdCurrentData[ "pager" ] = $objPager;
			
			$tmp = $this->hCommon->GetObject( $arrOptions );
			if ( $tmp->HasResult( ) ) {
				$mxdCurrentData[ "log_list" ] = $tmp->GetResult( );
			}
			
			$szFolder = $objCMS->GetPath( "root_application" );
			if ( $szFolder !== false && file_exists( $szFolder."/index.php" ) ) {
				include_once( $szFolder."/index.php" );
			}
			
			return true;
		} // function Process
		
		/**
		 * 	Добавление лога
		 */
		public function AddLog( $objQueryForm ) {
		} // function AddLog
		
	} // class CHStat
	
	
?>