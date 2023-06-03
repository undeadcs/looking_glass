<?php
	/**
	 *	Базовый класс обработчиков запроса
	 */
	class CHandler extends CFlex {
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			return false;
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			return false;
		} // function Process
		
	} // class CHandler
	
	
?>