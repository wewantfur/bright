<?php
namespace bright\controllers;

use bright\core\utils\StringUtils;

require_once(dirname(__FILE__) . '/../core/Bright.php');

class JSONController {
	function __construct() {
		$v = filter_input(INPUT_GET, 'v', FILTER_SANITIZE_STRING);
		if(StringUtils::endsWith($v, '/'))
			$v = substr($v, 0, -1);
		$va = explode('/', $v);
		$va[0] = 'bright';
		$va[1] = 'core';
		$method = array_pop($va);
		$class = '\\'.implode('\\', $va);
		if(class_exists($class,true)) {
			$cls = new $class();
			if(method_exists($cls, $method)) {
				try {
					$params = (array) json_decode(file_get_contents('php://input'));
					if(isset($params) && array_key_exists('arguments', $params)) {
						$res = call_user_func_array(array($cls, $method), $params['arguments']);
					} else {
						$res = call_user_func(array($cls, $method));
					}
					$result = (object) array('status' => 'OK', 'result' => $res);
				} catch(\bright\api\core\Exception $ex) {
					$result = (object) array('status' => 'Failed', 'code' => $ex -> getCode(), 'message' => $ex -> getMessage());

				} catch(Exception $ex) {
// 					Alter header to trigger jquery error 
					header('HTTP/1.1 500 Internal Server Error'	);
					echo  trim($ex -> getCode());
					echo  trim($ex -> getMessage());
					
				}
				header("content-type: text/json");
				echo json_encode($result);
			} else {
				header('HTTP/1.1 500 Internal Server Error'	);
				trigger_error("INEXISTANT METHOD " . $method);
				
			}
		} else {
			header('HTTP/1.1 500 Internal Server Error'	);
			trigger_error("INEXISTANT CLASS " . $class);
		}
	}
}

$json = new JSONController();