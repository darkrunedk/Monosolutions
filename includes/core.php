<?php

ob_start();
session_start();

$GLOBALS['config'] = array(
	'mysql' => array(
		'hostname' => "localhost",
		'username' => "root",
		'password' => "",
		'database' => "monosolutions"
	)
);

define("FILEPATH", realpath(dirname(__FILE__)));

// Automatic load of classes
spl_autoload_register(function($class){
	if (file_exists(FILEPATH . '/../classes/' . $class .'.php')) {
		require_once FILEPATH . '/../classes/' . $class .'.php';
	}
});

?>
