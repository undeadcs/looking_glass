<?php
	/**
	 *	Модуль серверов
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Pager
	 */

	/**
	 * 	Пейджер
	 */
	class CPager extends CFlex {
		protected $url = "";
		protected $page_size = 50;
		protected $page = 1;
		protected $pages_length = 5;
		protected $total = 0;
		//
		protected $pages = array( );
		protected $prev = 0;
		protected $next = 0;
		
		/**
		 *	Наполнение параметров объекта
		 *	используя маппинг можно получить разные имена атрибутов сущности в разных хранилищах
		 *	@param $arrInput mixed входные данные для объекта
		 *	@param $iMode int режим, из которого были получены данные
		 *	@return CResult
		 */
		public function Create( $arrInput, $iMode = FLEX_FILTER_PHP ) {
			$objRet = parent::Create( $arrInput, $iMode );
			if ( $this->page < 1 ) {
				$this->page = 1;
			}
			if ( $this->page_size < 1 ) {
				$this->page_size = 1;
			}
			//
			if ( $this->total && $this->page_size ) {
				$iMaxPage = intval( ceil( $this->total / $this->page_size ) );
				$iCurFrame = intval( ceil( $this->page / $this->pages_length ) );
				$iStart = ( $iCurFrame - 1 ) * $this->pages_length;
				$iEnd = $iCurFrame * $this->pages_length + 1;
				
				if ( $iStart ) {
					$this->pages[ ] = array( $iStart, false );
				}
				++$iStart;
				for( $i = $iStart; $i < $iEnd; ++$i ) {
					if ( $i > $iMaxPage ) {
						break;
					} else {
						if ( $i == $this->page ) {
							$this->pages[ ] = array( $i, true );
						} else {
							$this->pages[ ] = array( $i, false );
						}
					}
				}
				if ( $iEnd > $this->pages_length && $iEnd < ( $iMaxPage + 1 ) ) {
					$this->pages[ ] = array( $iEnd, false );
				}
				
				if ( $this->page > 1 ) {
					$this->prev = $this->page - 1;
				}
				if ( $this->page < $iMaxPage ) {
					$this->next = $this->page + 1;
				}
				//ShowVarD( $this->pages );
			}
			//
			return $objRet;
		} // function Create
		
		/**
		 *	Получение XML экземпляра
		 *	@param $domDoc DOMDocument экземпляр данного класса
		 *	@return CResult
		 */
		public function GetXML( &$domDoc ) {
			$objRet = parent::GetXML( $domDoc );
			if ( $objRet->HasResult( ) ) {
				$tmp = $objRet->GetResult( "doc" );
				foreach( $this->pages as $v ) {
					$tmp1 = $domDoc->createElement( "PagerOption" );
					$tmp1->setAttribute( "page", $v[ 0 ] );
					$tmp1->setAttribute( "cur", $v[ 1 ] );
					$tmp->appendChild( $tmp1 );
				}
				$tmp1 = $domDoc->createElement( "PagerPrev" );
				$tmp1->setAttribute( "page", $this->prev );
				$tmp->appendChild( $tmp1 );
				$tmp1 = $domDoc->createElement( "PagerNext" );
				$tmp1->setAttribute( "page", $this->next );
				$tmp->appendChild( $tmp1 );
				$objRet->AddResult( $tmp, "doc" );
			}
			return $objRet;
		} // function GetXML
		
		/**
		 * 	Получение строки для лимита
		 */
		public function GetSQLLimit( ) {
			return ( $this->page - 1 ) * $this->page_size.",".$this->page_size;
		} // function GetSQLLimit
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			$arrConfig[ FLEX_CONFIG_XML ][ FLEX_CONFIG_XMLNODENAME ] = "Pager";
			return $arrConfig;
		} // function GetConfig
		
	} // class CPager
?>