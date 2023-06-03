<?php
	/**
	 *	Агенты
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Agent
	 */

	/**
	 * 	Агент
	 */
	class CAgent extends CFlex {
		protected $id = 0;
		protected $name = '';
		//protected $title = "";
		//protected $url = '';
		protected $ip = '';
		protected $login = '';
		protected $key = '';
		protected $password = '';
		protected $ostype = '';
		protected $protocol = '';
		protected $password2 = '';
		protected $port = 23;
		
		public function __get( $szName ) {
			$arrReadOnly = array(
				'id' => true,
				'name' => true,
				'title' => true,
				//'url' => true,
				'ip' => true,
				'login' => true,
				'password' => true,
				'ostype' => true,
                                'protocol' => true,
				'key' => true,
				'password2' => true,
				'port' => true,
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
			$arrConfig[ FLEX_CONFIG_TABLE ] = 'ud_agent';
			$arrConfig[ FLEX_CONFIG_PREFIX ] = 'agent_';
			$arrConfig[ FLEX_CONFIG_SELECT ] = 'id';
			$arrConfig[ FLEX_CONFIG_UPDATE ] = 'id';
			$arrConfig[ FLEX_CONFIG_DELETE ] = 'id';
			// настройки режимов
			$arrConfig[ FLEX_CONFIG_XML	][ FLEX_CONFIG_XMLNODENAME ] = 'Agent';
			// настройки атрибутов
			$arrConfig[ 'id'		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_INT | FLEX_TYPE_UNSIGNED | FLEX_TYPE_NOTNULL | FLEX_TYPE_AUTOINCREMENT | FLEX_TYPE_PRIMARYKEY;
			$arrConfig[ 'id'		][ FLEX_CONFIG_DIGITS	] = 10;
			$arrConfig[ 'name'		][ FLEX_CONFIG_LENGHT	] = 50;
			$arrConfig[ 'name'		][ FLEX_CONFIG_TITLE	] = 'Имя';
			//$arrConfig[ 'title'		][ FLEX_CONFIG_LENGHT	] = 50;
			//$arrConfig[ 'title'		][ FLEX_CONFIG_TITLE	] = 'Заголовок';
			//$arrConfig[ 'url'		][ FLEX_CONFIG_TITLE	] = 'Url';
			$arrConfig[ 'ip'		][ FLEX_CONFIG_TITLE	] = 'IP-Адрес';
			$arrConfig[ 'login'		][ FLEX_CONFIG_TITLE	] = 'Логин';
			$arrConfig[ 'password'		][ FLEX_CONFIG_TITLE	] = 'Пароль';
			$arrConfig[ 'protocol'          ][ FLEX_CONFIG_TITLE    ] = 'Протокол';
			$arrConfig[ 'key'         	][ FLEX_CONFIG_TITLE    ] = 'Файл ключа';
			$arrConfig[ 'key'		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_TEXT;
			$arrConfig[ 'key'		][ FLEX_CONFIG_LENGHT	] = 10000;
			$arrConfig[ 'passowrd2'        	][ FLEX_CONFIG_TITLE    ] = 'Пароль для демона bgp';
			$arrConfig[ 'port'         	][ FLEX_CONFIG_TITLE    ] = 'Порт для демона bgp';
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
			$arrMust = array( 'name', 'ip', 'login', 'password', 'ostype', 'protocol' );
			if ( in_array( $szName, $arrMust ) ) {
				$szIndex = $this->GetAttributeIndex( $szName, $arrConfig, $iMode );
				$szTitle = $this->GetAttributeTitle( $szName, $arrConfig );
				if ( !isset( $arrInput[ $szIndex ] ) ) {
					$objRet->AddError( new CError( 1, "Отсутствует поле '$szTitle'" ), $szName );
				} elseif ( $arrInput[ $szIndex ] === '' ) {
					if ( $szName == 'password' ) {
						if ( $this->key == '' ) {
							$objRet->AddError( new CError( 1, "Поле '$szTitle' пусто" ), $szName );
						}
					} else {
						$objRet->AddError( new CError( 1, "Поле '$szTitle' пусто" ), $szName );
					}
				} elseif ( $szName == 'ip' ) {
					if ( !CValidator::IpAddress( $this->$szName ) ) {
						$objRet->AddError( new CError( 1, "Поле '$szTitle' содержит не допустимое значение" ), $szName );
					}
				} elseif ( $szName == 'ostype' ) {
					$arrOsType = array( 'Cisco', 'Juniper', 'Unix' );
					if ( !in_array( $this->$szName, $arrOsType ) ) {
						$objRet->AddError( new CError( 1, "Поле '$szTitle' содержит не допустимое значение" ), $szName );
					}
				} elseif ( $szName == 'protocol' ) {
					$arrProtocol = array( 'telnet', 'ssh', 'ssh2' );
					if ( !in_array( $this->$szName, $arrProtocol ) ) {
						$objRet->AddError( new CError( 1, "Поле '$szTitle' содержит не допустимое значение" ), $szName );
					}
				}
			}
			return $objRet;
		} // function InitAttr
		
	} // class CAgent
	
	
?>