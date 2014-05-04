<?php
use bright\core\plugins\PluginLocator;

use bright\core\Cache;

use bright\core\model\Model;

session_start();


/**
 * This is where the fun starts
 * @author ids
 *
 */
final class Bright {
	
	private static $_pluginLocator;

	public static function Init() {
		include_once(dirname(__FILE__) . '/../site/config/Constants.php');
		
		self::_SetHeaders();
		self::_SetIncludePaths();
		include('Autoloader.php');
		
		self::_SetupErrorHandlers();
		
		self::$_pluginLocator = new PluginLocator();
		self::$_pluginLocator -> add('string', '\bright\core\plugins\Plugin_string');
	}
	
	public static function GetPluginLocator() {
		return self::$_pluginLocator;
	}
	
	/**
	 * Set correct output header
	 */
	private static function _SetHeaders() {
		// 
		header('Content-Type: text/html; charset=utf-8');
	}
	
	/**
	 * Sets up the basic include paths. Include this file to any custom php file which is not included through the Bootstrap. (For instance, upload scripts or ajax calls)
	 * @author Ids Klijnsma - Fur
	 */
	private static function _SetIncludePaths() {
		
		
		ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR .
				BASEPATH . 'bright' . DIRECTORY_SEPARATOR . 'core' . PATH_SEPARATOR .
				BASEPATH . 'bright' . DIRECTORY_SEPARATOR . 'site');
		
		
		if(is_dir(BASEPATH . 'bright/externals')) {
			ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR .
					BASEPATH . 'bright/externals');
		}
		
	}
	
	private static function _SetupErrorHandlers() {
		
		date_default_timezone_set('Europe/Amsterdam');
		
		ini_set('display_errors', '1'); // display errors in the HTML
		ini_set('track_errors', '1'); // creates php error variable
		ini_set('log_errors', '1'); // writes to the log file
		error_reporting(E_ALL|E_STRICT);
		
		
		if(LIVESERVER === true) {
			ini_set('display_errors', '0'); // display errors in the HTML
			ini_set('track_errors', '0'); // creates php error variable
			error_reporting(0);
		}
	}
}





// function _fatal_handler() {
// 	$ex = error_get_last();
// 	if($ex['type'] === E_ERROR) {
// 		header('HTTP/1.1 500 Internal Server Error'	);
// 		echo 'FATAL ERROR:' . "\r\n";
// 		echo  trim($ex['message']);
// 	}
// }

// function _exceptionHandler(Exception $e) {
// 	header('HTTP/1.1 500 Internal Server Error'	);
// 	echo  trim($ex -> getCode());
// 	echo  trim($ex -> getMessage());


// function __construct() {
// 	error_log('CONSTRUCTING');
// 	set_exception_handler('_exceptionHandler');
// 	set_error_handler(function ($code, $message, $file, $line) {
// 		throw new ErrorException($message, $code, 0, $file, $line);
// 	});
// 	register_shutdown_function('_fatal_handler');

// }


// $c = new Cache();

// if(ROUTES != null) {
// 	$routes = json_decode(ROUTES);
// 	Model::getInstance() -> routes = $routes;
// }
