<?php
	/**
	 *	Класс для ведения результатов
	 *	@author UndeadCS
	 *	@package UndeadCS SDK
 	 *	@subpackage Result
	 */

	/**
	 *	Класс результатов
	 */
	class CResult {
		private $m_arrResults = array( );
		private $m_arrErrors = array( );
		
		public function __get( $szName ) {
			if ( $szName == "result" ) {
				return $this->m_arrResults;
			} elseif ( $szName == "error" ) {
				return $this->m_arrErrors;
			} elseif ( $szName == "has_result" ) {
				return $this->HasResult();
			} elseif ( $szName == "has_error" ) {
				return $this->HasError( );
			} else {
				return parent::__get( $szName );
			}
		} // function __get
		
		public function AddResult( $mxdInput, $mxdIndex = NULL ) {
			if ( is_object( $mxdInput ) && ( get_class( $mxdInput ) == "CResult" ) ) {
				$tmp = $mxdInput->GetResult( );
				foreach( $tmp as $i => $v ) {
					$this->AddResult( $v );
				}
			} elseif ( $mxdIndex !== NULL && ( is_int( $mxdIndex ) || is_string( $mxdIndex ) ) ) {
				$this->m_arrResults[ $mxdIndex ] = $mxdInput;
			} else {
				$this->m_arrResults[ ] = $mxdInput;
			}
		} // function AddResult
		
		public function AddError( $mxdInput, $mxdIndex = NULL ) {
			if ( is_object( $mxdInput ) && ( get_class( $mxdInput ) == "CResult" ) ) {
				$tmp = $mxdInput->GetError( );
				foreach( $tmp as $i => $v ) {
					$this->AddError( $v );
				}
			} elseif ( $mxdIndex !== NULL && ( is_int( $mxdIndex ) || is_string( $mxdIndex ) ) ) {
				$this->m_arrErrors[ $mxdIndex ] = $mxdInput;
			} else {
				$this->m_arrErrors[ ] = $mxdInput;
			}
		} // function AddError
		
		public function GetResult( $mxdIndex = NULL ) {
			if ( $mxdIndex !== NULL ) {
				return ( isset( $this->m_arrResults[ $mxdIndex ] ) ? $this->m_arrResults[ $mxdIndex ] : NULL );
			} else {
				return $this->m_arrResults;
			}
		} // function GetResult
		
		public function GetError( $mxdIndex = NULL ) {
			if ( $mxdIndex !== NULL ) {
				return ( isset( $this->m_arrErrors[ $mxdIndex ] ) ? $this->m_arrErrors[ $mxdIndex ] : NULL );
			} else {
				return $this->m_arrErrors;
			}
		} // function GetError
		
		public function HasResult( ) {
			return !empty( $this->m_arrResults );
		} // function HasResult
		
		public function HasError( ) {
			return !empty( $this->m_arrErrors );
		} // function HasError
		
	} // class CResult
	
	
?>