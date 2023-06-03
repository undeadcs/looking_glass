<?php
	/**
	 *	Агенты
	 *	@author UndeadCS
	 *	@package Undead Content System
	 *	@subpackage Agent
	 */

	require( 'agent.agent.php' );

	/**
	 * 	Агенты
	 */
	class CHAgent extends CHandler {
		private $hCommon = NULL;
		
		/**
		 * 	Инициализация обработчиков
		 */
		private function InitHandlers( ) {
			global $objCMS;
			$this->hCommon = new CFlexHandler( );
			$this->hCommon->Create( array( 'database' => $objCMS->database ) );
			$this->hCommon->CheckTable( array( FHOV_TABLE => 'ud_agent', FHOV_OBJECT => 'CAgent' ) );
		} // function InitHandlers
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			if ( preg_match( '/^\/admin\/stat\//', $szQuery ) || preg_match( '/^\/admin\/$\//', $szQuery ) ) {
				return false;
			}
			return ( preg_match( '/^\/admin\//', $szQuery ) ? true : false );
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			global $objCMS, $objCurrent, $mxdCurrentData, $szCurrentMode, $arrErrors;
			$this->InitHandlers( );
			$objCMS->SetWGI( WGI_AGENT );
			$objCMS->SetWGIState( MF_THIS );
			$modUser = new CHModUser( );
			$objCurrent = 'Agent';
			$szCurrentMode = 'List';
			$arrErrors = array( );
			$mxdCurrentData = array(
				'agent_list' => array( ),
				'current_agent' => new CAgent( )
			);
			$arrIndex = $mxdCurrentData[ 'current_agent' ]->GetAttributeIndexList( FLEX_FILTER_FORM );
			
			if ( preg_match( '/^\/admin\/\+\//', $szQuery ) ) {
				$szCurrentMode = 'Edit';
				$objCMS->SetWGIState( MF_THIS | MF_CURRENT );
				
				if ( count( $_POST ) ) {
					$arrData = $_POST;
					$fltArray = new CArrayFilter( );
					$arrFilter = array(
						'id' => $arrIndex[ 'id' ],
					);
					$fltArray->SetArray( $arrFilter );
					$arrData = $fltArray->Apply( $arrData );
					$tmp = $mxdCurrentData[ 'current_agent' ]->Create( $arrData );
					if ( $tmp->HasError( ) ) {
						$arrErrors = array_merge( $arrErrors, $tmp->GetError( ) );
					} else {
						$tmp = $this->hCommon->AddObject( array( $mxdCurrentData[ 'current_agent' ] ), array( FHOV_TABLE => 'ud_agent' ) );
						if ( $tmp->HasError( ) ) {
							$arrErrors = array_merge( $arrErrors, $tmp->GetError( ) );
						} else {
							$iId = $tmp->GetResult( 'insert_id' );
							$szText = $mxdCurrentData[ 'current_agent' ]->key;
							$szText = preg_replace( "/\r\n/sU", "\n", $szText, 10000, $i );
							ob_start( );
							echo $szText;
							$r = ob_get_clean( );
							file_put_contents( './.ssh/agent'.$iId, $r );
							chmod( './.ssh/agent'.$iId, 0600 );
							Redirect( $objCMS->GetPath( 'root_relative' ).'/admin/' );
						}
					}
				}
				//
			} elseif ( preg_match( '/^\/admin\/\d{1,20}\//', $szQuery ) ) {
				$tmp = NULL;
				preg_match( '/^\/admin\/(\d{1,20})\//', $szQuery, $tmp );
				$iId = intval( $tmp[ 1 ] );
				$tmp = $this->hCommon->GetObject( array(
					FHOV_WHERE => '`'.$arrIndex[ 'id' ]."`=$iId",
					FHOV_TABLE => 'ud_agent',
					FHOV_INDEXATTR => 'id',
					FHOV_OBJECT => 'CAgent'
				) );
				if ( $tmp->HasResult( ) ) {
					$tmp = $tmp->GetResult( $iId );
					$szCurrentMode = 'Edit';
					$objCMS->SetWGIState( MF_THIS | MF_CURRENT );
					$mxdCurrentData[ 'current_agent' ] = $tmp;
					
					if ( preg_match( '/^\/admin\/\d{1,20}\/del\//', $szQuery ) ) {
						$this->hCommon->DelObject( array( $mxdCurrentData[ 'current_agent' ] ), array( FHOV_TABLE => 'ud_agent', FHOV_INDEXATTR => 'id' ) );
						if ( file_exists( './.ssh/agent'.$mxdCurrentData[ 'current_agent' ]->id ) ) {
							unlink( './.ssh/agent'.$mxdCurrentData[ 'current_agent' ]->id );
						}
						Redirect( $objCMS->GetPath( "root_relative" )."/admin/" );
					} else {
						if ( count( $_POST ) ) {
							$arrData = $_POST;
							$fltArray = new CArrayFilter( );
							$fltArray->SetArray( array(
								'id' => $mxdCurrentData[ 'current_agent' ]->GetAttributeIndex( 'id', NULL, FLEX_FILTER_FORM )
							) );
							$arrData = $fltArray->Apply( $arrData );
							$tmp = $mxdCurrentData[ 'current_agent' ]->Create( $arrData );
							if ( $tmp->HasError( ) ) {
								$arrErrors = array_merge( $arrErrors, $tmp->GetError( ) );
							} else {
								$this->hCommon->UpdObject( array( $mxdCurrentData[ 'current_agent' ] ), array( FHOV_TABLE => 'ud_agent', FHOV_INDEXATTR => 'id' ) );
								if ( $mxdCurrentData[ 'current_agent' ]->key == '' ) {
									if ( file_exists( './.ssh/agent'.$mxdCurrentData[ 'current_agent' ]->id ) ) {
										unlink( './.ssh/agent'.$mxdCurrentData[ 'current_agent' ]->id );
									}
								} else {
									$szText = $mxdCurrentData[ 'current_agent' ]->key;
									$szText = preg_replace( "/\r\n/sU", "\n", $szText, 10000, $i );
									ob_start( );
									echo $szText;
									$r = ob_get_clean( );
									file_put_contents( './.ssh/agent'.$mxdCurrentData[ 'current_agent' ]->id, $r );
									chmod( './.ssh/agent'.$mxdCurrentData[ 'current_agent' ]->id, 0600 );
								}
								Redirect( $objCMS->GetPath( 'root_relative' ).'/admin/' );
							}
						}
					}
				}
				//
			} else {
				$tmp = $this->hCommon->GetObject( array( FHOV_TABLE => 'ud_agent', FHOV_OBJECT => 'CAgent' ) );
				if ( $tmp->HasResult( ) ) {
					$mxdCurrentData[ 'agent_list' ] = $tmp->GetResult( );
				}
			}
			
			$szFolder = $objCMS->GetPath( 'root_application' );
			if ( $szFolder !== false && file_exists( "$szFolder/index.php" ) ) {
				include_once( "$szFolder/index.php" );
			}
			
			return true;
		} // function Process
		
	} // class CHAgent
	
	
?>