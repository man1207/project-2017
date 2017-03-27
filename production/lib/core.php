<?php

if (!$PROLOG_INCLUDED) {
	die();
}


/* ---------------------Конфиги--------------------- */
class Config {
	
	static function load($name){
		return include $_SERVER['DOCUMENT_ROOT'] . "/conf/{$name}.conf";
	}
}


/* ---------------------Авторизация--------------------- */
class Auth {
	
	private static $config;
	
	private static function start_session($login, $password) {		
		
		if (!Database::load(self::$config['servername'], $login, $password, self::$config['dbname'])) 
			return false;
				
		$_SESSION['login'] = $login;
		$_SESSION['password'] = $password;
		//$_SESSION['auth'] = true;
		
		return true;
		
	}
	
	static function logout() {
		session_start();
		unset($_SESSION['login']);
		unset($_SESSION['password']);
	}
	
	# Проверка сессии
	private static function check_session() {	
		//echo session_id();
		if (!(isset($_SESSION['login']) && isset($_SESSION['password'])) OR !Database::load(self::$config['servername'], $_SESSION['login'], $_SESSION['password'], self::$config['dbname'])) 
			return false;		
		return true;
	}
	
	# Вернуть объект авторизации
	static function load($login = '', $password = '') {

		session_start();

		# Загрузка конфига
		if (!self::$config)
			self::$config = Config::load('db');
		# Проверка сессии
		if (!self::check_session())
			return self::start_session($login, $password);
		return true;
	}
}


/* ---------------------Работа с БД--------------------- */
class Database {
	
	private static $connection = null;
	
	private static $success = 'success';
	
	# Вернуть объект базы данных
	static function load($servername, $username, $password, $dbname) {
		
		
		
		//self::$connection = null;
		
		if (!self::$connection)
			self::$connection = mysqli_connect($servername, $username, $password, $dbname);
		//echo self::$connection->error;
		//die();
		return self::$connection;
	}
	
	# Закрыть соединение с базой данных
	static function unload() {
		if (self::$connection)
			self::$connection->close();
	}
	
	# Выполнить запрос, который возвращает данные
	static function query($sql) {
		$result = array ();
		$db_result = self::$connection->query($sql);
		if (!$db_result)
			return self::$connection->error;	
		if (!is_object($db_result)) {
			return $db_result;
		}
		while($row = $db_result->fetch_assoc()) {
			$result[] = $row;
		}
		return $result;
	}	
	
	#
	static function affected_rows() {
		return self::$connection->affected_rows;
	}
}

?>