<?php
	global $objCMS;
	
	$objInclude = new CInclude( );
	$objInclude->Create( array(
		"suffix" => ".php",
		"labels" => array(
			"custom_handler" => $objCMS->GetPath( "root_application" )."/"
		),
		"items" => array(
			array( "label" => "custom_handler", "name" => "handler" )
		)
	) );
	$objInclude->Process( );
	$objCMS->Create( array(	"arrHandler" => array(
		array( "label" => "agent", "object" => "CHAgent" ), // агенты
		array( "label" => "stat", "object" => "CHStat" ), // статистика
		array( "label" => "default_javascript", "object" => "CHCustomJs" ), // скрипты
		array( "label" => "default_css", "object" => "CHCustomCss" ), // стили приложения
		array( "label" => "default_image", "object" => "CHCustomImage" ), // картинки приложения
	) ) );
	
	$objCMS->ApplyPath( "media_application", $objCMS->GetPath( "root_application" )."/media" );
	$objCMS->ApplyPath( "media_images", $objCMS->GetPath( "media_application" )."/images" );
?>