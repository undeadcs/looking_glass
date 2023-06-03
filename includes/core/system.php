<?php
	/**
	 *	Система
	 *	@author UndeadCS
	 *	@package UndeadCS
	 *	@subpackage System
	 */

	// сделаем статичную привязку к сегментам графа WGI - World Graph Index, по сути это индексы корневых вершин
	define( "WGI_SYSTEM",			1	); // Система
	define( "WGI_AGENT",			2	); // Агенты
	define( "WGI_STAT",			3	); // Статистика
	define( "WGI_EXIT",			4	); // Выход
	
	// SUR - System User Rank ранги пользователей системы
	define( "SUR_GUEST",		0	); // гость
	define( "SUR_SUPERADMIN",	1	); // суперамдин
	define( "SUR_ADMIN",		2	); // администратор
	define( "SUR_OPERATOR",		3	); // оператор
	define( "SUR_CLIENT",		4	); // клиент
	
	/**
	 * 	Аккаунт из под которого работает система
	 */
	class CSystemAccount extends CFlex {
		protected $id = 0;
		protected $v_id = 0;
		protected $rank = SUR_GUEST;
		protected $login = "";
		
		public function __get( $szName ) {
			$arrReadOnly = array(
				"id" => true,
				"v_id" => true,
				"rank" => true,
				"login" => true
			);
			if ( isset( $arrReadOnly[ $szName ] ) ) {
				if ( $arrReadOnly[ $szName ] ) {
					return $this->$szName;
				} else {
					return NULL;
				}
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
	} // class CSystemAccount

	/**
	 *	Класс, отвечающий за работу CMS
	 */
	class CSystem extends CFlex {
		protected $arrPath = array( ); // пути
		protected $arrHandler = array( ); // обработчики запросов
		protected $arrConfig = array( ); // конфиг
		protected $arrHProc = array( ); // описание функций обработчиков
		protected $szTestQuery = ""; // имя строки тестирования
		protected $objDatabase = NULL; // база данных
		protected $hGraph = NULL; // обработчик графа
		protected $iCurWgi = WGI_SYSTEM; // индекст текущего узла мира
		protected $iCurWgiState = MF_NONE; // состояние текущего узла
		// учетка, из под которой стартует система
		private $objAccount = NULL; // аккаунт
		
		// старые параметры
		private $m_arrPaths = array( );
		private $m_arrConfig = array( );
		private $m_arrHandlerProc = array( "proc" => "Process", "test" => "Test" );
		private $m_szTestQuery = "REQUEST_URI";//"REDIRECT_URL";
		
		/**
		 *	Проверяет верность структуры конфига обработчиков
		 *	@param $mxdInput mixed набор данных
		 *	@return bool
		 */
		private function CheckHandlerInput( &$mxdInput ) {
			return is_array( $mxdInput ) && isset( $mxdInput[ "label" ], $mxdInput[ "object" ] );
		} // function CheckHandlerInput
		
		/**
		 *	Проверяет верность структуры конфига путей
		 *	@param $mxdInput array набор данных
		 *	@return bool
		 */
		private function CheckPathInput( &$mxdInput ) {
			return false;
		} // function CheckParhInput
		
		/**
		 *	Сохраняет имя функции обработчика и проверяет их (обработчики)
		 *	@param $szFor string для какой функции устанавливается имя
		 *	@param $szName string новое имя функции
		 *	@return bool true - удалось сохранить, false - не удалось
		 */
		private function SetHandlerProc( $szFor, $szName ) {
			$szFor = @strval( $szFor );
			$szName = @strval( $szName );
			if ( isset( $this->m_arrHandlerProc[ $szFor ] ) && !empty( $szName ) ) {
				$this->m_arrHandlerProc[ $szFor ] = $szName;
				if ( !empty( $this->m_arrHandlers ) ) {
					foreach( $this->m_arrHandlers as $i => $v ) {
						if ( !method_exists( $v, $szName ) ) {
							unset( $this->m_arrHandlers[ $i ] );
						}
					}
				}
				return true;
			}
			return false;
		}
		
		/**
		 *	Делает урл таким, как будто система находится в корне
		 *	@param $szUrl string урл для изменения
		 */
		private function GetRootUrl( $szUrl ) {
			$tmp = str_replace( "/index.php", '', $_SERVER[ "SCRIPT_NAME" ] );
			$tmp = str_replace( $tmp, '', $szUrl );
			if ( empty( $tmp ) ) {
				$tmp = "/";
			}
			return $tmp;
		} // function ProcRootUrl
		
		public function __get( $szName ) {
			if ( $szName == "database" ) {
				return $this->objDatabase;
			} elseif ( $szName == "wgi" ) {
				return $this->iCurWgi;
			} elseif ( $szName == "wgi_state" ) {
				return $this->iCurWgiState;
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
		public function BaseStart( ) {
			if ( isset( $this->arrConfig[ "graph" ] ) && $this->objDatabase ) {
				// разделы ( пока статичные )
				$arrBase = array( WGI_SYSTEM, WGI_AGENT, WGI_STAT, WGI_EXIT );
				// вершины
				$hTmp = new CHVertex( );
				$tmp = $this->arrConfig[ "graph" ][ "vertex" ];
				$tmp[ "database" ] = $this->objDatabase;
				$hTmp->Create( $tmp );
				$hTmp->CheckTable( array( FHOV_TABLE => "ud_vertex", FHOV_OBJECT => "CVertex" ) );
				$tmp = $hTmp->GetObject( array( FHOV_TABLE => "ud_vertex", FHOV_OBJECT => "CVertex" ) );
				if ( !$tmp->HasResult( ) ) {
					foreach( $arrBase as $v ) {
						$tmp1 = new CVertex( );
						$tmp1->Create( array( "vertex_id" => $v, "vertex_label" => WGI_SYSTEM ) );
						$hTmp->AddObject( array( $tmp1 ), array( FHOV_TABLE => "ud_vertex" ) );
					}
				}
				// ребра
				$hTmp = new CHEdge( );
				$tmp = $this->arrConfig[ "graph" ][ "edge" ];
				$tmp[ "database" ] = $this->objDatabase;
				$hTmp->Create( $tmp );
				$hTmp->CheckTable( array( FHOV_TABLE => "ud_edge", FHOV_OBJECT => "CEdge" ) );
			}
		} // function BaseStart
		
		/**
		 *	Инициализация работы CMS
		 */
		public function SystemStart( ) {
			$szApplicationRoot = $this->GetPath( "root_application" );
			if ( $szApplicationRoot !== false && file_exists( $szApplicationRoot."/config.php" ) ) {
				include_once( $szApplicationRoot."/config.php" );
			}
			
			//$this->BaseStart( );
			//
			if ( $this->objAccount === NULL ) {
				$objSysAcc = new CSystemAccount( );
				$objSysAcc->Create( array( "id" => 0, "rank" => SUR_GUEST, "login" => "" ) );
				$this->SetUser( $objSysAcc );
			}
		}
		
		/**
		 *	Работа CMS
		 */
		public function SystemProcess( ) {
			$szQuery = $_SERVER[ $this->m_szTestQuery ];
			preg_match( '/^([^\?]*)\??/', $_SERVER[ $this->m_szTestQuery ], $szQuery );
			if ( empty( $szQuery ) ) {
				$szQuery = "";
			} else {
				$szQuery = $szQuery[ 1 ];
			}
			//ShowVarD( $_SERVER[ $this->m_szTestQuery ], $szQuery );
			//$szQuery = $this->GetRootUrl( $_SERVER[ $this->m_szTestQuery ] );
			$szQuery = $this->GetRootUrl( $szQuery );
			$szTestFunc = $this->m_arrHandlerProc[ "test" ];
			$szProcFunc = $this->m_arrHandlerProc[ "proc" ];
			$bProcessed = false;
			foreach( $this->arrHandler as $i => $v ) {
				$szObject = $v[ "object" ];
				$objCurrentHandler = NULL;
				if ( class_exists( $szObject ) ) {
					$objCurrentHandler = new $szObject( );
				}
				if ( $objCurrentHandler !== NULL ) {
					$bResult = $objCurrentHandler->$szTestFunc( $szQuery );
					if ( $bResult === true ) {
						$bProcessed = $objCurrentHandler->$szProcFunc( $szQuery );
						if ( $bProcessed === true ) {
							break;
						}
					}
				}
			}
			if ( $bProcessed === false ) {
				$szFolder = $this->GetPath( "root_application" );
				if ( $szFolder !== false && file_exists( $szFolder."/index.php" ) ) {
					include_once( $szFolder."/index.php" );
				} else {
					$this->vDefault( );
				}
			}
		}
		
		/**
		 *	Завершение работы CMS
		 */
		public function SystemTerminate( ) {
		}
		
		/**
		 *	Получение меню
		 *	@param $domDoc DOMDocument внешний документ куда выгружается меню
		 *	@return string
		 */
		public function GetMenu( &$domDoc ) {
			$objMenu = new CMenu( );
			$szRoot = $this->GetPath( "root_relative" )."/admin";
			$arrMenu = array( // пока сделаем так, в дальнейшем будет автоматика с опредеоение current
				WGI_AGENT => array( "title" => "Агенты", "url" => $szRoot."/" ),
				WGI_STAT => array( "title" => "Статистика", "url" => $szRoot."/stat/" ),
				WGI_EXIT => array( "title" => "выход", "url" => $szRoot."/exit/" ),
			);
			$iCurrentSysRank = $this->objAccount->rank;
			if ( isset( $arrMenu[ $this->iCurWgi ] ) ) {
				$arrMenu[ $this->iCurWgi ][ "flags" ] = $this->iCurWgiState;
			}
			$objMenu->Create( array( "items" => $arrMenu ) );
			return $objMenu->GetXML( $domDoc );
		} // function GetMenu
		
		/**
		 *	Установка текущего узла мира
		 *	@return void
		 */
		public function SetWGI( $iWgi ) {
			$this->iCurWgi = $iWgi;
		} // function SetWGI
		
		/**
		 *	Устанавливает состояние для текущего WGI
		 */
		public function SetWGIState( $iWgiState ) {
			$this->iCurWgiState = $iWgiState;
		} // function SetWGIState
		
		/**
		 *	Добавление объекта в мир
		 *	@return CResult
		 */
		public function AddToWorld( $iWgi = 0, $szLinkLabel = "" ) {
			$objRet = new CResult( );
			$hVertex = new CHVertex( );
			$tmp = $this->arrConfig[ "graph" ][ "vertex" ];
			$tmp[ "database" ] = $this->objDatabase;
			$hVertex->Create( $tmp );
			if ( !$iWgi ) {
				$iWgi = $this->iCurWgi;
			}
			$tmp = new CVertex( );
			$tmp1 = $tmp->Create( array( "vertex_label" => $iWgi ) );
			if ( $tmp1->HasError( ) ) {
				$objRet->AddError( $tmp1 );
			} else {
				$tmp = array( $tmp );
				$tmp = $hVertex->AddObject( $tmp, array( FHOV_TABLE => "ud_vertex" ) );
				if ( $tmp->has_result && !$tmp->has_error ) {
					$iVertex = $tmp->result[ "insert_id" ];
					$objRet->AddResult( $iVertex, "graph_vertex_id" );
					// добавляем связь
					$hEdge = new CHEdge( );
					$tmp = $this->arrConfig[ "graph" ][ "edge" ];
					$tmp[ "database" ] = $this->objDatabase;
					$hEdge->Create( $tmp );
					$tmp = new CEdge( );
					$szLabel = "section[".$iWgi."] object";
					if ( !empty( $szLinkLabel ) ) {
						$szLabel = $szLinkLabel;
					}
					$tmp->Create( array( "edge_u_id" => $iWgi, "edge_v_id" => $iVertex, "edge_label" => $szLabel ) );
					$hEdge->AddObject( array( $tmp ), array( FHOV_TABLE => "ud_edge" ) );
				}
			}
			return $objRet;
		} // function AddToWorld
		
		/**
		 *	Удаление объекта из мира
		 *	@param $iIds array набор id вершин графа
		 *	@return void
		 */
		public function DelFromWorld( $iIds ) {
			$hVertex = new CHVertex( );
			$tmp = $this->arrConfig[ "graph" ][ "vertex" ];
			$tmp[ "database" ] = $this->objDatabase;
			$hVertex->Create( $tmp );
			$hEdge = new CHVertex( );
			$tmp = $this->arrConfig[ "graph" ][ "edge" ];
			$tmp[ "database" ] = $this->objDatabase;
			$hEdge->Create( $tmp );
			
			foreach( $iIds as $v ) {
				// сносим вершину
				$objVertex = new CVertex( );
				$objVertex->Create( array( "vertex_id" => $v ) );
				$hVertex->DelObject( array( $objVertex ), array( FHOV_TABLE => "ud_vertex" ) );
				// сносим ребра
				$tmp = $hEdge->GetObject( array( FHOV_WHERE => "edge_u_id=".$v." OR edge_v_id=".$v, FHOV_TABLE => "ud_edge", FHOV_INDEXATTR => "id", FHOV_OBJECT => "CEdge" ) );
				if ( $tmp->HasResult( ) ) {
					$hEdge->DelObject( $tmp->GetResult( ), array( FHOV_TABLE => "ud_edge" ) );
				}
			}
		} // function DelFromWorld
		
		/**
		 * 	Связывание объектов
		 * 	@param $iUId int id объекта источника
		 * 	@param $iVId int id объекта цели
		 * 	@param $szLabel string метка связи
		 * 	@return CResult
		 */
		public function LinkObjects( $iUId, $iVId, $szLabel ="" ) {
			// TODO: добавить проверку существования вершин, перед их связыванием
			$objRet = new CResult( );
			$hEdge = new CHEdge( );
			$hEdge->Create( array( "database" => $this->objDatabase ) );
			$objEdge = new CEdge( );
			$tmp = array( "edge_u_id" => $iUId, "edge_v_id" => $iVId, "edge_label" => $szLabel );
			$tmp = $objEdge->Create( $tmp );
			if ( $tmp->HasError( ) ) {
				$objRet->AddError( $tmp );
			} else {
				$tmp = $hEdge->AddObject( array( $objEdge ), array( FHOV_TABLE => "ud_edge" ) );
				if ( $tmp->HasError( ) ) {
					$objRet->AddError( $tmp );
				}
			}
			return $objRet;
		} // function LInkObjects
		
		/**
		 * 	Получение связанных объектов
		 * 	@param $iId int id объекта, для которого выгрести записи
		 * 	@param $bTo bool исходящие ребра
		 * 	@param $szLabel string фильтрация по метке
		 * 	@return CResult
		 */
		public function GetLinkObjects( $iId, $bTo = true, $szLabel = "" ) {
			$objRet = new CResult( );
			$hVertex = new CHVertex( );
			$hVertex->Create( array( "database" => $this->objDatabase ) );
			$hEdge = new CHEdge( );
			$hEdge->Create( array( "database" => $this->objDatabase ) );
			$objVertex = new CVertex( );
			$objEdge = new CEdge( );
			$tmp = $objVertex->Create( array( "vertex_id" => $iId ) );
			if ( !$tmp->HasError( ) ) {
				$szEdgeAttr = "v_id";
				$szGetAttr = "u_id";
				if ( $bTo ) {
					$szEdgeAttr = "u_id";
					$szGetAttr = "v_id";
				}
				$objEdge->Create( array( "edge_".$szEdgeAttr => $iId ) );
				$tmp = $objEdge->GetSQLSelect( );
				$arrAttr = $tmp->GetResult( "attr" );
				$arrValues = $tmp->GetResult( "values" );
				$szWhere = $arrAttr[ $szEdgeAttr ]."=".$arrValues[ $szEdgeAttr ];
				if ( !empty( $szLabel ) ) {
					$szWhere .= " AND `edge_label`='".@mysql_real_escape_string( $szLabel )."'";
				}
				$arrOptions = array( FHOV_WHERE => $szWhere, FHOV_TABLE => "ud_edge", FHOV_OBJECT => "CEdge" );
				$tmp = $hEdge->GetObject( $arrOptions );
				if ( $tmp->HasResult( ) ) {
					// выгребли связи
					$tmp = $tmp->GetResult( );
					foreach( $tmp as $i => $v ) {
						$tmp1 = $v->GetSQLSelect( );
						$arrValues = $tmp1->GetResult( "values" );
						$arrOptions = array( FHOV_WHERE => "`vertex_id`=".$arrValues[ $szGetAttr ], FHOV_TABLE => "ud_vertex", FHOV_OBJECT => "CVertex" );
						$tmp1 = $hVertex->GetObject( $arrOptions );
						if ( $tmp1->HasResult( ) ) {
							$tmp1 = $tmp1->GetResult( );
							$objRet->AddResult( current( $tmp1 ) );
						}
					}
				}
			}
			return $objRet;
		} // function GetLinkObjects
		
		/**
		 * 	Устанавливает пользователя, из под которого запущена система
		 */
		public function SetUser( $objSysAccount ) {
			// TODO: защиту от неверного значения
			$this->objAccount = $objSysAccount;
		} // function SetUser
		
		public function GetUserRank( ) {
			return $this->objAccount->rank;
		} // function GetUserRank
		
		public function GetUserLogin( ) {
			return $this->objAccount->login;
		} // function GetUserLogin
		
		public function GetUserVId( ) {
			return $this->objAccount->v_id;
		}
		
		/**
		 *	Добавление пути
		 *	@param $szName string логическое имя пути 
		 *	@param $szValue string значение пути
		 *	@param $bReplace bool переписывать путь, если он уже есть
		 *	@return bool true - при удачном завершении, false - не удалось запистаь путь
		 */
		public function ApplyPath( $szName, $szValue, $bReplace = true ) {
			$szName = @strval( $szName );
			$szValue = @strval( $szValue );
			$bReplace = ( @intval( $bReplace ) ? true : false );
			if ( $bReplace || !isset( $this->m_arrPaths[ $szName ] ) ) {
				$this->m_arrPaths[ $szName ] = $szValue;
				return true;
			}
			return false;
		}
		
		/**
		 *	Получение пути по его логическому имени
		 *	@return string строка пути, false - если такого пути не существует
		 */
		public function GetPath( $szName ) {
			$szName = @strval( $szName );
			return ( isset( $this->m_arrPaths[ $szName ] ) ? @strval( $this->m_arrPaths[ $szName ] ) : false );
		}
		
		/**
		 *	Установка имен функций обработчиков
		 *	если имя пустое, то оно не меняется
		 *	@param $szProcName string имя главной функции обработчика
		 *	@param $szTestName string имя проверочной функции обработчика
		 *	@return bool true - удалось установить хоть одно имя функции, false - не удалось
		 */
		public function ApplyHandlerProc( $szProcName, $szTestName ) {
			$szProcName = @strval( $szProcName );
			$szTestName = @strval( $szTestName );
			$bProc = $this->SetHandlerProc( "proc", $szProcName );
			$bTest = $this->SetHandlerProc( "test", $szTestName );
			$bOk = $bProc || $bTest;
			return $bOk;
		}
		
		public function vDefault( ) {
			header( "Content-Type: text/html; charset=UTF-8", true );
	
			ob_start( );

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>Empty project</title>
</head>
<body>Empty project</body>
</html><?
			$szText = ob_get_clean( );
			echo $szText;
		} // function vDefault
		
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
			if ( $szName == "arrConfig" || $szName == "arrHandler") {
				if ( isset( $arrInput[ $szIndex ] ) ) {
					$arrValue = $arrInput[ $szIndex ];
					if ( $szName == "arrConfig" ) {
						// пока так, чтоб работал граф
						$this->arrConfig = $arrInput[ $szIndex ];
					} elseif ( $szName == "arrHandler" ) {
						foreach( $arrValue as $v ) {
							if ( $this->CheckHandlerInput( $v ) ) {
								$this->arrHandler[ $v[ "label" ] ] = $v;
							}
						}
					}
				}
			} elseif ( $szName == "objDatabase" ) {
				if ( isset( $arrInput[ $szIndex ] ) ) {
					$tmp = new CDatabase( );
					$tmp->Create( $arrInput[ $szIndex ] );
					$this->objDatabase = $tmp;
					$this->objDatabase->Connect( );
				}
			} else {
				$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			}
			return $objRet;
		} // function InitAttr
		
	} // class CSystem
	
	
	
?>