<?php
	/**
	 *	Лог
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Log
	 */

	/**
	 * 	Лог
	 */
	class CLog extends CFlex {
		protected $id = 0;
		protected $cr_date = ""; // дата запроса
		protected $ip = ""; // ip клиента
		protected $agent = ""; // агент ( при whois пусто )
		protected $type = ""; // тип: bgp, ping, trace, whois
		protected $addr = ""; // адрес
		protected $query = ""; // команда, которая в итоге была послана роутеру

		/**
		 *	Получение конфига
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			// общие настройки
			$arrConfig[ FLEX_CONFIG_TABLE ] = "ud_log";
			$arrConfig[ FLEX_CONFIG_PREFIX ] = "log_";
			$arrConfig[ FLEX_CONFIG_SELECT ] = "id";
			$arrConfig[ FLEX_CONFIG_UPDATE ] = "id";
			$arrConfig[ FLEX_CONFIG_DELETE ] = "id";
			// настройки режимов
			$arrConfig[ FLEX_CONFIG_XML	][ FLEX_CONFIG_XMLNODENAME ] = "Log";
			// настройки атрибутов
			$arrConfig[ "id"		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_INT | FLEX_TYPE_UNSIGNED | FLEX_TYPE_NOTNULL | FLEX_TYPE_AUTOINCREMENT | FLEX_TYPE_PRIMARYKEY;
			$arrConfig[ "id"		][ FLEX_CONFIG_DIGITS	] = 10;
			$arrConfig[ "date"		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_DATE | FLEX_TYPE_TIME;
			$arrConfig[ "date"		][ FLEX_CONFIG_TITLE	] = "Дата";
			$arrConfig[ "ip"		][ FLEX_CONFIG_TITLE	] = "IP-адрес клиента";
			$arrConfig[ "agent"		][ FLEX_CONFIG_TITLE	] = "Агент";
			$arrConfig[ "type"		][ FLEX_CONFIG_TITLE	] = "Тип запроса";
			$arrConfig[ "addr"		][ FLEX_CONFIG_TITLE	] = "Адрес запроса";
			$arrConfig[ "query"		][ FLEX_CONFIG_TITLE	] = "Команда запроса";
			return $arrConfig;
		} // function GetConfig
		
	} // class CLog
	
	
?>