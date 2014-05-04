<?php
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
		if(strpos($fname, '..') !== false)
			return false;
		
		if(file_exists($fname))
			include($fname);
	}
	if($classpath[0] == PACKAGE) {
		array_shift($classpath);
		$fname = BASEPATH . 'bright/site/' . implode(DIRECTORY_SEPARATOR, $classpath) . '.php';
		
		if(strpos($fname, '..') !== false)
			return false;
		
		if(file_exists($fname))
			include($fname);
	}
}