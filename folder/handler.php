<?php
	define( 'N_MAX', 0xffffff );
	define( 'N_MINUTE', 60 );
	define( 'N_HOUR', N_MINUTE * 60 );
	define( 'N_DAY', N_HOUR * 24 );
	define( 'N_MONTH', N_DAY * 30 );
	define( 'N_YEAR', N_DAY * 365 );
	
	function headerCache($expTime = false){
		if (!$expTime) $expTime = 14 * N_DAY;
		$expires = time() + $expTime; // по времени доступа
		$expires = gmdate('D, d M Y H:i:s', $expires) . ' GMT';
		$modifiedSince = getenv('HTTP_IF_MODIFIED_SINCE');
		if ($modifiedSince){
			header('HTTP/1.1 304 Not Modified', true, 304);
			// обязательно ставим новый expire, старый может устареть
			header("Cache-Control: store, cache, public, s-maxage={$expTime}, max-age={$expTime}", true);
			header('Expires: '.$expires, true);
			exit();
		}
		
		header('Pragma: cache', true);
		header('Expires: '.$expires, true);
		header("Cache-Control: store, cache, public, s-maxage={$expTime}, max-age={$expTime}", true);
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT', true);
	}
	
	/**
	 *	Обработчик запроса к js файлу
	 */
	class CHCustomJs extends CHandler {
		public function Test( $szQuery ) {
			return ( preg_match( '/([\w0-9\/]*)\.js$/', $szQuery ) ? true : false );
		}
		
		public function Process( $szQuery ) {
			global $objCMS;
			$tmp = NULL;
			preg_match( '/\/([0-9a-zA-Z_]*)\.js$/', $szQuery, $tmp );
			$szConfName = "main";
			if ( is_array( $tmp ) && count( $tmp ) > 1 ) {
				$szConfName = $tmp[ 1 ];
			}
			//
			header( "Content-Type: text/js; charset=UTF-8" );
			//
			$objInclude =  new CInclude( );
			$szFolder = $objCMS->GetPath( "system_scripts" );
			$arrItems = array(
				array( "label" => "core", "name" => "main" ),
			);
			
			if ( $szConfName == "calendar" || $szConfName == "jquery" ) {
				headerCache( N_MONTH );
				$arrItems = array( array( "label" => "core", "name" => $szConfName ) );
			}
			if ( $szConfName == "custom" ) {
				header( "Content-Type: text/js; charset=cp1251", true );
				header( "Cache-Control: no-cache, must-revalidate" );
				header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
				$szFolder = $objCMS->GetPath( "root_application" );
				$arrItems = array( array( "label" => "core", "name" => $szConfName ) );
			}
			//
			ob_start( );
			
			$objInclude->Create( array(
				"suffix" => ".js",
				"labels" => array(
					"core" => $szFolder."/",
				),
				"items" => $arrItems
			) );
			$objInclude->Process( );
			$szText = ob_get_clean( );
			echo $szText;
			echo "\r\n/* "._usr_time_work( )."*/";
			//
			return true;
		}
	}
	
	/**
	 *	Обработчик запроса к CSS файлу
	 */
	class CHCustomCss extends CHandler {
		
		/**
		 *	Проверка на срабатывание (перехват)
		 *	@param $szQuery string строка тестирования
		 *	@return bool
		 */
		public function Test( $szQuery ) {
			return ( preg_match( '/([\w0-9\/]*)\.css$/', $szQuery ) ? true : false );
		} // function Test
		
		/**
		 *	Обработка
		 *	@param $szQuery string строка, на которой произошел перехват
		 *	@return bool
		 */
		public function Process( $szQuery ) {
			global $objCMS;
			//
			header( "Content-Type: text/css; charset=UTF-8" );
			header( "Cache-Control: no-cache, must-revalidate" );
			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			//
			$objInclude =  new CInclude( );
			$szFolder = $objCMS->GetPath( "root_application" );
			$szRoot = $objCMS->GetPath( "root_relative" )."/";
			
			ob_start( );
			
			$objInclude->Create( array(
				"suffix" => ".css",
				"labels" => array( 
					"core" => $szFolder."/",
				),
				"items" => array(
					array( "label" => "core", "name" => "main" ),
				)
			) );
			$objInclude->Process( );
			$szText = ob_get_clean( );
			$tmpOutput = new COutput( );
			$tmpOutput->AddFilter( new CMultiCommentFilter( ), new CSpaceFilter( ), new CCSSCompactFilter( ) );
			$szText = preg_replace( '/\{:root_relative\}/', $szRoot, $szText );
			echo $tmpOutput->Process( $szText );
			echo "\r\n/* "._usr_time_work( )."*/";
			//
			return true;
		} // function Process
		
	} // class CHCss
	
	/**
	 *	Обработчик запросов к картинкам
	 */
	class CHCustomImage extends CHandler {
		
		public function Test( $szQuery ) {
			return ( preg_match( '/([^?]*)(\.jpg|\.png|\.gif|\.ico|\.tiff)$/', $szQuery ) ? true : false );
		}
		
		public function Process( $szQuery ) {
			global $objCMS;
			$tmp = NULL;
			preg_match_all( '/(.*)(\.jpg|\.png|\.gif|\.ico|\.tiff)$/', $szQuery, $tmp );
			$arrType = array(
				".jpg" => "image/jpeg",
				".gif" => "image/gif",
				".png" => "image/png",
				".tiff" => "image/tiff",
				".ico" => "image/vnd.microsoft.icon"
			);
			$szExt = @$tmp[ 2 ][ 0 ];
			//
			if ( isset( $arrType[ $szExt ] ) ) {
				$szMediaImages = $objCMS->GetPath( "media_images" );
				$szHeader = $arrType[ $szExt ];
				$szFile = explode( "/", $tmp[ 1 ][ 0 ] );
				foreach( $szFile as $v ) {
					if ( $v !== "" ) {
						if ( file_exists( $szMediaImages."/".$v ) ) {
							$szMediaImages = $szMediaImages."/".$v;
						}
					}
				}
				
				$szFile = end( $szFile );
				if ( $szMediaImages !== false ) {
					$szPath = $szMediaImages."/".$szFile.$szExt;
					if ( file_exists( $szPath ) ) {
						$iSize = filesize( $szPath );
						header( 'Content-Type: '.$szHeader, true );
						header( 'Content-Length: '.$iSize );
						headerCache( N_DAY );
						ob_clean( );
						flush( );
						readfile( $szPath );
						exit;
					}
				}
			}
			return true;
		}
	} // class CHCustomImage
	
?>