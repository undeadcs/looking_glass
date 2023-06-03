<?php
	/**
	 *	Гибкий класс
	 *	@author UndeadCS
	 *	@package UndeadCS SDK
	 *	@subpackage Flex
	 */

	// режимы фильтра значений атрибутов
	define( "FLEX_FILTER_DATABASE",	bindec( "00000000000000000000000000000001" ) ); // база данных
	define( "FLEX_FILTER_HTML",	bindec( "00000000000000000000000000000010" ) ); // html (только на вывод)
	define( "FLEX_FILTER_PHP",	bindec( "00000000000000000000000000000100" ) ); // php
	define( "FLEX_FILTER_FORM",	bindec( "00000000000000000000000000001000" ) ); // данные взяты из формы
	define( "FLEX_FILTER_XML",	bindec( "00000000000000000000000000010000" ) ); // xml (только на вывод)

	// TODO: добавить опцию игнорирования атрибутов
	// конфигурационные параметры
	define( "FLEX_CONFIG_INIT",		1	); // инициализация (не используется)
	define( "FLEX_CONFIG_FILTER",		2	); // применение класса фильтра
	define( "FLEX_CONFIG_CREATE",		3	); // создание таблицы
	define( "FLEX_CONFIG_UPDATE",		4	); // обновление объекта
	define( "FLEX_CONFIG_SELECT",		5	); // выборка объекта
	define( "FLEX_CONFIG_COMMON",		6	); // некий общий конфиг для класса
	define( "FLEX_CONFIG_TYPE",		7	); // конфиг типа атрибута
	define( "FLEX_CONFIG_DIGITS",		8	); // опция количества цифр
	define( "FLEX_CONFIG_DECIMAL",		9	); // опция количества цифр после точки в числе
	define( "FLEX_CONFIG_NAME",		10	); // опция имени атрибута
	define( "FLEX_CONFIG_TABLE",		11	); // опция имени таблицы
	define( "FLEX_CONFIG_PREFIX",		12	); // опция префикса
	define( "FLEX_CONFIG_CHARSET",		13	); // опция установки кодировки
	define( "FLEX_CONFIG_COLLATE",		14	); // опция установки сравнения
	define( "FLEX_CONFIG_LENGHT",		15	); // опция длины строки
	define( "FLEX_CONFIG_DEFAULT",		16	); // опция значения по умолчанию
	define( "FLEX_CONFIG_PHP",		17	); // конфигурация для работы в PHP
	define( "FLEX_CONFIG_DATABASE",		18	); // конфигурация для БД
	define( "FLEX_CONFIG_FORM",		19	); // конфигурация для выливания данных в форму
	define( "FLEX_CONFIG_HTML",		20	); // конфигурация для выливания данных в HTML
	define( "FLEX_CONFIG_XML",		21	); // конфигурация для выливания данных в XML
	define( "FLEX_CONFIG_SESSION",		22	); // конфигурация для выливания данных в сессию (в разработке)
	define( "FLEX_CONFIG_TITLE",		23	); // название атрибута (title)
	define( "FLEX_CONFIG_DELETE",		24	); // удаление объекта
	define( "FLEX_CONFIG_XMLNODENAME",	25	); // имя узла XML
	
	// в разработке
	// FCV - Flex Config Value
		// режимы работы
	define( "FCV_PHP",			0	); // PHP
	define( "FCV_DB",			1	); // Database
	define( "FCV_FORM",			2	); // Form
	define( "FCV_XML",			3	); // XML
	define( "FCV_HTML",			4	); // HTML
	define( "FCV_SESSION",			5	); // Session
		// тип действия
	define( "FCV_INIT",			6	); // Инициализация
	define( "FCV_GET",			7	); // Получение данных

	// типы поддерживаемых данных
	define( "FLEX_TYPE_INT",		bindec( "00000000000000000000000000000001" ) ); // целое INT
	define( "FLEX_TYPE_UNSIGNED",		bindec( "00000000000000000000000000000010" ) ); // положительное UNSIGNED
	define( "FLEX_TYPE_FLOAT",		bindec( "00000000000000000000000000000100" ) ); // вещественное FLOAT
	define( "FLEX_TYPE_DOUBLE",		bindec( "00000000000000000000000000001000" ) ); // вещественное DOUBLE
	define( "FLEX_TYPE_STRING",		bindec( "00000000000000000000000000010000" ) ); // строка определенной длины VARCHAR
	define( "FLEX_TYPE_TEXT",		bindec( "00000000000000000000000000100000" ) ); // текст TEXT
	define( "FLEX_TYPE_DATE",		bindec( "00000000000000000000000001000000" ) ); // дата DATETIME
	define( "FLEX_TYPE_REGDATE",		bindec( "00000000000000000000000010000000" ) ); // дата регистрации
	define( "FLEX_TYPE_UPDDATE",		bindec( "00000000000000000000000100000000" ) ); // дата изменения
	// некоторые вещи для создания таблицы
	define( "FLEX_TYPE_NOTNULL",		bindec( "00000000000000000000001000000000" ) ); // NOT NULL
	define( "FLEX_TYPE_AUTOINCREMENT",	bindec( "00000000000000000000010000000000" ) ); // AUTO_INCREMENT
	define( "FLEX_TYPE_DEFAULT",		bindec( "00000000000000000000100000000000" ) ); // DEFAULT
	define( "FLEX_TYPE_PRIMARYKEY",		bindec( "00000000000000000001000000000000" ) ); // PRIMARY KEY
	// сложные типы
	define( "FLEX_TYPE_ARRAY",		bindec( "00000000000000000010000000000000" ) ); // массив
	define( "FLEX_TYPE_OBJECT",		bindec( "00000000000000000100000000000000" ) ); // объект
	define( "FLEX_TYPE_RESOURCE",		bindec( "00000000000000001000000000000000" ) ); // ресурс
	define( "FLEX_TYPE_BOOL",		bindec( "00000000000000010000000000000000" ) ); // булево значение
	// доп параметр для временных атрибутов
	define( "FLEX_TYPE_TIME",		bindec( "00000000000000100000000000000000" ) ); // еще и время
	
	// TODO: читай ниже
	//	обобщить методы работы с объектом
	// 	убрать двоякий конфиг для XML режимов, ввести дополнительную опцию для имени узла
	// 	привести значения конфига и режимы работы к единым индексам и убрать FLEX_FILTER_*
	
	/**
	 *	Гибкий класс
	 */
	class CFlex {
		///////////////////////////////// private:
		
		///////////////////////////////// public:
		
		public function __construct( ) {
		} // class __construct
		
		public function __get( $szName ) {
		} // function __get
		
		public function __set( $szName, $mxdValue ) {
		} // function__set
		
		/**
		 *	Возвращает имя узла DOM для объекта
		 *	@param $arrConfig array массив настроек
		 *	@return string
		 */
		public function GetXMLNodeName( &$arrConfig ) {
			$tmp =  $this->GetAttributeConfigValue( "", $arrConfig, FLEX_CONFIG_XML, FLEX_CONFIG_XMLNODENAME );
			if ( $tmp === NULL ) {
				return get_class( $this );
			}
			return $tmp;
		} // function GetXMLNodeName
		
		/**
		 *	Получение имени атрибута в узле XML
		 *	@param $szName string имя атрибута
		 *	@param $arrConfig array массив настроек
		 *	@return string
		 */
		public function GetAttributeXMLName( $szName, &$arrConfig ) {
			$tmp =  $this->GetAttributeConfigValue( $szName, $arrConfig, FLEX_CONFIG_XML, FLEX_CONFIG_NAME );
			if ( $tmp === NULL ) {
				$tmp = $szName;
			}
			return $tmp;
		} // function GetAttributeXMLName
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = array(
				FLEX_CONFIG_TABLE => get_class( $this ),
				FLEX_CONFIG_XML => array(
					FLEX_CONFIG_XMLNODENAME => get_class( $this )
				)
			);
			$arrAttr = $this->GetAttributeList( );
			foreach( $arrAttr as $i => $v ) {
				if ( is_int( $v ) ) {
					$arrConfig[ $i ] = array(
						FLEX_CONFIG_TYPE => FLEX_TYPE_INT,
						FLEX_CONFIG_DIGITS => 10,
					);
				} elseif ( is_float( $v ) ) {
					$arrConfig[ $i ] = array(
						FLEX_CONFIG_TYPE => FLEX_TYPE_FLOAT,
						FLEX_CONFIG_DIGITS => 10,
						FLEX_CONFIG_DECIMAL => 2
					);
				} elseif ( is_string( $v ) ) {
					$arrConfig[ $i ] = array(
						FLEX_CONFIG_TYPE => FLEX_TYPE_STRING,
						FLEX_CONFIG_LENGHT => 254,
					);
				} elseif ( is_bool( $v ) ) {
					$arrConfig[ $i ] = array(
						FLEX_CONFIG_TYPE => FLEX_TYPE_BOOL
					);
				} elseif ( is_array( $v ) ) {
					$arrConfig[ $i ] = array(
						FLEX_CONFIG_TYPE => FLEX_TYPE_ARRAY
					);
				} elseif ( is_object( $v ) ) {
					$arrConfig[ $i ] = array(
						FLEX_CONFIG_TYPE => FLEX_TYPE_OBJECT
					);
				}
			}
			return $arrConfig;
		} // function GetConfig
		
		/**
		 *	Наполнение параметров объекта
		 *	используя маппинг можно получить разные имена атрибутов сущности в разных хранилищах
		 *	@param $arrInput mixed входные данные для объекта
		 *	@param $iMode int режим, из которого были получены данные
		 *	@return CResult
		 */
		public function Create( $arrInput, $iMode = FLEX_FILTER_PHP ) {
			$objRet = new CResult( );
			if ( is_object( $arrInput ) ) {
				$arrInput = get_object_vars( $arrInput );
			}
			if ( is_array( $arrInput ) ) {
				$arrConfig = $this->GetConfig( );
				$arrAttr = $this->GetAttributeList( );
				foreach( $arrAttr as $i => $v ) {
					$tmp = $this->InitAttr( $i, $arrInput, $arrConfig, $iMode );
					if ( $tmp && $tmp->HasError( ) ) {
						$tmp = $tmp->GetError( );
						foreach( $tmp as $i => $v ) {
							$objRet->AddError( $v, $i );
						}
						$tmp = NULL;
					}
				}
			}
			return $objRet;
		} // function Create
		
		/**
		 *	Получение данных для SELECT
		 *	@return CResult
		 */
		public function GetSQLSelect( ) {
			$mxdRet = array(
				"query" => "", // строка запроса к БД
				"attr" => array( ), // список атрибутов
				"values" => array( ), // список значений атрибутов
			);
			$arrConfig = $this->GetConfig( );
			$arrAttr = $this->GetAttributeList( );
			//
			foreach( $arrAttr as $i => $v ) {
				$szPref = $this->GetAttributePrefix( $i, $arrConfig, FLEX_CONFIG_DATABASE );
				$mxdRet[ "attr" ][ $i ] = "`".$szPref.$i."`";
				$mxdRet[ "values" ][ $i ] = $this->FilterAttr( $i, $arrConfig, FLEX_FILTER_DATABASE );
			}
			//
			$szTable = isset( $arrConfig[ FLEX_CONFIG_TABLE ] ) ? $arrConfig[ FLEX_CONFIG_TABLE ] : "";
			if ( is_string( $szTable ) && $szTable !== "" && !empty( $mxdRet[ "attr" ] ) ) {
				$mxdRet[ "table" ] = $szTable;
				$szTable = "`".@mysql_real_escape_string( $szTable )."`";
				$mxdRet[ "query" ] = "SELECT ".join( ",", $mxdRet[ "attr" ] )." FROM ".$szTable;
				$tmp = $this->GetSQLSelectWhere( $arrConfig );
				$tmp = @strval( $tmp );
				if ( !empty( $tmp ) ) {
					$mxdRet[ "query" ] .= " WHERE ".$tmp;
					$mxdRet[ "where" ] = $tmp;
				}
			}
			$tmp = new CResult( );
			$tmp->AddResult( $mxdRet[ "query" ], "query" );
			$tmp->AddResult( $mxdRet[ "attr" ], "attr" );
			$tmp->AddResult( $mxdRet[ "values" ], "values" );
			if ( isset( $mxdRet[ "where" ] ) ) {
				$tmp->AddResult( $mxdRet[ "where" ], "where" );
			}
			return $tmp;
		} // function GetSQLSelect
		
		/**
		 *	Получение данных для INSERT
		 *	@return CResult
		 */
		public function GetSQLInsert( ) {
			$mxdRet = array(
				"query" => "", // строка запроса к БД
				"attr" => array( ), // список атрибутов
				"values" => array( ), // список значений атрибутов
			);
			$arrConfig = $this->GetConfig( );
			$arrAttr = $this->GetAttributeList( );
			//
			foreach( $arrAttr as $i => $v ) {
				$szPref = $this->GetAttributePrefix( $i, $arrConfig, FLEX_CONFIG_DATABASE );
				$mxdRet[ "attr" ][ $i ] = "`".$szPref.$i."`";
				$mxdRet[ "values" ][ $i ] = $this->FilterAttr( $i, $arrConfig, FLEX_FILTER_DATABASE );
			}
			//
			$szTable = isset( $arrConfig[ FLEX_CONFIG_TABLE ] ) ? $arrConfig[ FLEX_CONFIG_TABLE ] : "";
			if ( is_string( $szTable ) && $szTable !== "" && !empty( $mxdRet[ "attr" ] ) ) {
				$mxdRet[ "table" ] = $szTable;
				$szTable = "`".@mysql_real_escape_string( $szTable )."`";
				$mxdRet[ "query" ] = "INSERT INTO ".$szTable."(".join( ",", $mxdRet[ "attr" ] ).") VALUES (".join( ",", $mxdRet[ "values" ] ).")";
			}
			$tmp = new CResult( );
			$tmp->AddResult( $mxdRet[ "query" ], "query" );
			$tmp->AddResult( $mxdRet[ "attr" ], "attr" );
			$tmp->AddResult( $mxdRet[ "values" ], "values" );
			return $tmp;
		} // function GetSQLInsert
		
		/**
		 *	Получение данных для UPDATE
		 *	@return CResult
		 */
		public function GetSQLUpdate( ) {
			$mxdRet = array(
				"query" => "", // строка запроса к БД
				"attr" => array( ), // список атрибутов
				"values" => array( ), // список значений атрибутов
			);
			$arrConfig = $this->GetConfig( );
			$arrAttr = $this->GetAttributeList( );
			foreach( $arrAttr as $i => $v ) {
				$szPref = $this->GetAttributePrefix( $i, $arrConfig, FLEX_CONFIG_DATABASE );
				$mxdRet[ "attr" ][ $i ] = "`".$szPref.$i."`";
				$mxdRet[ "values" ][ $i ] = $this->FilterAttr( $i, $arrConfig, FLEX_FILTER_DATABASE );
			}
			$szTable = isset( $arrConfig[ FLEX_CONFIG_TABLE ] ) ? $arrConfig[ FLEX_CONFIG_TABLE ] : "";
			if ( is_string( $szTable ) && $szTable !== "" && !empty( $mxdRet[ "values" ] ) ) {
				$mxdRet[ "table" ] = $szTable;
				$szTable = "`".@mysql_real_escape_string( $szTable )."`";
				$tmp = "";
				foreach( $mxdRet[ "values" ] as $i => $v ) {
					$tmp[ ] = $mxdRet[ "attr" ][ $i ]."=".$v;
				}
				$mxdRet[ "query" ] = "UPDATE ".$szTable." SET ".join( ",", $tmp );
				$tmp = $this->GetSQLUpdateWhere( $arrConfig );
				$tmp = @strval( $tmp );
				if ( !empty( $tmp ) ) {
					$mxdRet[ "query" ] .= " WHERE ".$tmp;
					$mxdRet[ "where" ] = $tmp;
				}
			}
			$tmp = new CResult( );
			$tmp->AddResult( $mxdRet[ "query" ], "query" );
			$tmp->AddResult( $mxdRet[ "attr" ], "attr" );
			$tmp->AddResult( $mxdRet[ "values" ], "values" );
			return $tmp;
		} // function GetSQLUpdate
		
		/**
		 *	Получение данных для CREATE
		 *	@return CResult
		 */
		public function GetSQLCreate( ) {
			$mxdRet = array(
				"query" => "", // строка запроса к БД
				"attr" => array( ), // список атрибутов
				"values" => array( ), // список значений атрибутов
			);
			$arrConfig = $this->GetConfig( );
			$arrAttr = $this->GetAttributeList( );
			foreach( $arrAttr as $i => $v ) {
				$szPref = $this->GetAttributePrefix( $i, $arrConfig, FLEX_CONFIG_DATABASE );
				$mxdRet[ "attr" ][ $i ] = "`".$szPref.$i."`";
				$mxdRet[ "values" ][ $i ] = $this->GetAttributeCreate( $i, $arrConfig );
			}
			$szTable = isset( $arrConfig[ FLEX_CONFIG_TABLE ] ) ? $arrConfig[ FLEX_CONFIG_TABLE ] : "";
			if ( is_string( $szTable ) && $szTable !== "" && !empty( $mxdRet[ "values" ] ) ) {
				$mxdRet[ "table" ] = $szTable;
				$szTable = "`".@mysql_real_escape_string( $szTable )."`";
				$mxdRet[ "query" ] = "CREATE TABLE IF NOT EXISTS ".$szTable." (".join( ",", $mxdRet[ "values" ] ).")";
			}
			$tmp = new CResult( );
			$tmp->AddResult( $mxdRet[ "query" ], "query" );
			$tmp->AddResult( $mxdRet[ "attr" ], "attr" );
			$tmp->AddResult( $mxdRet[ "values" ], "values" );
			return $tmp;
		} // function GetSQLCreate
		
		/**
		 *	Получение данных для DELETE
		 *	@return CResult
		 */
		public function GetSQLDelete( ) {
			$objRet = new CResult( );
			$arrConfig = $this->GetConfig( );
			$szPref = $this->GetAttributePrefix( "", $arrConfig, FLEX_CONFIG_DATABASE );
			$szDelIndex = $this->GetAttributeConfigValue( "", $arrConfig, FLEX_CONFIG_DATABASE, FLEX_CONFIG_DELETE );
			if ( $szDelIndex !== NULL ) {
				$mxdValue = $this->FilterAttr( $szDelIndex, $arrConfig, FLEX_FILTER_DATABASE );
				$objRet->AddResult( "`".$szPref.$szDelIndex."`", "attr" );
				$objRet->AddResult( $mxdValue, "values" );
				
				$szTable = isset( $arrConfig[ FLEX_CONFIG_TABLE ] ) ? $arrConfig[ FLEX_CONFIG_TABLE ] : "";
				if ( is_string( $szTable ) && $szTable != "" && !empty( $mxdRet[ "values" ] ) ) {
					$mxdRet[ "table" ] = $szTable;
					$szTable = "`".@mysql_real_escape_string( $szTable )."`";
					$szQuery = "DELETE FROM ".$szTable." WHERE `".$szPref.$szDelIndex."`=".$mxdValue;
				}
			}
			return $objRet;
		} // function GetSQLDelete
		
		/**
		 *	Получение условия, по которому делается SELECT
		 *	@param $arrConfig array набор настроек
		 *	@return string
		 */
		public function GetSQLSelectWhere( $arrConfig = NULL ) {
			if ( $arrConfig === NULL ) {
				$arrConfig = $this->GetConfig( );
			}
			$szWhere = "";
			if ( isset( $arrConfig[ FLEX_CONFIG_SELECT ] ) ) {
				$tmp = $arrConfig[ FLEX_CONFIG_SELECT ];
				if ( isset( $this->$tmp ) ) {
					$szPref = $this->GetAttributePrefix( $tmp, $arrConfig, FLEX_CONFIG_DATABASE );
					$szWhere = $szPref.$tmp."=".$this->FilterAttr( $tmp, $arrConfig, FLEX_FILTER_DATABASE );
				}
			}
			return $szWhere;
		} // function GetSQLSelectWhere
		
		/**
		 *	Получение условия, по которому делается UPDATE
		 *	@return $arrConfig array набор настроек класса
		 *	@return string
		 */
		public function GetSQLUpdateWhere( $arrConfig = NULL ) {
			if ( $arrConfig === NULL ) {
				$arrConfig = $this->GetConfig( );
			}
			$szWhere = "";
			if ( isset( $arrConfig[ FLEX_CONFIG_UPDATE ] ) ) {
				$tmp = $arrConfig[ FLEX_CONFIG_UPDATE ];
				if ( isset( $this->$tmp ) ) {
					$szPref = $this->GetAttributePrefix( $tmp, $arrConfig, FLEX_CONFIG_DATABASE );
					$szWhere = $szPref.$tmp."=".$this->FilterAttr( $tmp, $arrConfig, FLEX_FILTER_DATABASE );
				}
			}
			return $szWhere;
		} // function GetSQLUpdateWhere
		
		/**
		 * 	Получение имени таблицы хранилища экземпляров
		 */
		public function GetSQLTableOwner( $arrConfig = NULL ) {
			if ( $arrConfig === NULL ) {
				$arrConfig = $this->GetConfig( );
			}
			return isset( $arrConfig[ FLEX_CONFIG_TABLE ] ) ? $arrConfig[ FLEX_CONFIG_TABLE ] : "";
		} // function GetSQLTableOwner
		
		/**
		 *	Получение формы экземпляра
		 *	@return CResult
		 */
		public function GetForm( ) {
			$mxdRet = new CResult( );
			return $mxdRet;
		} // function GetForm
		
		/**
		 *	Получение XML экземпляра
		 *	@param $domDoc DOMDocument экземпляр данного класса
		 *	@return CResult
		 */
		public function GetXML( &$domDoc ) {
			$objRet = new CResult( );
			if ( is_object( $domDoc ) && ( get_class( $domDoc ) == "DOMDocument" ) ) {
				$arrConfig = $this->GetConfig( );
				$szXMLNode = $this->GetXMLNodeName( $arrConfig );
				$doc = $domDoc->createElement( $szXMLNode );
				$arrAttr = $this->GetAttributeList( );
				foreach( $arrAttr as $i => $v ) {
					$tmp = $this->FilterAttr( $i, $arrConfig, FLEX_FILTER_XML );
					if ( $tmp !== NULL ) {
						$szPref = $this->GetAttributePrefix( $i, $arrConfig, FLEX_CONFIG_XML );
						$szName = $this->GetAttributeXMLName( $i, $arrConfig );
						$doc->setAttribute( $szPref.$szName, $tmp );
					}
				}
				$objRet->AddResult( $doc, "doc" );
			} else {
				$objRet->AddError( new CError( 0, "Wrong domDoc object type" ) );
			}
			return $objRet;
		} // function GetXML
		
		/**
		 *	Получение HTML экземпляра
		 *	@return CResult
		 */
		public function GetHTML( ) {
			$mxdRet = new CResult( );
			return $mxdRet;
		} // function GetHTML
		
		/**
		 *	Получение данных в виде массива
		 *	@return CResult
		 */
		public function GetArray( ) {
			$objRet = new CResult( );
			$arrConfig = $this->GetConfig( );
			$arrAttr = $this->GetAttributeList( );
			foreach( $arrAttr as $i => $v ) {
				$tmp = $this->FilterAttr( $i, $arrConfig );
				if ( $tmp !== NULL ) {
					//$szIndex = $this->GetAttributeIndex( $i, $arrConfig, FLEX_FILTER_PHP );
					$objRet->AddResult( $tmp, $i );
				}
			}
			return $objRet;
		} // function GetArray
		
		///////////////////////////////// protected:
		
		/**
		 *	Возвращает правильную последовательность атрибутов, от корневого потомка к текущему
		 *	@return array
		 */
		protected function GetAttributeList( ) {
			$mxdRet = array( );
			foreach( $this as $i => $v ) {
				$mxdRet[ $i ] = $v;
			}
			$szParent = get_parent_class( $this );
			if ( $szParent !== false ) {
				$tmp = new $szParent( );
				$tmp1 = $tmp->GetAttributeList( );
				if ( $tmp1 ) {
					foreach( $tmp1 as $i => $v ) {
						if ( isset( $mxdRet[ $i ] ) ) {
							unset( $mxdRet[ $i ] );
						}
					}
					$mxdRet = array_merge( $tmp1, $mxdRet );
				}
			}
			return $mxdRet;
		} // function GetAttributeList
		
		/**
		 *	Обрабатывает строку из конфига для заданного параметра
		 *	@param $szName string имя атрибута
		 *	@param $arrConfig array набор настроек
		 *	@return mixed
		 */
		protected function GetAttributeCreate( $szName, $arrConfig = NULL ) {
			if ( $arrConfig === NULL ) {
				$arrConfig = $this->GetConfig( );
			}
			$mxdRet = false;
			$arrConfigRow = isset( $arrConfig[ $szName ] ) ? $arrConfig[ $szName ] : array( FLEX_CONFIG_LENGHT => 255 );
			$iType = intval( $arrConfigRow[ FLEX_CONFIG_TYPE ] );
			$tmp = array( );
			if ( $iType & FLEX_TYPE_INT ) {
				$tmp1 = "INT";
				if ( isset( $arrConfigRow[ FLEX_CONFIG_DIGITS ] ) ) {
					$tmp1 .= "(".$arrConfigRow[ FLEX_CONFIG_DIGITS ].")";
				}
				$tmp[ ] = $tmp1;
			} elseif ( $iType & FLEX_TYPE_FLOAT ) {
				$tmp1 = "FLOAT";
				if ( isset( $arrConfigRow[ FLEX_CONFIG_DIGITS ], $arrConfigRow[ FLEX_CONFIG_DECIMAL ] ) ) {
					$tmp1 .= "(".$arrConfigRow[ FLEX_CONFIG_DIGITS ].",".$arrConfigRow[ FLEX_CONFIG_DECIMAL ].")";
				}
				$tmp[ ] = $tmp1;
			} elseif ( $iType & FLEX_TYPE_DOUBLE ) {
				$tmp1 = "DOUBLE";
				if ( isset( $arrConfigRow[ FLEX_CONFIG_DIGITS ], $arrConfigRow[ FLEX_CONFIG_DECIMAL ] ) ) {
					$tmp1 .= "(".$arrConfigRow[ FLEX_CONFIG_DIGITS ].",".$arrConfigRow[ FLEX_CONFIG_DECIMAL ].")";
				}
				$tmp[ ] = $tmp1;
			} elseif ( $iType & FLEX_TYPE_STRING ) {
				$tmp1 = "VARCHAR";
				if ( isset( $arrConfigRow[ FLEX_CONFIG_LENGHT ] ) ) {
					$tmp1 .= "(".$arrConfigRow[ FLEX_CONFIG_LENGHT ].")";
				}
				$tmp[ ] = $tmp1;
			} elseif ( $iType & FLEX_TYPE_TEXT || $iType & FLEX_TYPE_ARRAY || $iType & FLEX_TYPE_OBJECT ) {
				$tmp[ ] = "TEXT";
			} elseif ( $iType & FLEX_TYPE_DATE ) {
				if ( $iType & FLEX_TYPE_TIME ) {
					$tmp[ ] = "DATETIME";
				} else {
					$tmp[ ] = "DATE";
				}
			} elseif ( $iType & FLEX_TYPE_TIME ) {
				$tmp[ ] = "TIME";
			} else {
				$tmp[ ] = "VARCHAR(255)";
			}
			if ( $iType & FLEX_TYPE_NOTNULL ) {
				$tmp[ ] = "NOT NULL";
			}
			if ( $iType & FLEX_TYPE_DEFAULT ) {
				if ( isset( $arrConfigRow[ FLEX_CONFIG_DEFAULT ] ) ) {
					$tmp[ ] = "DEFAULT '".$arrConfigRow[ FLEX_CONFIG_DEFAULT ]."'";
				}
			}
			if ( $iType & FLEX_TYPE_AUTOINCREMENT ) {
				$tmp[ ] = "AUTO_INCREMENT";
			}
			if ( $iType & FLEX_TYPE_PRIMARYKEY ) {
				$tmp[ ] = "PRIMARY KEY";
			}
			if ( !empty( $tmp ) ) {
				$szPref = $this->GetAttributePrefix( $szName, $arrConfig, FLEX_CONFIG_DATABASE );
				$mxdRet = "`".$szPref.$szName."` ".join( " ", $tmp );
			}
			return $mxdRet;
		}
		
		/**
		 *	Получение значения конфига
		 *	@param $szName string имя атрибута
		 *	@param $arrConfig array массив настроек
		 *	@param $iConfigMode int режим работы
		 *	@param $iConfigValue int опция конфига, которую нужно получить
		 *	@return mixed
		 */
		protected function GetAttributeConfigValue( $szName, &$arrConfig, $iConfigMode, $iConfigValue ) {
			if ( isset( $arrConfig[ $szName ][ $iConfigMode ][ $iConfigValue ] ) ) {
				return $arrConfig[ $szName ][ $iConfigMode ][ $iConfigValue ];
			} elseif ( isset( $arrConfig[ $szName ][ $iConfigValue ] ) ) {
				return $arrConfig[ $szName ][ $iConfigValue ];
			} elseif ( isset( $arrConfig[ $iConfigMode ][ $iConfigValue ] ) ) {
				return $arrConfig[ $iConfigMode ][ $iConfigValue ];
			} elseif ( isset( $arrConfig[ $iConfigValue ] ) ) {
				return $arrConfig[ $iConfigValue ];
			}
			return NULL;
		} // function GetAttributeConfigValue
		
		/**
		 *	Получение префикса, учитывая настройки
		 *	@param $szName string имя атрибута
		 *	@param $arrConfig array массив настроек]
		 *	@param $iConfigMode int режим работы
		 *	@return mixed
		 */
		protected function GetAttributePrefix( $szName, &$arrConfig, $iConfigMode = FLEX_FILTER_PHP ) {
			return $this->GetAttributeConfigValue( $szName, $arrConfig, $iConfigMode, FLEX_CONFIG_PREFIX );
		} // function GetAttributePrefix
		
		/**
		 * 	Получение ассоциативного массива индексов атрибутов для определенного режима
		 * 	@param $iMode int режим работы
		 * 	@return array
		 */
		public function GetAttributeIndexList( $iMode = FLEX_FILTER_PHP ) {
			$arrRet = array( );
			$arrConfig = $this->GetConfig( );
			$arrAttr = $this->GetAttributeList( );
			foreach( $arrAttr as $i => $v ) {
				$arrRet[ $i ] = $this->GetAttributeIndex( $i, $arrConfig, $iMode );
			}
			return $arrRet;
		} // function GetAttributeIndexList
		
		/**
		 *	Получение ассоциативного индекса атрибута во входящем массиве
		 *	@param $szName string имя атрибута
		 *	@param $arrConfig array конфиг
		 *	@param $iMode int режим
		 *	@return string
		 */
		public function GetAttributeIndex( $szName, $arrConfig = NULL, $iMode = FLEX_FILTER_PHP ) {
			if ( $arrConfig === NULL ) {
				$arrConfig = $this->GetConfig( );
			}
			$szIndex = NULL;
			$iPrefMode = NULL;
			if ( $iMode & FLEX_FILTER_DATABASE ) {
				$iPrefMode = FLEX_CONFIG_DATABASE;
			} elseif ( $iMode & FLEX_FILTER_FORM ) {
				$iPrefMode = FLEX_CONFIG_FORM;
			} else {
				$iPrefMode = FLEX_CONFIG_PHP;
			}
			$szIndex = $this->GetAttributeConfigValue( $szName, $arrConfig, $iPrefMode, FLEX_CONFIG_NAME );
			if ( $szIndex === NULL ) {
				$szIndex = $szName;
			}
			$szPref = $this->GetAttributePrefix( $szName, $arrConfig, $iPrefMode );
			$szIndex = $szPref.$szIndex;
			return $szIndex;
		} // function GetAttributeIndex
		
		/**
		 * 	Получение значения атрибута
		 * 	@param $szName string имя атрибута
		 * 	@param $iMode int режим работы
		 * 	@return mixed
		 */
		public function GetAttributeValue( $szName, $iMode = FLEX_FILTER_PHP ) {
			$arrConfig = $this->GetConfig( );
			return $this->FilterAttr( $szName, $arrConfig, $iMode );
		} // function GetAttributeValue
		
		/**
		 * 	Получение заголовка атрибута
		 * 	@param $szName string имя атрибута
		 * 	@param $arrConfig array набор настроек
		 * 	@return string
		 */
		public function GetAttributeTitle( $szName, $arrConfig = NULL ) {
			if ( $arrConfig === NULL ) {
				$arrConfig = $this->GetConfig( );
			}
			return ( isset( $arrConfig[ $szName ][ FLEX_CONFIG_TITLE ] ) ? strval( $arrConfig[ $szName ][ FLEX_CONFIG_TITLE ] ) : $szName );
		} // function GetAttributeTitle
		
		/**
		 *	Инициализация атрибута объекта
		 *	@param $szName string имя атрибута
		 *	@param $arrInput mixed некое значение
		 *	@param $arrConfig array конфиг объекта
		 *	@param $iMode int режим фильтрации
		 *	@return CResult
		 */
		protected function InitAttr( $szName, &$arrInput, &$arrConfig, $iMode = FLEX_FILTER_PHP ) {
			$objRet = new CResult( );
			$arrConfigRow = isset( $arrConfig[ $szName ] ) ? $arrConfig[ $szName ] : array( FLEX_CONFIG_LENGHT => 255 );
			$iType = isset( $arrConfigRow[ FLEX_CONFIG_TYPE ] ) ? $arrConfigRow[ FLEX_CONFIG_TYPE ] : FLEX_TYPE_STRING;
			// получаем значение из входных данных, учитывая конфиг
			$tmp = NULL;
			// получение значения
			$szIndex = $this->GetAttributeIndex( $szName, $arrConfig, $iMode );
			if ( isset( $arrInput[ $szIndex ] ) ) {
				$tmp = $arrInput[ $szIndex ];
			} else {
				$tmp = $this->$szName;
			}
			// фильтрация
			$iLength = false;
			if ( $iType & FLEX_TYPE_INT ) {
				$tmp = @intval( $tmp );
				if ( $iType & FLEX_TYPE_UNSIGNED ) {
					$tmp = abs( $tmp );
				}
			} elseif ( $iType & FLEX_TYPE_FLOAT || $iType & FLEX_TYPE_DOUBLE ) {
				$tmp = @floatval( $tmp );
				if ( isset( $arrConfigRow[ FLEX_CONFIG_DECIMAL ] ) ) {
					$tmp = @round( $tmp, intval( $arrConfigRow[ FLEX_CONFIG_DECIMAL ] ) );
				}
			} elseif ( $iType & FLEX_TYPE_STRING || $iType & FLEX_TYPE_TEXT ) {
				$tmp = @strval( $tmp );
				$tmp = trim( $tmp );
				if ( $iMode & FLEX_FILTER_DATABASE ) {
					$tmp = @strval( $tmp );
					if ( $iLength !== false && $tmp !== "" ) {
						$tmp = substr( $tmp, 0, $iLength );
					}
				} elseif ( $iMode & FLEX_FILTER_FORM ) {
					if ( get_magic_quotes_gpc( ) ) {
						$tmp = stripslashes( $tmp );
					}
				}
				if ( isset( $arrConfig[ $szName ][ FLEX_CONFIG_LENGHT ] ) ) {
					$iLength = @intval( $arrConfig[ $szName ][ FLEX_CONFIG_LENGHT ] );
				}
				if ( $iLength && strlen( $tmp ) ) {
					$tmp = substr( $tmp, 0, $iLength );
				}
			}
			$this->$szName = $tmp;
			return $objRet;
		} // function InitAttr
		
		/**
		 *	Получение значения атрибута объекта
		 *	@param $szName string имя атрибута
		 *	@param $arrConfig array конфиг объекта
		 *	@param $iMode int режим фильтрации
		 *	@return mixed
		 */
		protected function FilterAttr( $szName, &$arrConfig, $iMode = FLEX_FILTER_PHP ) {
			$arrConfigRow = isset( $arrConfig[ $szName ] ) ? $arrConfig[ $szName ] : array( FLEX_CONFIG_LENGHT => 255 );
			$iType = isset( $arrConfigRow[ FLEX_CONFIG_TYPE ] ) ? $arrConfigRow[ FLEX_CONFIG_TYPE ] : FLEX_TYPE_STRING;
			// типизация
			$iLength = false;
			$tmp = $this->$szName;
			if ( $iType & FLEX_TYPE_INT ) {
				$tmp = @intval( $tmp );
			} elseif ( ( $iType & FLEX_TYPE_FLOAT ) | ( $iType & FLEX_TYPE_DOUBLE ) ){
				$tmp = @floatval( $tmp );
			} elseif ( $iType & FLEX_TYPE_STRING || $iType & FLEX_TYPE_TEXT ) {
				if ( isset( $arrConfigRow[ FLEX_CONFIG_LENGHT ] ) ) {
					$iLength = @intval( $arrConfigRow[ FLEX_CONFIG_LENGHT ] );
				}
				$tmp = @strval( $tmp );
				if ( $iLength && strlen( $tmp ) ) {
					$tmp = substr( $tmp, 0, $iLength );
				}
			} elseif ( $iType & FLEX_TYPE_ARRAY || $iType & FLEX_TYPE_OBJECT || $iType & FLEX_TYPE_OBJECT ) {
				$tmp = "";
			} elseif ( $iType & FLEX_TYPE_DATE ) {
				$tmp = @strval( $tmp );
			}
			
			// наполнение
			if ( $iMode & FLEX_FILTER_PHP ) {
			} elseif ( $iMode & FLEX_FILTER_DATABASE ) {
				if ( $iType & FLEX_TYPE_STRING || $iType & FLEX_TYPE_TEXT ) {
					$tmp = @strval( $tmp );
					if ( $iLength !== false && $tmp !== "" ) {
						$tmp = substr( $tmp, 0, $iLength );
					}
					$tmp = @mysql_real_escape_string( $tmp );
					$tmp = "'".$tmp."'";
				} elseif ( $iType & FLEX_TYPE_DATE ) {
					$tmp = "'".$tmp."'";
				}
			} elseif ( $iMode & FLEX_FILTER_FORM ) {
			} elseif ( $iMode & FLEX_FILTER_HTML ) {
			} elseif ( $iMode & FLEX_FILTER_XML ) {
				if ( $iType & FLEX_TYPE_STRING || $iType & FLEX_TYPE_TEXT ) {
					$tmp = html_entity_decode( $tmp );
					$tmp = iconv( "cp1251", "UTF-8//TRANSLIT", $tmp );
				}
			}
			return $tmp;
		} // function FilterAttr
		
	} // class CFlex
	
	// FHCV -  Flex Handler Config Value
	define( "FHCV_TABLE",			1	); // имя таблицы, в которой хранятся сущности
	define( "FHCV_OBJECT_NAME",		2	); // имя класса объекта сущностей
	
	// Опции обработчика FHOV - Flex Handler Option Value
	define( "FHOV_WHERE",			1	); // условие WHERE
	define( "FHOV_GROUP",			2	); // условие GROUP
	define( "FHOV_ORDER",			3	); // условие ORDER
	define( "FHOV_LIMIT",			4	); // условие LIMIT
	define( "FHOV_TABLE",			5	); // явное указание таблицы
	define( "FHOV_OBJECT",			6	); // указание имени класса объектов
	define( "FHOV_INDEXATTR",		7	); // указание индексного атрибута
	define( "FHOV_IGNOREATTR",		8	); // игнорирование атрибутов
	define( "FHOV_ONLYATTR",		9	); // выбор только указанных атрибутов
	define( "FHOV_FORCETABLE",		10	); // застявляет использовать введенное имя таблицы
	define( "FHOV_PAGE",			11	); // страница
	define( "FHOV_PAGESIZE",		12	); // размер страницы
	
	/**
	 *	Обработчик объектов
	 */
	class CFlexHandler extends CFlex {
		protected $database = NULL;
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = array( );
			return $arrConfig;
		} // function GetConfig
		
		/**
		 *	Проверяет таблицу
		 *	@param $arrOptions array набор настроек
		 *	@return void
		 */
		public function CheckTable( $arrOptions ) {
			$szTable = strval( isset( $arrOptions[ FHOV_TABLE ] ) ? $arrOptions[ FHOV_TABLE ] : "" );
			$szObjectName = strval( isset( $arrOptions[ FHOV_OBJECT ] ) ? $arrOptions[ FHOV_OBJECT ] : "" );
			
			if ( !empty( $szTable ) && !empty( $szObjectName ) && $this->database !== NULL ) {
				// проверим наличие таблицы, при отсутствии создадим
				$bForceCreate = false; // заставить создать таблицу
				$szQuery = "SHOW TABLES FROM `".$this->database->db_name."`";
				$tmp = $this->database->Query( $szQuery );
				if ( $tmp->has_result ) {
					$tmp = $tmp->result;
					$tmp1 = array( );
					foreach( $tmp as $v ) {
						$szTblName = $v[ "Tables_in_".$this->database->db_name ];
						$tmp1[ $szTblName ] = $szTblName;
					}
					if ( isset( $tmp1[ $szTable ] ) ) {
						// тут нужно делать проверку на альтеринг
					} else {
						$bForceCreate = true;
					}
				} elseif ( !$tmp->has_error ) {
					// когда таблицы вообще нет
					$bForceCreate = true;
				}
				if ( $bForceCreate ) {
					$tmp = new $szObjectName( );
					$tmp = $tmp->GetSQLCreate( );
					if ( $tmp->HasResult( ) ) {
						$szQuery = "";
						if ( isset( $arrOptions[ FHOV_FORCETABLE ] ) && ( $arrOptions[ FHOV_FORCETABLE ] === true ) ) {
							$arrValues = $tmp->GetResult( "values" );
							$tmp = array( );
							foreach( $arrValues as $v ) {
								$tmp[ ] = $v;
							}
							if ( !empty( $tmp ) ) {
								$szQuery = "CREATE TABLE `".$szTable."` (".join( ",", $tmp ).")";
							}
						} else {
							$szQuery = $tmp->GetResult( "query" );
						}
						$this->database->Query( $szQuery );
					}
				}
			}
		} // function CheckTable
		
		/**
		 *	Получение объектов
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function GetObject( $arrOptions = array( ) ) {
			$mxdRet = new CResult( );
			$szTable = strval( isset( $arrOptions[ FHOV_TABLE ] ) ? $arrOptions[ FHOV_TABLE ] : "" );
			$szObjectName = strval( isset( $arrOptions[ FHOV_OBJECT ] ) ? $arrOptions[ FHOV_OBJECT ] : "" );
			
			if ( !empty( $szTable ) && !empty( $szObjectName ) && $this->database !== NULL ) {
				$szAttr = "*";
				if ( isset( $arrOptions[ FHOV_IGNOREATTR ] ) || isset( $arrOptions[ FHOV_ONLYATTR ] ) ) {
					$tmpObject = new $szObjectName( );
					$tmp = $tmpObject->GetSQLSelect( );
					$arrAttr = $tmp->GetResult( "attr" );
					if ( isset( $arrOptions[ FHOV_IGNOREATTR ] ) && is_array( $arrOptions[ FHOV_IGNOREATTR ] ) ) {
						foreach( $arrOptions[ FHOV_IGNOREATTR ] as $v ) {
							if ( is_string( $v ) ) {
								if ( isset( $arrAttr[ $v ] ) ) {
									unset( $arrAttr[ $v ] );
								}
							}
						}
					}
					if ( isset( $arrOptions[ FHOV_ONLYATTR ] ) && is_array( $arrOptions[ FHOV_ONLYATTR ] ) ) {
						foreach( $arrAttr as $i => $v ) {
							if ( !in_array( $i, $arrOptions[ FHOV_ONLYATTR ] ) ) {
								unset( $arrAttr[ $i ] );
							}
						}
					}
					if ( !empty( $arrAttr ) ) {
						$szAttr = join( ",", $arrAttr );
					}
				}
				//
				$szQuery = "SELECT ".$szAttr." FROM ".$szTable;
				$arrTail = array( );
				if ( isset( $arrOptions[ FHOV_WHERE ] ) ) {
					$arrTail[ ] = "WHERE ".$arrOptions[ FHOV_WHERE ];
				}
				if ( isset( $arrOptions[ FHOV_GROUP ] ) ) {
					$arrTail[ ] = "GROUP BY ".$arrOptions[ FHOV_GROUP ];
				}
				if ( isset( $arrOptions[ FHOV_ORDER ] ) ) {
					$arrTail[ ] = "ORDER BY ".$arrOptions[ FHOV_ORDER ];
				}
				if ( isset( $arrOptions[ FHOV_LIMIT ] ) ) {
					$arrTail[ ] = "LIMIT ".$arrOptions[ FHOV_LIMIT ];
				}
				if ( !empty( $arrTail ) ) {
					$szQuery .= " ".join( " ", $arrTail );
				}
				$tmp = $this->database->Query( $szQuery );
				if ( $tmp->HasError( ) ) {
					$mxdRet->AddError( $tmp );
				}
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( );
					$szIndexAttr = strval( isset( $arrOptions[ FHOV_INDEXATTR ] ) ? $arrOptions[ FHOV_INDEXATTR ] : "" );
					foreach( $tmp as $i => $v ) {
						$tmpObject = new $szObjectName( );
						$tmpObject->Create( $v, FLEX_FILTER_DATABASE );
						if ( empty( $szIndexAttr ) ) {
							$mxdRet->AddResult( $tmpObject );
						} else {
							$mxdRet->AddResult( $tmpObject, $tmpObject->GetAttributeValue( $szIndexAttr, FLEX_FILTER_PHP ) );
						}
					}
				}
			}
			
			return $mxdRet;
		} // function GetObject
		
		/**
		 *	Добавление объектов
		 *	@param $arrInput array набор новых объектов
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function AddObject( $arrInput, $arrOptions = array( ) ) {
			$objRet = new CResult( );
			$szTable = strval( isset( $arrOptions[ FHOV_TABLE ] ) ? $arrOptions[ FHOV_TABLE ] : "" );
			
			if ( !empty( $szTable ) && $this->database !== NULL ) {
				$arrInsert = array( );
				$szAttr = "";
				$szValues = "";
				foreach( $arrInput as $i => $v ) {
					$v->Create( $arrOptions );
					$tmp = $v->GetSQLInsert( );
					if ( $tmp->HasResult( ) ) {
						$arrAttr = $tmp->result[ "attr" ];
						$arrValues = $tmp->result[ "values" ];
						if ( isset( $arrOptions[ FHOV_IGNOREATTR ] ) && is_array( $arrOptions[ FHOV_IGNOREATTR ] ) ) {
							foreach( $arrOptions[ FHOV_IGNOREATTR ] as $v ) {
								if ( is_string( $v ) ) {
									if ( isset( $arrAttr[ $v ] ) ) {
										unset( $arrAttr[ $v ] );
									}
									if ( isset( $arrValues[ $v ] ) ) {
										unset( $arrValues[ $v ] );
									}
								}
							}
						}
						if ( isset( $arrOptions[ FHOV_ONLYATTR ] ) && is_array( $arrOptions[ FHOV_ONLYATTR ] ) ) {
							foreach( $arrAttr as $i => $v ) {
								if ( !in_array( $i, $arrOptions[ FHOV_ONLYATTR ] ) ) {
									unset( $arrAttr[ $i ] );
								}
							}
							foreach( $arrValues as $i => $v ) {
								if ( !in_array( $i, $arrOptions[ FHOV_ONLYATTR ] ) ) {
									unset( $arrAttr[ $i ] );
								}
							}
						}
						if ( $szAttr == "" ) {
							$szAttr = join( ",", $arrAttr );
						}
						$arrInsert[ ] = "(".join( ",", $arrValues ).")";
					}
				}
				$szValues = join( ",", $arrInsert );
				$szQuery = "INSERT INTO `".$szTable."`(".$szAttr.") VALUES ".$szValues;
				$tmp = $this->database->Query( $szQuery );
				if ( $tmp->HasError( ) ) {
					$objRet->AddError( $this->database->GetError( ) );
				} else {
					$objRet->AddResult( $this->database->GetInsertId( ), "insert_id" );
					$objRet->AddResult( $this->database->GetAffectedRows( ), "affected_rows" );
				}
			}
			
			return $objRet;
		} // function AddObject
		
		/**
		 *	Удаление объекта
		 *	@param $arrInput array массив экземпляров класса
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function DelObject( $arrInput, $arrOptions = array( ) ) {
			$objRet = new CResult( );
			$szTable = strval( isset( $arrOptions[ FHOV_TABLE ] ) ? $arrOptions[ FHOV_TABLE ] : "" );
			
			if ( !empty( $szTable ) && $this->database !== NULL ) {
				$szAttrDel = "";
				$arrToDel = array( );
				foreach( $arrInput as $i => $v ) {
					// ошибки игнорируем, т.к. нам нужно заполнить не все атрибуты
					$v->Create( $arrOptions );
					$tmp = $v->GetSQLDelete( );
					if ( $tmp->HasResult( ) ) {
						if ( $szAttrDel == "" ) {
							$szAttrDel = $tmp->GetResult( "attr" );
						}
						$arrToDel[ ] = $tmp->GetResult( "values" );
					}
				}
				if ( !empty( $szAttrDel ) && !empty( $arrToDel ) ) {
					$szQuery = "DELETE FROM `".$szTable."` WHERE ".$szAttrDel." IN(".join( ",", $arrToDel ).")";
					$objRet = $this->database->Query( $szQuery );
				}
			}
						
			return $objRet;
		} // function DelObject
		
		/**
		 *	Обновление объектов
		 *	@param $arrInput array массив экземпляров класса
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function UpdObject( $arrInput, $arrOptions = array( ) ) {
			$objRet = new CResult( );
			$szTable = strval( isset( $arrOptions[ FHOV_TABLE ] ) ? $arrOptions[ FHOV_TABLE ] : "" );
			$szIndexAttr = strval( isset( $arrOptions[ FHOV_INDEXATTR ] ) ? $arrOptions[ FHOV_INDEXATTR ] : "" );
			
			if ( !empty( $szTable ) && !empty( $szIndexAttr ) && $this->database !== NULL ) {
				foreach( $arrInput as $i => $v ) {
					$tmp = $v->GetSQLUpdate( );
					$szUpdBy = "";
					$arrAttr = $tmp->GetResult( "attr" );
					$arrValues = $tmp->GetResult( "values" );
					$szUpdBy = $arrAttr[ $szIndexAttr ]."=".$arrValues[ $szIndexAttr ];
					unset( $arrAttr[ $szIndexAttr ], $arrValues[ $szIndexAttr ] );
					if ( isset( $arrOptions[ FHOV_IGNOREATTR ] ) ) {
						foreach( $arrOptions[ FHOV_IGNOREATTR ] as $j => $w ) {
							if ( isset( $arrAttr[ $w ] ) ) {
								unset( $arrAttr[ $w ] );
							}
							if ( isset( $arrValues[ $w ] ) ) {
								unset( $arrValues[ $w ] );
							}
						}
					}
					if ( isset( $arrOptions[ FHOV_ONLYATTR ] ) && is_array( $arrOptions[ FHOV_ONLYATTR ] ) ) {
						foreach( $arrAttr as $i => $v ) {
							if ( !in_array( $i, $arrOptions[ FHOV_ONLYATTR ] ) ) {
								unset( $arrAttr[ $i ] );
							}
						}
						foreach( $arrValues as $i => $v ) {
							if ( !in_array( $i, $arrOptions[ FHOV_ONLYATTR ] ) ) {
								unset( $arrValues[ $i ] );
							}
						}
					}
					$szSet = array( );
					foreach( $arrAttr as $j => $w ) {
						$szSet[ ] = $w."=".$arrValues[ $j ];
					}
					$szQuery = "UPDATE `".$szTable."` SET ".join( ",", $szSet )." WHERE ".$szUpdBy;
					//ShowVar( $szQuery );
					$objRet = $this->database->Query( $szQuery );
				}
			}
			
			return $objRet;
		} // function UpdObject
		
		/**
		 * 	Подсчитывает количество объектов
		 * 	@return CResult
		 */
		public function CountObject( $arrOptions ) {
			$objRet = new CResult( );
			$szTable = strval( isset( $arrOptions[ FHOV_TABLE ] ) ? $arrOptions[ FHOV_TABLE ] : "" );
			if ( !empty( $szTable ) && $this->database !== NULL ) {
				$szQuery = "SELECT COUNT(*) c FROM ".$szTable;
				$arrTail = array( );
				if ( isset( $arrOptions[ FHOV_WHERE ] ) ) {
					$arrTail[ ] = "WHERE ".$arrOptions[ FHOV_WHERE ];
				}
				if ( !empty( $arrTail ) ) {
					$szQuery .= " ".join( " ", $arrTail );
				}
				$tmp = $this->database->Query( $szQuery );
				if ( $tmp->HasError( ) ) {
					$objRet->AddError( $tmp );
				}
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( );
					$tmp = current( $tmp );
					$tmp = $tmp[ "c" ];
					$objRet->AddResult( intval( $tmp ), "count" );
				}
			}
			
			return $objRet;
		} // function CountObject
		
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
			if ( $szName == "database" ) {
				if ( isset( $arrInput[ $szIndex ] ) ) {
					$this->database = $arrInput[ $szIndex ];
				}
			} else {
				$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			}
			return $objRet;
		} // function InitAttr
		
	} // class CFlexHandler
	
	
?>