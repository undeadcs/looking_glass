<?php
	/**
	 *	Класс отвечает за вывод текста, с возможностью применения фильтров
	 */
	class COutput {
		private $m_arrFilters = array( );
		
		/**
		 * 	Добавление фильтра вывода
		 */
		public function AddFilter( ) {
			$iNum = func_num_args( );
			$mxdArgs = func_get_args( );
			for( $i = 0; $i < $iNum; ++$i ) {
				if ( is_object( $mxdArgs[ $i ] ) || is_string( $mxdArgs[ $i ] ) ) {
					$this->m_arrFilters[ ] = $mxdArgs[ $i ];
				}
			}
		}
		
		/**
		 * 	Вывод текста
		 */
		public function Process( $szText = false ) {
			$szText = @strval( $szText );
			
			if ( !empty( $this->m_arrFilters ) ) {
				foreach( $this->m_arrFilters as $i => $v ) {
					$tmpObject = NULL;
					if ( is_object( $v ) ) {
						$tmpObject = $v;
					} elseif ( is_string( $v ) ) {
						$tmpObject =  new $v( );
					}
					if ( $tmpObject !== NULL ) {
						$bMethodExists = method_exists( $tmpObject, "Apply" );
						if ( $bMethodExists ) {
							$szText = $tmpObject->Apply( $szText );
						}
					}
				}
			}
			
			return $szText;
		}
	}
?>