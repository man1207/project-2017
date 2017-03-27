<?php

# Защита от запуска скриптов в обход index.php
$PROLOG_INCLUDED = true;


/* ---------------------Подключение библиотек--------------------- */
# Подключаем ядро (работа с базой, конфигами, авторизацией)
require 'lib/core.php';

# Подключаем функции Инспектора
require 'lib/inspector.php';

# Подключаем диспатч
require 'lib/dispatch.php';


/* ---------------------Подключение конфигов--------------------- */
# Подключаем конфиги базы данных
$config['db'] = Config::load('db');


/* ---------------------Подключение роутов--------------------- */
# create a stack of actions
$routes = [ 
	# Главная страница
	action('GET', '/', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/main', ['header' => $header, 'footer' => $footer]);
		return response($html);
	}),
	# Авторизация
	action('GET', '/auth', function () {
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['auth' => false]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/auth', ['header' => $header]);
		return response($html);
	}),
	action('POST', '/auth', function () {	
		
		$login = $_REQUEST['login'];
		$password = $_REQUEST['password'];
		
		if (Auth::load($login, $password)) 
			return redirect('/');
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['auth' => false]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		# Тут пишем, что логин и пароль не верный и формируем страничку	
		$html = phtml(__DIR__ . '/templates/auth', ['header' => $header]);
		return response($html);
	}),
	action('POST', '/logout', function () {	
		
		Auth::logout();
		
		return redirect('/auth');
		
	}),
	# Журнал
	action('GET', '/journal', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$journal_data = Inspector::get_journal();
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Журнал', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/journal', ['header' => $header, 'footer' => $footer, 'journal' => $journal_data]);
		return response($html);
	}),
	# Журнал Удаление записей
	action('POST', '/journal', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$days =  $_POST['days'];
		
		$result = Inspector::remove_old_recs_journal($days);
		
		$journal_data = Inspector::get_journal();
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Журнал', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/journal', ['header' => $header, 'footer' => $footer, 'journal' => $journal_data, 'info' => $result]);
		return response($html);
	}),
	# Обслуживание базы
	action('GET', '/maintenance', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Обслуживание базы', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/maintenance', ['header' => $header, 'footer' => $footer]);
		return response($html);
	}),
	# Обслуживание базы
	action('POST', '/maintenance', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		
		if (isset($data['action']) && $data['action'] == 'start') {
			
			//Запускаем некий процесс
			$result = Inspector::reindex_db();
			$json = json_encode(array('action' => 'done'));
			
			Inspector::terminate();
		
			return response($json, 200, ['content-type' => 'application/json']);
			
		} else {
			
			Inspector::terminate();
			
			$header = phtml(__DIR__ . '/templates/header', ['title' => 'Обслуживание базы', 'auth' => true]);
			$footer = phtml(__DIR__ . '/templates/footer');
			
			$html = phtml(__DIR__ . '/templates/maintenance', ['header' => $header, 'footer' => $footer]);
			
			return response($html);
		}
	}),
	# Тарифы
	action('GET', '/tariff', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$list = Inspector::get_printerlist();
		
		$tariff = Inspector::get_tariff($list[0]); //Тарифы для Общий
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Тарифы', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/tariff', ['header' => $header, 'footer' => $footer, 'list' => $list, 'tariff' => $tariff]);
		return response($html);
	}),
	# Тарифы Получить и Установить
	action('POST', '/tariff', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		
		if (isset($data['action']) && $data['action'] == 'get') {
			//print_r($data);
			$printer = $data['printer'];
			$tariff = Inspector::get_tariff($printer); 
			Inspector::terminate();
			$json = json_encode($tariff);
			//print_r($json);
			return response($json, 200, ['content-type' => 'application/json']);
		}
		
		if (isset($data['action']) && $data['action'] == 'save') {	
			$items = $data['items'];
			$printer = $data['printer'];
			$result = Inspector::save_tariff($printer, $items);
			Inspector::terminate();
			if ($result)
				return response(json_encode(array('Записи обновлены')), 200, ['content-type' => 'application/json']);
			else	
				return response(json_encode(array('Произошла ошибка')), 200, ['content-type' => 'application/json']);
						
		}
		
		return response(json_encode(array()), 200, ['content-type' => 'application/json']);
	}),
	# Списки пользователей
	action('GET', '/list', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$list = Inspector::get_userlist();
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Списки пользователей', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/list', ['header' => $header, 'footer' => $footer, 'list' => $list]);
		return response($html);
	}),
	# Списки пользователей Удаление
	action('POST', '/list', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$list = Inspector::get_userlist();
		$name = $_POST['name'];
		$info = 'Удалено ' . $name . ' записей';
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Списки пользователей', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/list', ['header' => $header, 'footer' => $footer, 'list' => $list, 'info' => $info]);
		return response($html);
	}),
	# SQL-консоль
	action('GET', '/sql', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'SQL-консоль', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/sql', ['header' => $header, 'footer' => $footer]);
		return response($html);
	}),
	action('POST', '/sql', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');

		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		
		$data_to_send = Inspector::execute_command($data['request']);

		$json = json_encode($data_to_send);
		
		Inspector::terminate();
		
		return response($json, 200, ['content-type' => 'application/json']);
		
		//$html = phtml(__DIR__ . '/templates/sql', ['header' => $header, 'footer' => $footer]);
		//return response($html);
	}),
	# Досье
	action('GET', '/dossier', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Досье сотрудников', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/dossier', ['header' => $header, 'footer' => $footer]);
		return response($html);
	}),
	# Настройки
	action('GET', '/settings', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Настройки комплекса', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/settings', ['header' => $header, 'footer' => $footer]);
		return response($html);
	}),
	# Пользователи
	action('GET', '/users', function () {	
	
		if (!Auth::load()) 
			return redirect('/auth');
		
		$list = Inspector::get_db_users();
		
		Inspector::terminate();
		
		$header = phtml(__DIR__ . '/templates/header', ['title' => 'Пользователи базы', 'auth' => true]);
		$footer = phtml(__DIR__ . '/templates/footer');
		
		$html = phtml(__DIR__ . '/templates/users', ['header' => $header, 'footer' => $footer, 'list' => $list]);
		return response($html);
	}),
];


# we need the method and requested path
# Получаем метод и путь
$verb = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

# serve app against verb + path, pass dependencies
# запускаем диспатчер

//print_r($routes);

$responder = serve($routes, $verb, $path);

# invoke responder to flush response
$responder();

?>