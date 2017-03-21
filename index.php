<?php

# Защита от запуска скриптов в обход index.php
$PROLOG_INCLUDED = true;


/* ---------------------Подключение библиотек--------------------- */
# Подключаем ядро (работа с базой, конфигами, авторизацией)
require 'lib/core.php';

# Подключаем диспатч
require 'lib/dispatch.php';


/* ---------------------Подключение конфигов--------------------- */
# Подключаем конфиги базы данных
$config['db'] = get_config('db');


/* ---------------------Подключение роутов--------------------- */
# create a stack of actions
$routes = [
  action('GET', '/', function ($db, $config) {
	# Везде надо воткнуть проверку авторизации
	
    // $list = loadAllBooks($db);
    // $json = json_encode($list);
    // return response($json, 200, ['content-type' => 'application/json']);
  }),
  /* action('GET', '/books/:id', function ($args, $db) {
    $book = loadBookById($db, $args['id']);
    $html = phtml(__DIR__.'/views/book', ['book' => $book]);
    return response($html);
  }), */
  /* action('GET', '/about', page(__DIR__.'/views/about')) */
];

# sample dependencies
$config = require __DIR__.'/config.php';
$db = createDBConnection($config['db']);

# we need the method and requested path
# Получаем метод и путь
$verb = $_SERVER['REQUEST_METHOD'],
$path = $_SERVER['REQUEST_URI'],

# serve app against verb + path, pass dependencies
# запускаем диспатчер
$responder = serve($routes, $verb, $path, $db, $config);

# invoke responder to flush response
$responder();