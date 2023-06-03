<?php
	global $objCMS, $objCurrent, $iCurrentSysRank, $szCurrentMode, $mxdCurrentData, $arrErrors, $mxdLinks;

	header( "Content-Type: text/html; charset=cp1251" );
	
	$objPage = new CPage( );
	$objPage->SetTitle( "Looking Glass" );
	$objPage->AddMeta( array( "http_equiv" => "Content-Type", "content" => "text/html; charset=cp1251" ) );
	$objPage->AddStyle( $objCMS->GetPath( "root_relative" )."/main.css" );
	$objPage->AddScript( $objCMS->GetPath( "root_relative" )."/jquery.js" );
	$objPage->AddScript( $objCMS->GetPath( "root_relative" )."/custom.js" );
	
	$domDoc = new DOMDocument( );
	$domXsl = new DOMDocument( );
	$objXlst = new XSLTProcessor( );
	
	$objDoc = $domDoc->createElement( "Doc" );
	$objDoc->setAttribute( "logo_url", $objCMS->GetPath( "root_relative" )."/" );
	$objDoc->setAttribute( "logo_src", $objCMS->GetPath( "root_relative" )."/skin/logo.gif" );
	$domDoc->appendChild( $objDoc );
	
	$domXsl->load( $objCMS->GetPath( "root_application" )."/main_client.xsl" );
	
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
		
		if ( $objCurrent === 'Client' ) {
			if ( $szCurrentMode == 'Help' ) {
				$objPage->SetTitle( 'Looking Glass / Помощь' );
				$doc->setAttribute( 'root_relative', $objCMS->GetPath( 'root_relative' ) );
			}
			/*if ( $szCurrentMode === "Mode" ) {
				$doc->setAttribute( "post_url", $objCMS->GetPath( "root_relative")."/" );
				foreach( $mxdCurrentData[ "agent_list" ] as $v ) {
					$tmp = $v->GetXML( $domDoc );
					if ( $tmp->HasResult( ) ) {
						$doc->appendChild( $tmp->GetResult( "doc" ) );
					}
				}
				$tmp = $mxdCurrentData[ "current_queryform" ]->GetXML( $domDoc );
				if ( $tmp->HasResult( ) ) {
					$doc->appendChild( $tmp->GetResult( "doc" ) );
				}
			}*/
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
	if ( $mxdCurrentData[ "current_query" ] === NULL ) {
		echo $objPage->GetDoc( );
	} else {
		ShowVarD( $mxdCurrentData[ "current_query" ] );
	}
	
?>
