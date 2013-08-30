<?php
namespace bright\core\frontend;

use bright\core\frontend\Bootstrap;

$ds = DIRECTORY_SEPARATOR;
require_once(dirname(__FILE__) . "{$ds}..${ds}Bright.php");

class Bootstrap {

	public static function setup() {
		$r = new Router();
		$r -> init();
// 		$r -> getRoute($path);
	}
}

Bootstrap::setup();