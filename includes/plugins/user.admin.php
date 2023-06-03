<?php
	/**
	 *	Учетка: администраторы
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage ModUser
	 */

	// Ранг пользователя UR - User Rank
	define( "UR_SUPERADMIN",	0	); // суперадминистратор
	define( "UR_ADMIN",		1	); // администратор
	define( "UR_OPERATOR",		2	); // оператор
	
	/**
	 * 	Админская учетка
	 */
	class CAdmin extends CUser {
		protected $rank = UR_OPERATOR; // ранг
		
		public function __get( $szName ) {
			$arrReadOnly = array(
				"rank" => true,
			);
			if ( isset( $arrReadOnly[ $szName ] ) && $arrReadOnly[ $szName ] ) {
				return $this->$szName;
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
		/**
		 *	Получение конфига
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			// общие настройки
			$arrConfig[ FLEX_CONFIG_TABLE ] = "ud_admin";
			$arrConfig[ FLEX_CONFIG_PREFIX ] = "admin_";
			// настройки режимов
			$arrConfig[ FLEX_CONFIG_XML	][ FLEX_CONFIG_XMLNODENAME ] = "Admin";
			// настройки атрибутов
			$arrConfig[ "rank"		][ FLEX_CONFIG_TITLE	] = "Ранг";
			$arrConfig[ "add_info"		][ FLEX_CONFIG_TITLE	] = "Дополнительная информация";
			$arrConfig[ "add_info"		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_TEXT;
			return $arrConfig;
		} // function GetConfig
		
	} // class CAdmin
	
?>