<?php
	/**
	 *	Тэг meta
	 */
	class CHtmlMeta extends CFlex {
		protected $name = "";
		protected $http_equiv = "";
		protected $content = "";
		
		/**
		 *	Получение HTML экземпляра
		 *	@return CResult
		 */
		public function GetHTML( ) {
			$mxdRet = new CResult( );
			
			$r = "";
			$tmp = array( );
			foreach( $this as $i => $v ) {
				if ( $v !== "" ) {
					$szAttr = $i;
					if ( $szAttr === "http_equiv" ) {
						$szAttr = "http-equiv";
					}
					$tmp[ ] = $szAttr.'="'.$v.'"';
				}
			}
			if ( !empty( $tmp ) ) {
				$r = "<meta ".join( " ", $tmp )."/>";
				$mxdRet->AddResult( $r );
			}
			
			return $mxdRet;
		} // function GetHTML
		
	} // class CHtmlMeta
	
	/**
	 *	Тэг link
	 */
	class CHtmlLink extends CFlex {
		protected $href = "";
		
		/**
		 *	Получение HTML экземпляра
		 *	@return CResult
		 */
		public function GetHTML( ) {
			$mxdRet = new CResult( );
			
			if ( !empty( $this->href ) ) {
				$r = '<link rel="stylesheet" type="text/css" href="'.$this->href.'"/>';
				$mxdRet->AddResult( $r );
			}
			
			return $mxdRet;
		} // function GetHTML
		
	} // class CHtmlLink
	
	/**
	 *	Тэг script
	 */
	class CHtmlScript extends CFlex {
		protected $src = "";
		
		/**
		 *	Получение HTML экземпляра
		 *	@return CResult
		 */
		public function GetHTML( ) {
			$mxdRet = new CResult( );
			
			if ( !empty( $this->src ) ) {
				$r = '<script type="text/javascript" src="'.$this->src.'"></script>';
				$mxdRet->AddResult( $r );
			}
			
			return $mxdRet;
		} // function GetHTML
		
	} // class CHtmlLink
	
	
?>