<?php
	/**
	 *	Класс для ведения ошибок
	 *	@author UndeadCS
	 *	@package UndeadCS SDK
 	 *	@subpackage Error
	 */

	/**
	 *	Класс ошибок
	 */
	class CError {
		private $m_iCode = 0;
		private $m_szText = "";
		
		public function __construct( $iCode = 0, $szText = "" ) {
			$this->m_iCode = @intval( $iCode );
			$this->m_szText = @strval( $szText );
		} // function __construct
		
		public function __get( $szName ) {
			if ( $szName == "code" ) {
				return $this->m_iCode;
			} elseif ( $szName == "text" ) {
				return $this->m_szText;
			}
		} // function __get
		
		/**
		 *	Получение кода ошибки
		 */
		public function GetCode( ) {
			return $this->m_iCode;
		} // function GetCode
		
		/**
		 *	Получение текста ошибки
		 */
		public function GetText( ) {
			return $this->m_szText;
		} // function GetText
		
	} // class CError
	
?>