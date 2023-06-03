<?php
	// project system config
	global $g_arrConfig;
	$g_arrConfig = array(
		// includes
		"include" => array(
			"suffix" => ".php",
			"labels" => array(
				"root" => "",
				"core" => "includes/core/",
				"util" => "includes/utils/",
				"plugin" => "includes/plugins/",
				"sdk" => "includes/sdk/"
			),
			"items" => array(
				//
				array( "label" => "root", "name" => "db" ),
				// core - ядро
				array( "label" => "core", "name" => "output" ), // вывод
				array( "label" => "core", "name" => "filter" ), // фильтр
				array( "label" => "core", "name" => "system" ), // система
				array( "label" => "core", "name" => "handler" ), // обработчик ( перехватчик = обработчик запроса )
				array( "label" => "core", "name" => "page" ), // страница
				array( "label" => "core", "name" => "html" ), // html
				array( "label" => "core", "name" => "account" ), // аккаунт
				array( "label" => "core", "name" => "database" ), // работа с базой данных
				array( "label" => "core", "name" => "graph" ), // граф
				// utils - утилиты
				array( "label" => "util", "name" => "showvar" ), // показ переменных
				array( "label" => "util", "name" => "filter" ), // фильтры
				array( "label" => "util", "name" => "handler" ), // обработчики
				array( "label" => "util", "name" => "menu" ), // меню
				array( "label" => "util", "name" => "validator" ), // проверялка различных переменных
				array( "label" => "util", "name" => "misc" ), // всякая всячина
				array( "label" => "util", "name" => "pager" ), // пейджер
				array( "label" => "util", "name" => "mail" ), // отправка писем
				// plugins - расширения ( плагины )
				array( "label" => "plugin", "name" => "install" ), // установщик
				array( "label" => "plugin", "name" => "login" ), // форма входа
				array( "label" => "plugin", "name" => "user" ), // пользователи
				array( "label" => "plugin", "name" => "client" ), // клиентская часть
				array( "label" => "plugin", "name" => "agent" ), // агенты
				array( "label" => "plugin", "name" => "stat" ), // статистика
			)
		),
		// system
		"system" => array(
			"arrHProc" => array(
				"test" => "Test",
				"proc" => "Process"
			),
			"arrPath" => array(
			),
			"arrHandler" => array(
				array( "label" => "default_javascript", "object" => "CJavaScriptHandler" ), // запросы к js файлам
				array( "label" => "default_css", "object" => "CCSSHandler" ), // запросы к css файлам
				array( "label" => "default_image", "object" => "CImageHandler" ), // запросы к картинкам jpg, png, gif
				array( "label" => "login", "object" => "CHModLogin" ), // вход
				array( "label" => "install", "object" => "CHModInstall" ), // установщик
				array( "label" => "client", "object" => "CHModClient" ), // клиентская часть
			),
			"arrConfig" => array(
				"graph" => array(
					"vertex" => array(
						"table" => "ud_vertex",
						"object_name" => "CVertex",
						"index_attr" => "vertex_id"
					),
					"edge" => array(
						"table" => "ud_edge",
						"object_name" => "CEdge",
						"index_attr" => "edge_id"
					)
				)
			),
		),
	);

?>