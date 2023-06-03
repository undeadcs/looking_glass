<?php
	global $objCMS, $objCurrent, $iCurrentSysRank, $szCurrentMode, $mxdCurrentData, $arrErrors, $mxdLinks;

	header( "Content-Type: text/html; charset=windows-1251" );
	
	$objPage = new CPage( );
	$objPage->SetTitle( "Looking Glass" );
	$objPage->AddMeta( array( "http_equiv" => "Content-Type", "content" => "text/html; charset=windows-1251" ) );
	$objPage->AddStyle( $objCMS->GetPath( "root_relative" )."/admin/main.css" );
	$objPage->AddScript( $objCMS->GetPath( "root_relative" )."/admin/jquery.js" );
	
	$domDoc = new DOMDocument( );
	$domXsl = new DOMDocument( );
	$objXlst = new XSLTProcessor( );
	
	$objDoc = $domDoc->createElement( "Doc" );
	$objDoc->setAttribute( "logo_url", $objCMS->GetPath( "root_relative" )."/admin/" );
	$objDoc->setAttribute( "logo_src", $objCMS->GetPath( "root_relative" )."/admin/skin/logo.gif" );
	$domDoc->appendChild( $objDoc );
	
	$domXsl->load( $objCMS->GetPath( "root_application" )."/main.xsl" );
	
	$tmp = $objCMS->GetMenu( $domDoc );
	if ( $tmp->HasResult( ) ) {
		$objDoc->appendChild( $tmp->GetResult( "doc" ) );
	}
	
	if ( $objCurrent && $szCurrentMode ) {
		$doc = $domDoc->createElement( $objCurrent.$szCurrentMode );
		$objDoc->appendChild( $doc );
		
		if ( !empty( $arrErrors ) ) {
			foreach( $arrErrors as $i => $v ) {
				$tmp = $domDoc->createElement( "Error" );
				$tmp->setAttribute( "text", iconv( "cp1251", "UTF-8", $v->text ) );
				$doc->appendChild( $tmp );
			}
		}
		if ( $objCurrent === "Install" ) {
			$domDoc = new DOMDocument( );
			$doc = $domDoc->createElement( $objCurrent.$szCurrentMode );
			$doc->setAttribute( "logo_url", $objCMS->GetPath( "root_relative" )."/admin/" );
			$doc->setAttribute( "logo_src", $objCMS->GetPath( "root_relative" )."/admin/skin/logo.gif" );
			$domDoc->appendChild( $doc );
			
			if ( !empty( $arrErrors ) ) {
				foreach( $arrErrors as $i => $v ) {
					$tmp = $domDoc->createElement( "Error" );
					$tmp->setAttribute( "text", iconv( "cp1251", "UTF-8", $v->text ) );
					$doc->appendChild( $tmp );
				}
			}
			
			$doc->setAttribute( "post_url", $objCMS->GetPath( "root_relative" )."/admin/$/" );
			
			$arrNeed = array( "db", "superadmin" );
			foreach( $arrNeed as $v ) {
				$tmp = $mxdCurrentData[ $v ]->GetXML( $domDoc );
				if ( $tmp->HasResult( ) ) {
					$doc->appendChild( $tmp->GetResult( "doc" ) );
				}
			}
			
			$objPage->StartBody( );
			
			$objXlst->importStylesheet( $domXsl );
			$szText = $objXlst->transformToXml( $domDoc );
			$szText = iconv( "UTF-8", "cp1251//TRANSLIT", $szText );
			$szText = preg_replace( '/<textarea([^>]*)\/>/', '<textarea$1></textarea>', $szText );
			echo $szText;
			
			$objPage->EndBody( );
			echo $objPage->GetDoc( );
			return;
		}
		if ( $objCurrent === "Login" ) {
			$objPage->SetTitle( "Looking Glass / Вход в систему" );
			$domDoc = new DOMDocument( );
			$doc = $domDoc->createElement( $objCurrent.$szCurrentMode );
			$doc->setAttribute( "post_url", $objCMS->GetPath( "root_relative" )."/admin/" );
			$doc->setAttribute( "logo_url", $objCMS->GetPath( "root_relative" )."/admin/" );
			$doc->setAttribute( "logo_src", $objCMS->GetPath( "root_relative" )."/admin/skin/logo.gif" );
			$domDoc->appendChild( $doc );
			
			$objPage->StartBody( );
			
			$objXlst->importStylesheet( $domXsl );
			$szText = $objXlst->transformToXml( $domDoc );
			$szText = iconv( "UTF-8", "cp1251//TRANSLIT", $szText );
			$szText = preg_replace( '/<textarea([^>]*)\/>/', '<textarea$1></textarea>', $szText );
			echo $szText;
			
			$objPage->EndBody( );
			echo $objPage->GetDoc( );
			return;
		}
		if ( $objCurrent === "Agent" ) {
			if ( $szCurrentMode === "List" ) {
				$objPage->SetTitle( "Looking Glass / Агенты" );
				$doc->setAttribute( "base_url", $objCMS->GetPath( "root_relative" )."/admin" );
				if ( isset( $mxdCurrentData[ "agent_list" ] ) ) {
					foreach( $mxdCurrentData[ "agent_list" ] as $v ) {
						$tmp = $v->GetXML( $domDoc );
						if ( $tmp->HasResult( ) ) {
							$doc->appendChild( $tmp->GetResult( "doc" ) );
						}
					}
				}
			}
			if ( $szCurrentMode === "Edit" ) {
				if ( $mxdCurrentData[ "current_agent" ]->id ) {
					$objPage->SetTitle( "Looking Glass / Данные агента" );
					$doc->setAttribute( "mode", "edit" );
				} else {
					$objPage->SetTitle( "Looking Glass / Добавление агента" );
					$doc->setAttribute( "mode", "add" );
				}
				$doc->setAttribute( "base_url", $objCMS->GetPath( "root_relative" )."/admin" );
				$tmp = $mxdCurrentData[ "current_agent" ]->GetXML( $domDoc );
				if ( $tmp->HasResult( ) ) {
					$doc->appendChild( $tmp->GetResult( "doc" ) );
				}
			}
		}
		if ( $objCurrent === "Stat" ) {
			if ( $szCurrentMode === "List" ) {
				$doc->setAttribute( "base_url", $objCMS->GetPath( "root_relative" )."/admin/stat" );
				$objPage->SetTitle( "Looking Glass / Статистика" );
				$objPage->AddScript( $objCMS->GetPath( "root_relative" )."/admin/calendar.js" );
				$objPage->AddScript( $objCMS->GetPath( "root_relative" )."/admin/custom.js" );
				$tmp = $mxdCurrentData[ "filter" ]->GetXML( $domDoc );
				if ( $tmp->HasResult( ) ) {
					$doc->appendChild( $tmp->GetResult( "doc" ) );
				}
				
				foreach( $mxdCurrentData[ "log_list" ] as $v ) {
					$tmp = $v->GetXML( $domDoc );
					if ( $tmp->HasResult( ) ) {
						$tmp = $tmp->GetResult( "doc" );
						$szAttr = $tmp->getAttribute( "log_addr" );
						if ( strlen( $szAttr ) > 50 ) {
							$szAttr = wordwrap( $szAttr, 50, "<br/>", true );
							$tmp->setAttribute( "log_addr", $szAttr );
						}
						$doc->appendChild( $tmp );
					}
				}
				
				$tmp = $mxdCurrentData[ "pager" ]->GetXML( $domDoc );
				if ( $tmp->HasResult( ) ) {
					$doc->appendChild( $tmp->GetResult( "doc" ) );
				}
			}
		}
	}
	
	$objPage->StartBody( );
	
	$objXlst->importStylesheet( $domXsl );
	$szText = $objXlst->transformToXml( $domDoc );
	$szText = iconv( "UTF-8", "cp1251//TRANSLIT", $szText );
	$szText = preg_replace( '/<textarea([^>]*)\/>/', '<textarea$1></textarea>', $szText );
	$szText = preg_replace( '/<script([^>]*)\/>/', '<script$1></script>', $szText );
	$szText = preg_replace( '/<a([^>]*)\/>/', '<a$1></a>', $szText );
	echo $szText;
	
	$objPage->EndBody( );
	echo $objPage->GetDoc( );
	echo '<!-- '._usr_time_work( ).'-->';
?>
