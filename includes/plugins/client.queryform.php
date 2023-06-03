<?php
	/**
	 *	Форма запроса
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage Query
	 */

	/**
	 * 	Форма запроса
	 */
	class CQueryForm extends CFlex {
		protected $query = "";
		protected $addr = "";
		protected $agent = "";
		protected $flt_inp = "";
		protected $flt_msk = "";
		protected $ftl_reg = 0;
		
		public function __get( $szName ) {
			$arrReadOnly = array(
				"query" => true,
				"addr" => true,
				"agent" => true,
			);
			if ( isset( $arrReadOnly[ $szName ] ) && $arrReadOnly[ $szName ] ) {
				return $this->$szName;
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
		/**
		 * 	Проверяет стоит ли делать запрос
		 */
		public function DoQuery( ) {
			return ( !empty( $this->query ) && !empty( $this->addr ) && !empty( $this->agent ) );
		} // function DoQuery
		
		/**
		 *	Получение XML экземпляра
		 *	@param $domDoc DOMDocument экземпляр данного класса
		 *	@return CResult
		 */
		public function GetXML( &$domDoc ) {
			$objRet = parent::GetXML( $domDoc );
			if ( !$objRet->HasError( ) ) {
				$tmp = $objRet->GetResult( "doc" );
				
				$arrQueryType = array( "bgp", "ping", "trace", "whois" );
				foreach( $arrQueryType as $v ) {
					$tmp1 = $domDoc->createElement( "QueryType" );
					$tmp1->setAttribute( "name", $v );
					if ( ( $this->query == "" && $v == "bgp" ) || ( $this->query == $v ) ) {
						$tmp1->setAttribute( "sel", true );
					}
					$tmp->appendChild( $tmp1 );
				}
				
				$objRet->AddResult( $tmp, "doc" );
			}
			return $objRet;
		} // function GetXML
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			$arrConfig[ FLEX_CONFIG_XML ][ FLEX_CONFIG_XMLNODENAME ] = "QueryForm";
			return $arrConfig;
		} // function GetConfig
		
		/**
		 *	Инициализация атрибута объекта
		 *	@param $szName string имя атрибута
		 *	@param $arrInput mixed некое значение
		 *	@param $arrConfig array конфиг объекта
		 *	@param $iMode int режим фильтрации
		 *	@return CResult
		 */
		protected function InitAttr( $szName, &$arrInput, &$arrConfig, $iMode = FLEX_FILTER_PHP ) {
			$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			if ( $szName == "query" ) {
				$arrQuery = array( "bgp", "ping", "trace", "whois" );
				if ( !in_array( $this->query, $arrQuery ) ) {
					$this->query = "";
				}
			} elseif ( $szName == "addr" ) {
			}
			return $objRet;
		} // function InitAttr
		
	} // class CQueryForm
	
	
?>