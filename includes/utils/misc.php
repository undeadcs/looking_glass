<?php
	/**
	 * 	Редирект на заданный урл
	 * 	@param $szUrl string адрес, на который сделать редирект
	 * 	@param $iCode int код возврата ( по умолчанию 301 )
	 */
	function Redirect( $szUrl, $iCode = 301 ) {
		if ( empty( $szUrl ) ) {
			$szUrl = '/';
		}
		header( "Location: $szUrl", true, $iCode );
		exit;
	} // function Redirect
	
	function AccForSoa( $szAcc ) {
		return str_replace( '.', "\\\.", $szAcc[ 1 ] ).'.';
	} // function AccForSoa
	
	/**
	 * 	Преобразование e-mail для SOA записи
	 */
	function ConvertEmailForSoa( $szEmail ) {
		$szEmail = preg_replace_callback( '/([^@]*)@/U', 'AccForSoa', $szEmail );
		return $szEmail;
	} // function ConvertEmailForSoa
	
	/**
	 * 	Редирект на основе js
	 */
	function RedirectJs( $szUrl, $iDelaySeconds = 1 ) {
		ob_start( );
		?>
<div>seconds to redirect:&nbsp;<span id="xcount"><?=$iDelaySeconds?></span></div>
<noscript><a href="<?=$szUrl?>">next step</a></noscript>
<script>
<!--
var szUrl = '<?=$szUrl?>';
var iStep = <?=$iDelaySeconds?>;

function xredir( ) {
	if ( iStep ) {
		iStep -= 1;
		document.getElementById( "xcount" ).innerHTML = iStep;
	} else {
		window.location = szUrl;
		return;
	}
	setTimeout( "xredir( )", 1000 );
}

xredir( );
//-->
</script>
		<?
		return ob_get_clean( );
	}
	
	/**
	 * 	Копирует директирию
	 */
	function DirCopy( $szSrcFolder, $szDstFolder ) {
		if ( !file_exists( $szSrcFolder ) || !is_dir( $szSrcFolder ) ) {
			return;
		}
		$arrFolder = @scandir( $szSrcFolder );
		if ( !file_exists( $szDstFolder ) ) {
			mkdir( $szDstFolder );
		}
		foreach( $arrFolder as $i => $v ) {
			if ( $v == "." || $v == ".." ) {
				unset( $arrFolder[ $i ] );
			} elseif ( is_dir( $szSrcFolder."/".$v ) ) {
				DirCopy( $szSrcFolder."/".$v, $szDstFolder."/".$v );
			} else {
				copy( $szSrcFolder."/".$v, $szDstFolder."/".$v );
			}
		}
	} // function DirCopy
	
	/**
	 * 	Архивирует папку, сохраняя ее структуру
	 */
	function DirArchive( &$objArchive, $szFolder, $szDelFromStart ) {
		$arrFolder = scandir( $szFolder );
		$szArchiveFolder = str_replace( $szDelFromStart, '', $szFolder );
		$objArchive->addEmptyDir( $szArchiveFolder );
		foreach( $arrFolder as $i => $v ) {
			if ( $v == "." || $v === ".." ) {
				unset( $arrFolder[ $i ] );
			} elseif ( is_dir( $szFolder."/".$v ) ) {
				DirArchive( $objArchive, $szFolder."/".$v, $szDelFromStart );
			} else {
				$objArchive->addFile( $szFolder."/".$v, $szArchiveFolder."/".$v );
			}
		}
	} // function DirArchive
	
	/**
	 * 	Очищает папку и удаляет ее
	 */
	function DirClear( $szFolder ) {
		if ( file_exists( $szFolder ) ) {
			$arrFolder = scandir( $szFolder );
			foreach( $arrFolder as $i => $v ) {
				if ( $v == "." || $v == ".." ) {
					unset( $arrFolder[ $i ] );
				} elseif ( is_dir( $szFolder."/".$v ) ) {
					DirClear( $szFolder."/".$v );
				} else {
					if ( file_exists( $szFolder."/".$v ) ) {
						clearstatcache( );
						unlink( $szFolder."/".$v );
					}
				}
			}
			rmdir( $szFolder );
		}
	} // function DirClear
	
	function translit($str){
		static $transl = array(
			'А' => 'A',  'Б' => 'B',  'В' => 'V',  'Г' => 'G',  'Д' => 'D',  'Е' => 'E',  'Ё' => 'JO',  'Ж' => 'ZH',  'З' => 'Z',  'И' => 'I',
			'Й' => 'J', 'К' => 'K',  'Л' => 'L',  'М' => 'M',  'Н' => 'N',  'О' => 'O',  'П' => 'P',   'Р' => 'R',   'С' => 'S',  'Т' => 'T',
			'У' => 'U',  'Ф' => 'F',  'Х' => 'H',  'Ц' => 'C',  'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '',   'Ы' => 'Y',  'Ь' => '',
			'Э' => 'EH', 'Ю' => 'JU', 'Я' => 'JA', 'а' => 'a',  'б' => 'b',  'в' => 'v',  'г' => 'g',   'д' => 'd',   'е' => 'e',  'ё' => 'jo',
			'ж' => 'zh', 'з' => 'z',  'и' => 'i',  'й' => 'j', 'к' => 'k',  'л' => 'l',  'м' => 'm',   'н' => 'n',   'о' => 'o',  'п' => 'p',
			'р' => 'r',  'с' => 's',  'т' => 't',  'у' => 'u',  'ф' => 'f',  'х' => 'h',  'ц' => 'c',   'ч' => 'ch',  'ш' => 'sh', 'щ' => 'shh',
			'ъ' => '',  'ы' => 'y',  'ь' => '',  'э' => 'eh', 'ю' => 'ju', 'я' => 'ja'
		);
		return strtr($str, $transl);
	}
	
	function ConvertEsc( $arrMatch ) {
		$iChar = intval( $arrMatch[ 1 ] );
		//$iChar = octdec( $matches[ 1 ] );
		//$szChar = "";//( $iChar < 32 ? "\\".$iChar : chr( $iChar ) );
		/*if ( $iChar < 32 || $iChar == 127 ) {
			$szChar = "\\".$arrMatch[ 1 ];
		} else {
			$szChar = chr( $iChar );
		}*/
		//$szChar = chr( $iChar );
		//$szChar = "&#".$iChar.";";
		//return $szChar;
		return chr( $iChar );
	} // function ConvertEsc
	
?>