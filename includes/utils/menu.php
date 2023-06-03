<?php
	/**
	 *	Меню
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Menu
	 */

	// flags MF - Menu Flag
	define( "MF_NONE",		bindec( "00000000000000000000000000000000" ) ); // просто пункт в меню
	define( "MF_CURRENT",		bindec( "00000000000000000000000000000001" ) ); // текущий пункт
	define( "MF_THIS",		bindec( "00000000000000000000000000000010" ) ); // находимся в данном пункте

	/**
	 *	Элемент меню
	 */
	class CMenuItem extends CFlex {
		public $title = ""; // заголовок пункта меню
		public $url = ""; // адрес ссылки
		public $flags = MF_NONE; // флаги
		
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
			if ( $szName == "title" || $szName == "url" ) {
				if ( isset( $arrInput[ $szIndex ] ) ) {
					$mxdValue = @trim( strval( $arrInput[ $szIndex ] ) );
					$mxdValue = html_entity_decode( $mxdValue );
					if ( $szName == "title" ) {
						if ( $iMode == FLEX_FILTER_PHP ) {
							// внес эту вещь в базовый класс, т.к. PHP у нас теперь cp1251
							//$mxdValue = iconv( "cp1251", "UTF-8", $mxdValue );
						}
						$this->$szName = $mxdValue;
					} elseif ( $szName == "url" ) {
						if ( strpos( $mxdValue, "&amp;" ) !== false ) {
							// нахуй сносим любые амперсанды, сносим пока они есть.
							while( strpos( $mxdValue, "&amp;" ) !== false ) {
								$mxdValue = str_replace( "&amp;", "&", $mxdValue );
							}
						}
						$this->$szName = $mxdValue;
					}
				}
			} else {
				$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			}
			return $objRet;
		} // function InitAttr
		
	} // class CMenuItem
	
	/**
	 *	Меню
	 */
	class CMenu extends CFlex {
		public $items = array( );
		
		/**
		 *	Получение XML экземпляра
		 *	@param $domDoc DOMDocument экземпляр данного класса
		 *	@return CResult
		 */
		public function GetXML( &$domDoc ) {
			$mxdRet = new CResult( );
			if ( is_object( $domDoc ) && ( get_class( $domDoc ) == "DOMDocument" ) ) {
				$arrConfig = $this->GetConfig( );
				$szXMLNode = $this->GetXMLNodeName( $arrConfig );
				$doc = $domDoc->createElement( $szXMLNode );
				$arrAttr = $this->GetAttributeList( );
				foreach( $arrAttr as $i => $v ) {
					if ( $i == "items" ) {
						foreach( $v as $j => $w ) {
							$tmp = new CMenuItem( );
							$tmp->Create( $w );
							$tmp1 = $tmp->GetXML( $domDoc );
							if ( $tmp1->HasResult( ) ) {
								$doc->appendChild( $tmp1->GetResult( "doc" ) );
							}
						}
					}
				}
				$mxdRet->AddResult( $doc, "doc" );
			} else {
				$mxdRet->AddError( new CError( 0, "Wrong domDoc object type" ) );
			}
			return $mxdRet;
		} // function GetXML
		
	} // class CMenu
?>