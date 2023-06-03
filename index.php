<?php
	define( "UNDEAD_CS", 1 );
	
	function _usr_micro_time( ) {
		list( $usec, $sec ) = explode( " ", microtime( ) );
		return ( ( float ) $usec + ( float ) $sec );
	}
	function _usr_start_count( ) {
		$GLOBALS[ "started_at" ] = _usr_micro_time( );
	}
	function _usr_time_work( ) {
		return round( ( _usr_micro_time( ) - $GLOBALS[ "started_at" ] ), 4 )."s";
	}
	_usr_start_count( );
	
	/**
	 *	Точка входа в систему
	 *	@author UndeadCS
	 *	@version 0.9
	 *	@package Undead Content System
	 *	@subpackage EntryPoint
	 */

	require( "includes/sdk/error.php" ); // ошибки
	require( "includes/sdk/result.php" ); // результаты
	require( "includes/sdk/flex.php" ); // суперкласс
	require( "config.php" ); // конфиг
	
	/**
	 *	Элемент инклуда
	 */
	class CIncludeItem extends CFlex {
		protected $label = "";
		protected $name = "";
	} // class CIncludeItem
	
	/**
	 *	Подключалка файлов
	 */
	class CInclude extends CFlex {
		protected $suffix = "";
		protected $labels = array( );
		protected $items = array( );
		
		/**
		 *	Фильтрует значение для выбранного атрибута, используется для ввода данных в объект
		 *	@param $szName string имя атрибута
		 *	@param $arrInput mixed некое значение
		 *	@param $arrConfig array конфиг объекта
		 *	@param $iMode int режим фильтрации
		 */
		protected function InitAttr( $szName, &$arrInput, &$arrConfig, $iMode = FLEX_FILTER_PHP ) {
			$objRet = new CResult( );
			$szIndex = $this->GetAttributeIndex( $szName, $arrConfig, $iMode );
			if ( $szName == "labels" ) {
				if ( isset( $arrInput[$szIndex ] ) && is_array( $arrInput[ $szIndex ] ) ) {
					foreach( $arrInput[ $szIndex ] as $i => $v ) {
						if ( is_string( $i ) && is_string( $v ) ) {
							$this->labels[ $i ] = $v;
						}
					}
				}
			} else if ( $szName == "items" ) {
				if ( isset( $arrInput[ $szIndex ] ) && is_array( $arrInput[ $szIndex ] ) ) {
					foreach( $arrInput[ $szIndex ] as $i => $v ) {
						if ( is_array( $v ) ) {
							$tmp = new CIncludeItem( );
							$tmp->Create( $v );
							$this->items[ $i ] = $tmp;
						}
					}
				}
			} else {
				$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			}
			return $objRet;
		} // function InitAttr
		
		/**
		 *	Запуск подключения файлов
		 */
		public function Process( ) {
			foreach( $this->items as $i => $v ) {
				$tmp = $v->GetArray( );
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( );
					$szPrefix = ( isset( $this->labels[ $tmp[ "label" ] ] ) ? $this->labels[ $tmp[ "label" ] ] : "" );
					if ( file_exists( $szPrefix.$tmp[ "name" ].$this->suffix ) ) {
						include_once( $szPrefix.$tmp[ "name" ].$this->suffix );
					}
				}
			}
		} // function Process
	}
	
	// includes
	$objSystemInclude = new CInclude( );
	$objSystemInclude->Create( $g_arrConfig[ "include" ] );
	$objSystemInclude->Process( );
	
	// system
	$objCMS = new CSystem( );
	$objCMS->Create( $g_arrConfig[ "system" ] );
	unset( $tmp );
	
	// relative [str_here] /
	$objCMS->ApplyPath( "root_relative", str_replace( "/index.php", "", $_SERVER[ "SCRIPT_NAME" ] ) );
	// system [str_here] /relative/
	if ( $objCMS->GetPath( "root_relative" ) === "" ) {
		$tmp = preg_replace( '/\/$/', '', $_SERVER[ "DOCUMENT_ROOT" ] );
		$objCMS->ApplyPath( "root_system", $tmp );
	} else {
		$tmp = preg_replace( '/\/$/', '', $_SERVER[ "DOCUMENT_ROOT" ] );
		$objCMS->ApplyPath( "root_system", $tmp.$objCMS->GetPath( "root_relative" ) );
	}
	// http [str_here] /
	$objCMS->ApplyPath( "root_http", "http://".$_SERVER[ "HTTP_HOST" ].$objCMS->GetPath( "root_relative" ) );
	// application [str_here] /
	$objCMS->ApplyPath( "root_application", $objCMS->GetPath( "root_system" )."/folder" );
	// scripts [str_here] /system/
	$objCMS->ApplyPath( "system_scripts", $objCMS->GetPath( "root_system" )."/scripts" );
	// styles [str_here] /system/
	$objCMS->ApplyPath( "system_styles", $objCMS->GetPath( "root_system" )."/styles" );
	
	//ShowVarD( $objCMS );
		
	$objCMS->SystemStart( );
	
	$objCMS->SystemProcess( );
	$objCMS->SystemTerminate( );
?>