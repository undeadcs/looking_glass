<?php
	/**
	 *	Клиентская часть
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage Client
	 */

	require( 'client.queryform.php' );
	require( 'client.query.php' );

	/**
	 * 
	 */
	class CHModClient extends CHandler {
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			global $objCMS;
			if ( preg_match( '/^\/admin\//', $szQuery ) ) {
				return false;
			}
			//
			if ( !preg_match( '/^\/help\//', $szQuery ) ) {
				Redirect( $objCMS->GetPath( 'root_relative' ).'/lg.cgi' );
			}
			//
			return true;
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			global $objCMS, $objCurrent, $mxdCurrentData, $szCurrentMode, $arrErrors;
			$objCurrent	= 'Client';
			$szCurrentMode	= 'Help';
			/*$szCurrentMode = "Mode";
			$arrErrors = array( );
			$mxdCurrentData = array(
				"agent_list" => array( ),
				"current_queryform" => NULL,
				"current_query" => NULL
			);
			
			$hCommon = new CFlexHandler( );
			$hCommon->Create( array( "database" => $objCMS->database ) );
			$tmp = $hCommon->GetObject( array( FHOV_TABLE => "ud_agent", FHOV_INDEXATTR => "id", FHOV_OBJECT => "CAgent" ) );
			if ( $tmp->HasResult( ) ) {
				$mxdCurrentData[ "agent_list" ] = $tmp->GetResult( );
			}
			unset( $hCommon, $tmp );
			
			$mxdCurrentData[ "current_queryform" ] = new CQueryForm( );
			$mxdCurrentData[ "current_queryform" ]->Create( $_GET, FLEX_FILTER_FORM );
			
			if ( $mxdCurrentData[ "current_queryform" ]->DoQuery( ) ) {
				$mxdCurrentData[ "current_query" ] = new CQuery( );
			}*/
			
			// передаем управление приложению
			$szFolder = $objCMS->GetPath( 'root_application' );
			if ( $szFolder !== false && file_exists( $szFolder.'/index_client.php' ) ) {
				include_once( $szFolder.'/index_client.php' );
			}
			
			return true;
		} // function Process
		
	} // class CHModClient
	
	
	
?>