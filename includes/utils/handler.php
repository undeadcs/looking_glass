<?php
	/**
	 *	Обработчик запроса к js файлу
	 */
	class CJavaScriptHandler extends CHandler {
		public function Test( $szQuery ) {
			return ( preg_match( '/([\w0-9\/]*)\.js$/', $szQuery ) ? true : false );
		}
		
		public function Process( $szQuery ) {
			global $objCMS;
			//
			header( "Content-Type: text/css; charset=UTF-8" );
			header( "Cache-Control: no-cache, must-revalidate" );
			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			//
			$objInclude =  new CInclude( );
			$szFolder = $objCMS->GetPath( "system_scripts" );
			//
			ob_start( );
			
			$objInclude->Create( array(
				"suffix" => ".js",
				"labels" => array(
					"core" => $szFolder."/",
				),
				"items" => array(
					array( "label" => "core", "name" => "jquery-1.2.6.min" ),
					array( "label" => "core", "name" => "main" ),
				)
			) );
			$objInclude->Process( );
			$szText = ob_get_clean( );
			echo $szText;
			//
			return true;
		}
	}
	
	/**
	 *	Обработчки запроса к css файлу
	 */
	class CCSSHandler extends CHandler {
		public function Test( $szQuery ) {
			return ( preg_match( '/([\w0-9\/]*)\.css$/', $szQuery ) ? true : false );
		}
		
		public function Process( $szQuery ) {
			global $objCMS;
			//
			header( "Content-Type: text/css; charset=UTF-8" );
			header( "Cache-Control: no-cache, must-revalidate" );
			header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
			//
			$objInclude =  new CInclude( );
			$szFolder = $objCMS->GetPath( "system_styles" );
			
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
			echo $tmpOutput->Process( $szText );
			//
			return true;
		}
	}
	
	/**
	 *	Обработчик AJAX запроса
	 */
	class CAJAXHandler extends CHandler {
		public function Test( $szQuery ) {
			return ( preg_match( '/([\w0-9\/]*)\.ajax$/', $szQuery ) ? true : false );
		}
		
		public function Process( $szQuery ) {
			//
			header( "Content-Type: text/plain; charset=cp1251" );
			echo "Ajax Handler";
			//
			return true;
		}
	}
	
	/**
	 *	Обработчик запросов к картинкам
	 */
	class CImageHandler extends CHandler {
		
		public function Test( $szQuery ) {
			return ( preg_match( '/([^?]*)(\.jpg|\.png|\.gif|\.ico|\.tiff)$/', $szQuery ) ? true : false );
		} // function Test
		
		public function Process( $szQuery ) {
			/*
				Люди учитывайте пожалуйста имена картинок, т.к. некоторые имена блокируются анти-баннерными фильтрами
				например имя картинки 468x60 блокировало его загрузку
			//*/
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
				$szHeader = $arrType[ $szExt ];
				$szFile = explode( "/", $tmp[ 1 ][ 0 ] );
				$szFile = end( $szFile );
				$szMediaImages = $objCMS->GetPath( "media_images" );
				if ( $szMediaImages !== false ) {
					$szPath = $szMediaImages."/".$szFile.$szExt;
					if ( file_exists( $szPath ) ) {
						//*
						header( "Content-Type: ".$szHeader, true );
						$hFile = @fopen( $szPath, "r" );
						if ( $hFile !== false ) {
							$iSize = filesize( $szPath );
							$szText = fread( $hFile, $iSize );
							fclose( $hFile );
							echo $szText;
						}
						//*/
					}
				}
			}
			return true;
		} // function Process
		
	} // class CImageHandler
	
	
?>