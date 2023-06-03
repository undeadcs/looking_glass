<?php
	/**
	 *	База данных
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Database
	 */

	/**
	 *	Аккаунт БД
	 */
	class CDbAccount extends CAccount {
		public $database = ""; // имя базы данных, в которой лежат данные системы
	} // class CDbAccount
	
	define( "ERROR_DB_NOTINIT",		1	); // не инициализировано соединение
	define( "ERROR_DB_FAILCONNECT",		2	); // не получилось присоединиться

	/**
	 *	Класс работы с базой данных
	 */
	class CDatabase extends CFlex {
		protected $hConnection = NULL;
		protected $objAccount = NULL;
		
		public function __get( $szName ) {
			if ( $szName == "db_name" ) {
				if ( $this->objAccount ) {
					return $this->objAccount->database;
				} else {
					return "";
				}
			} else {
				return parent::__get( $szName );
			}
		}
		
		/**
		 *	Фильтрует значение для выбранного атрибута, используется для ввода данных в объект
		 *	@param $szName string имя атрибута
		 *	@param $arrInput mixed некое значение
		 *	@param $arrConfig array конфиг объекта
		 *	@param $iMode int режим фильтрации
		 *	@return CResult
		 */
		protected function InitAttr( $szName, &$arrInput, &$arrConfig, $iMode = FLEX_FILTER_PHP ) {
			$objRet = new CResult( );
			if ( $szName == "objAccount" ) {
				$this->objAccount = new CDbAccount( );
				$objRet = $this->objAccount->Create( $arrInput, $iMode );
				if ( $objRet->HasError( ) ) {
					$this->objAccount = NULL;
				}
			} else {
				$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			}
			return $objRet;
		} // function InitAttr
		
		/**
		 *	Подключение к БД
		 *	@return CResult
		 */
		public function Connect( ) {
			$objRet = new CResult( );
			
			if ( $this->objAccount ) {
				$this->hConnection = @mysql_connect(
					$this->objAccount->server,
					$this->objAccount->username,
					$this->objAccount->password
				);
				if ( is_resource( $this->hConnection ) ) {
					if ( !@mysql_select_db( $this->objAccount->database ) ) {
						$tmp = @mysql_query( "CREATE DATABASE ".$this->objAccount->database, $this->hConnection );
						if ( !$tmp ) {
							$objRet->AddError( new CError( mysql_errno( $this->hConnection ), mysql_error( $this->hConnection ) ) );
						} else {
							@mysql_select_db( $this->objAccount->database );
						}
					}
				} else {
					$objRet->AddError( new CError( ERROR_DB_FAILCONNECT, "Не удалось подключиться к серверу баз данных" ) );
				}
			}
			
			return $objRet;
		} // function Connect
		
		/**
		 *	Выполняет запрос к базе данных
		 *	@param $szQuery string строка запроса
		 *	@return CResult
		 */
		public function Query( $szQuery ) {
			$objRet = new CResult( );
			
			if ( is_resource( $this->hConnection ) ) {
				$tmp = @mysql_query( $szQuery, $this->hConnection );
				if ( $tmp ) {
					if ( is_resource( $tmp )&& mysql_num_rows( $tmp ) ) {
						while( $row = mysql_fetch_assoc( $tmp ) ) {
							$objRet->AddResult( $row );
						}
						mysql_free_result( $tmp );
					}
				} else {
					$objRet->AddError( new CError( mysql_errno( $this->hConnection ), mysql_error( $this->hConnection ) ) );
				}
			} else {
				$objRet->AddError( new CError( ERROR_DB_NOTINIT, "Отсутствует соединение" ) );
			}
			
			return $objRet;
		} // function Query
		
		/**
		 *	Возвращает id записи
		 *	@return int
		 */
		public function GetInsertId( ) {
			if ( is_resource( $this->hConnection ) ) {
				return @intval( mysql_insert_id( $this->hConnection ) );
			}
			return 0;
		} // function GetInsertId
		
		/**
		 *	Возвращает колчиество обработанных строк
		 *	@return int
		 */
		public function GetAffectedRows( ) {
			if ( is_resource( $this->hConnection ) ) {
				return @intval( mysql_affected_rows( $this->hConnection ) );
			}
			return 0;
		} // function GetAffectedRows
		
		/**
		 *	Возвращает ошибку при работе с БД
		 *	@return CError
		 */
		public function GetError( ) {
			if ( is_resource( $this->hConnection ) ) {
				return new CError( mysql_errno( $this->hConnection ), mysql_error( $this->hConnection ) );
			}
			return NULL;
		} // function GetError
		
	} // class CDatabase
	
?>