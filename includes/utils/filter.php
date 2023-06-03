<?php
	/**
	 *	Фильтр пробелов
	 *	удаляет длинные последовательности пробелов и заменяет их на один
	 */
	class CSpaceFilter extends CFilter {
		public function Apply( $mxdValue ) {
			$mxdValue = preg_replace( '/\s\s+/', ' ', $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр многострочных комментариев в стиле C++
	 */
	class CMultiCommentFilter extends CFilter {
		public function Apply( $mxdValue ) {
			$mxdValue = preg_replace( '/\/\*[^*]*\*\//', ' ', $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр, делающий CSS файл более компактным
	 *	убирает не нужные пробелы и символы, которые можно убирать, следуя синтаксису
	 */
	class CCSSCompactFilter extends CFilter {
		public function Apply( $mxdValue ) {
			$mxdValue = preg_replace( array( '/(,|:|;|\{|\})\s/', '/\s(,|:|;|\{|\})/' ), array( '$1', '$1' ), $mxdValue );
			$mxdValue = str_replace( ";}", "}", $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр применяет md5 функцию к тексту
	 *	логика применения описывается параметрами класса
	 *	параметры iNumOfApply - количество применений функции md5 к тексту
	 */
	class CMd5Filter extends CFilter {
		private $m_iNumOfApply = 1;
		
		public function __construct( $iNumOfApply ) {
			$this->m_iNumOfApply = @intval( $iNumOfApply );
		}
		
		public function Apply( $mxdValue ) {
			$mxdValue = @strval( $mxdValue );
			for( $i = 0; $i < $this->m_iNumOfApply; ++$i ) {
				$mxdValue = md5( $mxdValue );
			}
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр делает из переменной целое число
	 */
	class CIntFilter extends CFilter {
		private $m_iDigits = 10;
		
		public function Apply( $mxdValue ) {
			$mxdValue = @strval( $mxdValue );
			$mxdValue = floatval( $mxdValue );
			$mxdValue = round( $mxdValue );
			$fPhpMax = floatval( PHP_INT_MAX );
			$mxdValue = $mxdValue % $fPhpMax;
			$mxdValue = @intval( $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр делает из переменной вещественное число
	 */
	class CFloatFilter extends CFilter {
		private $m_iDigits = 10;
		private $m_iDecimal = 2;
		
		public function __construct( $iDigits = 10, $iDecimal = 2 ) {
			$this->m_iDigits = @intval( $iDigits );
			$this->m_iDecimal = @intval( $iDecimal );
		}
		
		public function Apply( $mxdValue ) {
			$mxdValue = @floatval( $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр делает из переменной строку заданной длины
	 *	если не задавать параметр, то длина может быть любой
	 */
	class CStringFilter extends CFilter {
		private $m_iLength = false;
		
		public function __construct( $iLength = false ) {
			$this->m_iLength = $iLength;
		}
		
		public function Apply( $mxdValue ) {
			$mxdValue = @strval( $mxdValue );
			if ( $this->m_iLength !== false && $mxdValue !== "" ) {
				$mxdValue = substr( $mxdValue, 0, $this->m_iLength );
			}
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр подготавливает строку для вставки в базу данных
	 */
	class CStringDBFilter extends CFilter {
		private $m_iLength = false;
		private $m_szEncoding = "cp1251";
		
		public function __construct( $iLength = false, $szEncoding = "cp1251" ) {
			$this->m_iLength =$iLength;
			$this->m_szEncoding = @strval( $szEncoding );
		}
		
		public function Apply( $mxdValue ) {
			$mxdValue = @strval( $mxdValue );
			if ( $this->m_iLength !== false && $mxdValue !== "" ) {
				$mxdValue = substr( $mxdValue, 0, $this->m_iLength );
			}
			$mxdValue = @mysql_real_escape_string( $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр восстанавливает строку из БД
	 */
	class CStringDBBackFilter extends CFilter {
		private $m_iLength = false;
		private $m_szEncoding = "cp1251";
		
		public function __construct( $iLength = false, $szEncoding = "cp1251" ) {
			$this->m_iLength = $iLength;
			$this->m_szEncoding = @strval( $szEncoding );
		}
		
		public function Apply( $mxdValue ) {
			$mxdValue = @strval( $mxdValue );
			if ( $this->m_iLength !== false && $mxdValue !== "" ) {
				$mxdValue = substr( $mxdValue, 0, $this->m_iLength );
			}
			//$mxdValue = stripslashes( $mxdValue );
			return $mxdValue;
		}
	}
	
	/**
	 *	Фильтр экранирует строку для вставки в элементы формы
	 */
	class CStringFormFilter extends CFilter {
		private $m_iLength = false;
		private $m_szEncoding = "cp1251";
		
		public function __construct( $iLength = false, $szEncoding = "cp1251" ) {
			$this->m_iLength = $iLength;
			$this->m_szEncoding = @strval( $szEncoding );
		}
		
		public function Apply( $mxdValue ) {
			$mxdValue = @strval( $mxdValue );
			if ( $this->m_iLength !== false && $mxdValue !== "" ) {
				$mxdValue = substr( $mxdValue, 0, $this->m_iLength );
			}
			$mxdValue = htmlentities( $mxdValue, NULL, $this->m_szEncoding );
			return $mxdValue;
		}
		
	} // class CStringFormFilter
	
	/**
	 * 	Фильтрует массив
	 */
	class CArrayFilter extends CFilter {
		private $m_arrBanIndex = array( ); // индексы, которые нужно удалить
		
		public function __construct( $arrBanIndex = array( ) ) {
			$this->SetArray( $arrBanIndex );
		}
		
		public function SetArray( $arrBanIndex ) {
			if ( is_array( $arrBanIndex ) ) {
				$this->m_arrBanIndex = $arrBanIndex;
			}
		}
		
		public function Apply( $mxdValue ) {
			if ( is_array( $mxdValue ) ) {
				foreach( $this->m_arrBanIndex as $v ) {
					if ( isset( $mxdValue[ $v ] ) ) {
						unset( $mxdValue[ $v ] );
					}
				}
			}
			return $mxdValue;
		}
		
	} // class CArrayFilter
	
	/**
	 * 	Фильтрует дату
	 */
	class CDateFilter extends CFilter {
		private $m_szFormat = "Y-m-d";
		
		public function __construct( $szFormat = "Y-m-d" ) {
			if ( is_string( $szFormat ) ) {
				$this->m_szFormat = $szFormat;
			}
		}
		
		public function Apply( $mxdValue ) {
			$tmp = @strtotime( $mxdValue );
			if ( $tmp === false ) {
			} else {
				$mxdValue = date( $this->m_szFormat, $tmp );
			}
			return $mxdValue;
		}
		
	} // class CDateFilter
	
?>