<?php
	/**
	 *	Граф
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Graph
	 */

	/**
	 *	Вершина графа
	 */
	class CVertex extends CFlex {
		protected $id = 0; // id вершины
		protected $label = ""; // метка вершины
		
		public function __get( $szName ) {
			if ( $szName == "id" ) {
				return $this->id;
			} elseif ( $szName == "label" ) {
				return $this->label;
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			// общие настройки
			$arrConfig[ FLEX_CONFIG_TABLE ] = "ud_vertex";
			$arrConfig[ FLEX_CONFIG_PREFIX ] = "vertex_";
			$arrConfig[ FLEX_CONFIG_SELECT ] = "id";
			$arrConfig[ FLEX_CONFIG_UPDATE ] = "id";
			$arrConfig[ FLEX_CONFIG_DELETE ] = "id";
			// настройки режимов
			$arrConfig[ FLEX_CONFIG_XML	][ FLEX_CONFIG_NAME 	] = "Vertex";
			// настройки атрибутов
			$arrConfig[ "id"		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_INT | FLEX_TYPE_NOTNULL | FLEX_TYPE_AUTOINCREMENT | FLEX_TYPE_PRIMARYKEY;
			$arrConfig[ "id"		][ FLEX_CONFIG_DIGITS	] = 10;
			//
			return $arrConfig;
		} // function GetConfig
		
	} // class CVertex
	
	/**
	 *	Обработчик вершин
	 */
	class CHVertex extends CFlexHandler {
	} // class CHVertex
	
	/**
	 *	Ребро графа
	 */
	class CEdge extends CVertex {
		protected $u_id = 0; // id стартовой вершины
		protected $v_id = 0; // id конечной вершины
		
		public function __get( $szName ) {
			if ( $szName == "u_id" || $szName == "start_vertex" ) {
				return $this->u_id;
			} elseif ( $szName == "v_id" || $szName == "finish_vertex" ) {
				return $this->v_id;
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
		/**
		 *	Возвращает настройки класса
		 *	@return array
		 */
		public function GetConfig( ) {
			$arrConfig = parent::GetConfig( );
			// общие настройки
			$arrConfig[ FLEX_CONFIG_TABLE ] = "ud_edge";
			$arrConfig[ FLEX_CONFIG_PREFIX ] = "edge_";
			// настройки режимов
			$arrConfig[ FLEX_CONFIG_XML	][ FLEX_CONFIG_NAME 	] = "Edge";
			// настройки атрибутов
			$arrConfig[ "u_id"		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_INT | FLEX_TYPE_NOTNULL;
			$arrConfig[ "u_id"		][ FLEX_CONFIG_DIGITS	] = 10;
			$arrConfig[ "u_id"		][ FLEX_CONFIG_DEFAULT	] = 0;
			$arrConfig[ "v_id"		][ FLEX_CONFIG_TYPE	] = FLEX_TYPE_INT | FLEX_TYPE_NOTNULL;
			$arrConfig[ "v_id"		][ FLEX_CONFIG_DIGITS	] = 10;
			$arrConfig[ "v_id"		][ FLEX_CONFIG_DEFAULT	] = 0;
			//
			return $arrConfig;
		} // function GetConfig
		
	} // class CEdge
	
	/**
	 *	Обработчик ребер
	 */
	class CHEdge extends CFlexHandler {
	} // class CHEdge
	
	
	// необходимость использования функционала ниже под вопросом. зависит от парадигмы мира данной системы
	
	/**
	 *	Граф классический ( V, E ), ориентированный
	 */
	class CGraph extends CFlex {
		protected $vertex = array( ); // набор вершин
		protected $edge = array( ); // набор ребер
		
		public function __get( $szName ) {
			if ( $szName == "vertex" ) {
				return $this->vertex;
			} elseif ( $szName == "edge" ) {
				return $this->edge;
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
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
			$szIndex = $this->GetAttributeIndex( $szName, $arrConfig, $iMode );
			if ( $szName == "vertex" ) {
				if ( isset( $arrInput[ $szIndex ] ) && is_array( $arrInput[ $szIndex ] ) ) {
					foreach( $arrInput[ $szIndex ] as $i => $v ) {
						if ( is_array( $v ) ) {
							$tmp = new CVertex( );
							$tmp1 = $tmp->Create( $v, $iMode );
							if ( $tmp1->has_error ) {
								$objRet->AddError( $tmp1 );
							} else {
								$this->vertex[ $tmp->id ] = $tmp;
							}
						}
					}
				}
			} elseif ( $szName == "edge" ) {
				if ( isset( $arrInput[ $szIndex ] ) && is_array( $arrInput[ $szIndex ] ) ) {
					foreach( $arrInput[ $szIndex ] as $i => $v ) {
						if ( is_array( $v ) ) {
							$tmp = new CEdge( );
							$tmp1 = $tmp->Create( $v, $iMode );
							if ( $tmp1->has_error ) {
								$objRet->AddError( $tmp1 );
							} else {
								$this->edge[ ] = $tmp;
							}
						}
					}
				}
			} else {
				$objRet = parent::InitAttr( $szName, $arrInput, $arrConfig, $iMode );
			}
			return $objRet;
		} // function InitAttr
		
		/**
		 *	Фильтрует значение для выбранного атрибута, используется для вывода данных
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
			}
			// наполнение
			if ( $iMode & FLEX_FILTER_PHP ) {
			} elseif ( $iMode & FLEX_FILTER_DATABASE ) {
				if ( $iType & FLEX_TYPE_STRING || $iType & FLEX_TYPE_TEXT ) {
					$tmpFilter = new CStringDBFilter( $iLength );
					$tmp = $tmpFilter->Apply( $tmp );
					$tmp = "'".$tmp."'";
				}
			} elseif ( $iMode & FLEX_FILTER_FORM ) {
			} elseif ( $iMode & FLEX_FILTER_HTML ) {
			} elseif ( $iMode & FLEX_FILTER_XML ) {
				$tmp = html_entity_decode( $tmp );
			}
			return $tmp;
		} // function FilterAttr
		
	} // class CGraph
	
	// типы объектов для работы графа OTG - Object Type Graph
	define( "OTG_GRAPH",		0	); // граф
	define( "OTG_VERTEX",		1	); // только вершины
	define( "OTG_EDGE",		2	); // вершины
	
	/**
	 *	Обработчик графа
	 */
	class CHGraph extends CFlexHandler {
		
		/**
		 *	Получение объектов
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function GetObject( $arrOptions = array( ) ) {
			$mxdRet = new CResult( );
			$szTable = $this->table;
			$szObjectName = $this->object_name;
			$szIndexAttr = $this->index_attr;
			if ( !empty( $szTable ) && !empty( $szObjectName ) ) {
				$szQuery = "SELECT * FROM ".$szTable;
				$arrTail = array( );
				if ( isset( $arrOptions[ "#where" ] ) ) {
					$arrTail[ ] = "WHERE ".$arrOptions[ "#where" ];
				}
				if ( isset( $arrOptions[ "#group" ] ) ) {
					$arrTail[ ] = "GROUP BY ".$arrOptions[ "#group" ];
				}
				if ( isset( $arrOptions[ "#order" ] ) ) {
					$arrTail[ ] = "ORDER BY ".$arrOptions[ "#order" ];
				}
				if ( isset( $arrOptions[ "#limit" ] ) ) {
					$arrTail[ ] = "LIMIT ".$arrOptions[ "#limit" ];
				}
				if ( !empty( $arrTail ) ) {
					$szQuery .= " ".join( " ", $arrTail );
				}
				$tmp = $this->database->Query( $szQuery );
				if ( $tmp->has_error ) {
					$mxdRet->AddError( $tmp );
				}
				if ( $tmp->has_result ) {
					$tmp = $tmp->result;
					foreach( $tmp as $i => $v ) {
						$tmpObject = new $szObjectName( );
						$tmpObject->Create( $v, FLEX_FILTER_DATABASE );
						if ( $szIndexAttr == "" ) {
							$mxdRet->AddResult( $tmpObject );
						} else {
							$mxdRet->AddResult( $tmpObject, $tmpObject->$szIndexAttr );
						}
					}
				}
			}
			return $mxdRet;
		} // function GetObject
		
		/**
		 *	Добавление объектов
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function AddObject( $arrInput, $arrOptions = array( ) ) {
			$mxdRet = new CResult( );
			$szTable = $this->table;
			$szObjectName = $this->object_name;
			
			if ( !empty( $szTable ) ) {
				$arrInsert = array( );
				$szAttr = "";
				$szValues = "";
				foreach( $arrInput as $i => $v ) {
					$v->Create( $arrOptions );
					$tmp = $v->GetSQLInsert( );
					if ( $tmp->has_result ) {
						if ( $szAttr == "" ) {
							$szAttr = join( ",", $tmp->result[ "attr" ] );
						}
						$arrInsert[ ] = "(".join( ",", $tmp->result[ "values" ] ).")";
					}
				}
				$szValues = join( ",", $arrInsert );
				$szQuery = "INSERT INTO ".$szTable."(".$szAttr.") VALUES ".$szValues;
				$tmp = $this->database->Query( $szQuery );
				if ( $tmp->has_error ) {
					$mxdRet->AddError( $this->database->GetError( ) );
				} else {
					$mxdRet->AddResult( $this->database->GetInsertId( ), "insert_id" );
					$mxdRet->AddResult( $this->database->GetAffectedRows( ), "affected_rows" );
				}
			}
			
			return $mxdRet;
		} // function AddObject
		
		/**
		 *	Удаление объекта
		 *	@param $arrInput array массив экземпляров класса
		 *	@param $arrOptions array массив настроек
		 *	@return CResult
		 */
		public function DelObject( $arrInput, $arrOptions = array( ) ) {
			$objRet = new CResult( );
			$szTable = $this->table;
			$szObjectName = $this->object_name;
			$szIndexAttr = $this->index_attr;
			
			if ( !empty( $szTable ) && !empty( $szObjectName ) && !empty( $szIndexAttr ) ) {
				$szAttrDel = "";
				$arrToDel = array( );
				foreach( $arrInput as $i => $v ) {
					$v->Create( $arrOptions );
					$tmp = $v->GetSQLDelete( );
					if ( $tmp->has_result ) {
						if ( $szAttrDel == "" ) {
							$szAttrDel = $tmp->result[ "attr" ];
						}
						$arrToDel[ ] = $tmp->result[ "values" ];
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
			$szTable = $this->table;
			$szIndexAttr = $this->index_attr;
			
			if ( !empty( $szTable ) && !empty( $szIndexAttr ) ) {
				foreach( $arrInput as $i => $v ) {
					$tmp = $v->GetSQLUpdate( );
					$szDelBy = "";
					$arrAttr = $tmp->GetResult( "attr" );
					$arrValues = $tmp->GetResult( "values" );
					$szDelBy = $arrAttr[ $szIndexAttr ]."=".$arrValues[ $szIndexAttr ];
					unset( $arrAttr[ $szIndexAttr ], $arrValues[ $szIndexAttr ] );
					if ( isset( $arrOptions[ "#attr_ban" ] ) ) {
						foreach( $arrOptions[ "#attr_ban" ] as $j => $w ) {
							if ( isset( $arrAttr[ $w ] ) ) {
								unset( $arrAttr[ $w ] );
							}
							if ( isset( $arrValues[ $w ] ) ) {
								unset( $arrValues[ $w ] );
							}
						}
					}
					$szSet = array( );
					foreach( $arrAttr as $j => $w ) {
						$szSet[ ] = $w."=".$arrValues[ $j ];
					}
					$szQuery = "UPDATE `".$szTable."` SET ".join( ",", $szSet )." WHERE ".$szDelBy;
					$objRet = $this->database->Query( $szQuery );
				}
			}
			
			return $objRet;
		} // function UpdObject
		
	} // class CHGraph
	
	
?>