<?php
	/**
	 *	Фильтр
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Filter
	 */

	/**
	 * 	Фильтр для клиентов
	 */
	class CLogFilter extends CFlex {
		protected $d = ""; // дата
		protected $ip = ""; // ip клиента
		protected $t = ""; // тип запроса
		protected $a = ""; // агент
		
		public function GetUrlAttr( ) {
			$r = "";
			$tmp = array( );
			$arrConfig = $this->GetConfig( );
			if ( $this->d !== "" ) {
				$tmp[ ] = "d=".$this->FilterAttr( "d", $arrConfig, FLEX_FILTER_FORM );
			}
			if ( $this->ip !== "" ) {
				$tmp[ ] = "ip=".$this->FilterAttr( "ip", $arrConfig, FLEX_FILTER_FORM );
			}
			if ( $this->t ) {
				$tmp[ ] = "t=".$this->FilterAttr( "t", $arrConfig, FLEX_FILTER_FORM );
			}
			if ( $this->a !== "" ) {
				$tmp[ ] = "a=".urlencode( $this->FilterAttr( "a", $arrConfig, FLEX_FILTER_FORM ) );
			}
			if ( !empty( $tmp ) ) {
				$r = join( "&", $tmp );
			}
			return $r;
		} // function GetUrl
		
		public function GetWhere( ) {
			$r = "";
			$tmp = new CLog( );
			$arrIndex = $tmp->GetAttributeIndexList( FLEX_FILTER_FORM );
			//
			$arrWhere = array( );
			if ( preg_match( '/\d{4}-\d{2}-\d{2}/', $this->d ) ) {
				$tmp->Create( array( $arrIndex[ "cr_date" ] => $this->d ), FLEX_FILTER_FORM );
				$arrWhere[ ] = "DATE(`".$tmp->GetAttributeIndex( "cr_date", NULL, FLEX_FILTER_DATABASE )."`)=".$tmp->GetAttributeValue( "cr_date", FLEX_FILTER_DATABASE );
			}
			if ( ( $this->ip !== "" ) && CValidator::IpAddress( $this->ip ) ) {
				$tmp->Create( array( $arrIndex[ "ip" ] => $this->ip ), FLEX_FILTER_FORM );
				$arrWhere[ ] = "`".$tmp->GetAttributeIndex( "ip", NULL, FLEX_FILTER_DATABASE )."`=".$tmp->GetAttributeValue( "ip", FLEX_FILTER_DATABASE );
			}
			$arrQuery = array( "bgp", "ping", "trace", "whois", "illegal" );
			if ( ( $this->t !== "" ) && in_array( $this->t, $arrQuery ) ) {
				$tmp->Create( array( $arrIndex[ "type" ] => $this->t ), FLEX_FILTER_FORM );
				$arrWhere[ ] = "`".$tmp->GetAttributeIndex( "type", NULL, FLEX_FILTER_DATABASE )."`=".$tmp->GetAttributeValue( "type", FLEX_FILTER_DATABASE );
			}
			if ( $this->a !== "" ) {
				$arrWhere[ ] = "`".$tmp->GetAttributeIndex( "agent", NULL, FLEX_FILTER_DATABASE )."` LIKE '%".@mysql_real_escape_string( $this->a )."%'";
			}
			
			if ( !empty( $arrWhere ) ) {
				$r = join( " AND ", $arrWhere );
			}
			return $r;
		} // function GetWhere
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			$arrConfig[ FLEX_CONFIG_XML ][ FLEX_CONFIG_XMLNODENAME ] = "LogFilter";
			//
			$arrConfig[ "kw" ][ FLEX_CONFIG_LENGHT ] = 40;
			return $arrConfig;
		} // function GetConfig
		
	} // class CLogFilter
	
	
?>