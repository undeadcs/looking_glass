<?php
	/**
	 *	Класс фильтра
	 */
	class CFilter {
		private $m_szName = "";
		
		public function SetName( $szName ) {
			$this->m_szName = @strval( $szName );
		}
		
		public function Apply( $mxdValue ) {
			return $mxdValue;
		}
	}
?>