<?php
namespace bright\core;

use bright\core\exceptions\FilesException;

class Cache {
	function __construct() {
		if(!is_dir(BASEPATH . 'bright/cache')) {
			$result = @mkdir(BASEPATH . 'bright/cache');
			
			if($result == false) {
				throw new FilesException('cache', FilesException::CANNOT_CREATE_DIR);
			}
		}
	}
}