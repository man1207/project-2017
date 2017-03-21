<?php

if (!$PROLOG_INCLUDED) {
	die();
}


/* ---------------------Конфиги--------------------- */
function get_config($name){
	return include $_SERVER['DOCUMENT_ROOT'] . "conf/{$name}.conf";
}


/* ---------------------Авторизация--------------------- */
function auth_user($login, $password) {
	
}


/* ---------------------Работа с БД--------------------- */
class Database {
	
	
}
function db_connect($config) {
	
} 

?>