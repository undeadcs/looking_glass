<?php
	/**
	 *	Страница
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage Page
	 */
	
	define( "CPAGE_MODE_START",	0	); // ничего не заполняли
	define( "CPAGE_MODE_HEAD",	1	); // заполнение заголовка страницы
	define( "CPAGE_MODE_BODY",	2	); // заполнение тела страницы

	/**
	 *	Класс для обработки вывода HTML страницы
	 */
	class CPage extends CFlex {
		private $m_iState = CPAGE_MODE_START;
		private $m_szDocType = "";
		private $m_szTitle = "";
		private $m_arrMeta = array( );
		private $m_arrScript = array( );
		private $m_arrStyle = array( );
		private $m_szBody = "";
		// пока не используются
		private $m_objDoc = NULL; // DOMDocument, который будет обрабатываться
		private $m_objXsl = NULL; // DOMDocument, который будет обработчиком
		private $m_objXslt = NULL; // XSLT - процессор преобразования
		
		private function ReplaceEntity( &$szText ) {
			// TODO: вынести эти настройки в Web-интерфейс и хранение в БД
			$arrEntity = array(
				"lt", "gt", "nbsp", "larr", "rarr", "laquo", "raquo", "mdash"
			);
			foreach( $arrEntity as $v ) {
				$szText = str_replace( "&amp;".$v.";", "&".$v.";", $szText );
			}
		} // function ReplaceEntity
		
		public function CPage( ) {
			$this->m_szDocType = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		} // function CPage
		
		public function StartHead( ) {
			$this->m_iState = CPAGE_MODE_HEAD;
		} // function StartHead
		
		public function EndHead( ) {
			$this->m_iState = CPAGE_MODE_START;
		} // function EndHead
		
		public function AddMeta( $arrMeta ) {
			$tmp = new CHtmlMeta( );
			$tmp->Create( $arrMeta );
			$this->m_arrMeta[ ] = $tmp;
		} // function AddMeta
		
		public function AddStyle( $szHref ) {
			$tmp = new CHtmlLink( );
			$tmp->Create( array( "href" => $szHref ) );
			$this->m_arrMeta[ ] = $tmp;
		} // function AddStyle
		
		public function AddScript( $szSrc ) {
			$tmp = new CHtmlScript( );
			$tmp->Create( array( "src" => $szSrc ) );
			$this->m_arrScript[ ] = $tmp;
		} // function AddScript
		
		public function GetHead( ) {
			$r = "<title>".$this->m_szTitle."</title>";
			foreach( $this->m_arrMeta as $i => $v ) {
				$tmp = $v->GetHTML( );
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( 0 );
					$r .= $tmp;
					$tmp = NULL;
				}
			}
			foreach( $this->m_arrStyle as $i => $v ) {
				$tmp = $v->GetHTML( );
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( 0 );
					$r .= $tmp;
					$tmp = NULL;
				}
			}
			foreach( $this->m_arrScript as $i => $v ) {
				$tmp = $v->GetHTML( );
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( 0 );
					$r .= $tmp;
					$tmp = NULL;
				}
			}
			return '<head>'.$r.'</head>';
		} // function GetHead
		
		public function GetBody( ) {
			return '<body>'.$this->m_szBody.'</body>';
		} // function GetBody
		
		/**
		 *	Начало заполнения тела документа
		 */
		public function StartBody( ) {
			$this->m_iState = CPAGE_MODE_BODY;
			ob_start( );
		} // function StartBody
		
		/**
		 *	Завершение наполнения тела документа
		 *	@return void
		 */
		public function EndBody( ) {
			$tmp = ob_get_clean( );
			if ( $tmp !== false ) {
				$this->m_szBody = $tmp;
			}
			$this->m_iState = CPAGE_MODE_START;
		} // function EndBody
		
		/**
		 *	Получение текста страницы
		 *	@return string
		 */
		public function GetDoc( ) {
			$r = $this->m_szDocType;
			$r .= $this->GetStartHtml( );
			$r .= $this->GetHead( );
			$r .= $this->GetBody( );
			$r .= $this->GetEndHtml( );
			$this->ReplaceEntity( $r );
			return $r;
		} // function GetDoc
		
		/**
		 *	Устанавливает заголовок страницы
		 *	@param $szTitle string новый заголовок для страницы
		 *	@return void
		 */
		public function SetTitle( $szTitle ) {
			$this->m_szTitle = @strval( $szTitle );
		} // function SetTitle
		
		/**
		 *	Возвращает заголовок страницы
		 *	@return string
		 */
		public function GetTitle( ) {
			return $this->m_szTitle;
		} // function GetTitle
		
		/**
		 * 	Возвращает строку DOCTYPE
		 */
		public function GetDocType( ) {
			return $this->m_szDocType;
		} // function GetDocType
		
		/**
		 * 	Возвращает стартовый html тэг
		 */
		public function GetStartHtml( ) {
			return '<html xmlns="http://www.w3.org/1999/xhtml">';
		} // function GetStartHtml
		
		/**
		 * 	Возвращает закрывающий тэг html
		 */
		public function GetEndHtml( ) {
			return '</html>';
		} // function GetEndHtml
		
	} // class CPage
	
	
	
?>