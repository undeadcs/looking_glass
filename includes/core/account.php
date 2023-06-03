<?php
	/**
	*	Обобщенный вид аккаунта
	*/
	class CAccount extends CFlex {
		public $server = ""; // хост, на котором крутится сервак
		public $username = ""; // имя юзверя
		public $password = ""; // пароль юзверя
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			// настройки атрибутов
			$arrConfig[ "server"		][ FLEX_CONFIG_TITLE	] = "Хост";
			$arrConfig[ "username"		][ FLEX_CONFIG_TITLE	] = "Пользователь";
			$arrConfig[ "password"		][ FLEX_CONFIG_TITLE	] = "Пароль";
			return $arrConfig;
		} // function GetConfig
		
	} // class CAccount
?>