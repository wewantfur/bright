<?php
use bright\core\model\Model;

session_start();

/**
 * Sets up the basic include paths. Include this file to any custom php file which is not included through the Bootstrap. (For instance, upload scripts or ajax calls)
 * @author Ids Klijnsma - Fur
 */
ini_set('display_errors', '1'); // display errors in the HTML
ini_set('track_errors', '1'); // creates php error variable
ini_set('log_errors', '1'); // writes to the log file
error_reporting(E_ALL|E_STRICT);

// Set correct output header
header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set('Europe/Amsterdam');
include_once(dirname(__FILE__) . '/../site/config/Constants.php');

if(LIVESERVER === true) {
	ini_set('display_errors', '0'); // display errors in the HTML
	ini_set('track_errors', '0'); // creates php error variable
	error_reporting(0);
}

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR .
					BASEPATH . 'bright' . DIRECTORY_SEPARATOR . 'core' . PATH_SEPARATOR .
					BASEPATH . 'bright' . DIRECTORY_SEPARATOR . 'site');


if(is_dir(BASEPATH . 'bright/externals')) {
	ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR .
						BASEPATH . 'bright/externals');
}

require BASEPATH . 'vendor/autoload.php';
spl_autoload_register('_loader');
function _loader($classname) {
	$classpath = explode('\\', $classname);
	
	if($classname == 'Smarty') {
		$fname = BASEPATH . '/bright/externals/smarty/libs/Smarty.class.php';
		include($fname);
	}
	if(count($classpath) == 1)
		return false;
	
	if($classpath[1] == 'StdClass')
		return false;
	
	
	if($classpath[0] == 'bright') {
		$fname = BASEPATH . implode(DIRECTORY_SEPARATOR, $classpath) . '.php';
		if(strpos($fname, '..') === false) {
			if(file_exists($fname))
				include($fname);
		}
	}
	if($classpath[0] == PACKAGE) {
		array_shift($classpath);
		$fname = BASEPATH . 'bright/site/' . implode(DIRECTORY_SEPARATOR, $classpath) . '.php';
		if(strpos($fname, '..') === false) {
			if(file_exists($fname))
				include($fname);
		}
	}
}

function _fatal_handler() {
	$ex = error_get_last();
	if($ex['type'] === E_ERROR) {
		header('HTTP/1.1 500 Internal Server Error'	);
		echo 'FATAL ERROR:' . "\r\n";
		echo  trim($ex['message']);
	}
}

function _exceptionHandler(Exception $e) {
	header('HTTP/1.1 500 Internal Server Error'	);
	echo  trim($ex -> getCode());
	echo  trim($ex -> getMessage());
}

function __construct() {
	set_exception_handler('_exceptionHandler');
	register_shutdown_function('_fatal_handler');

}

if(ROUTES != null) {
	$routes = json_decode(ROUTES);
	Model::getInstance() -> routes = $routes;
}
