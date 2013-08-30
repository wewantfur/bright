<?php
namespace bright\core\frontend;


use bright\core\model\Model;

use bright\core\Utils;

class BasicView {
	
	private $_smarty;
	
	private $_data = array();
	
	private $_content;
	
	public function __construct($content) {
		// create object
		$cls = get_called_class();
		
		$this -> maintemplate = 'default';
		$this -> viewtemplate = strtolower(substr(get_called_class(), strrpos(get_called_class(), '\\')+1, -4)) . '.tpl';
		$this -> _smarty = new \Smarty();
		$ds = DIRECTORY_SEPARATOR;
		$this -> _smarty -> addTemplateDir(BASEPATH . "bright{$ds}site{$ds}templates{$ds}")
						 -> setCacheDir(BASEPATH . "bright{$ds}cache{$ds}smarty")
						 -> setCompileDir(BASEPATH . "bright{$ds}cache{$ds}smarty_c")
						 -> enableSecurity()
						 -> php_handling = \Smarty::PHP_REMOVE;
		$this -> _smarty -> error_reporting = E_ALL & ~E_NOTICE;
		$this -> _content = $content;
		$this -> language = $_SESSION[BSP]['language'];
	}
	
	public function __set($name, $value) {
		switch ($name) {
			case 'maintemplate':
			case 'viewtemplate':
				if(!Utils::endsWith($value, '.tpl')) {
					$value .= '.tpl';
				}
			break;
		}
		$this -> _data[$name] = $value;
	}
	
	public function __get($name) {
		if(array_key_exists($name, $this -> _data))
			return $this -> _data[$name];
		
		
		if(isset($this -> _content -> content -> $name)) {
			if(isset($this -> _content -> content -> {$name}[$this -> language])) {
				return $this -> _content -> content -> {$name}[$this -> language];
				
			}
		}
		return null;
	}
	
	/**
	 * Gets the url of the current page
	 * @param boolean $relative When true, the domain is omitted
	 * @param boolean $includeParameters When false, the GET parameters are omitted
	 * @return string The url of the current page
	 */
	public function getPageUrl($relative = false, $includeParameters = true) {
		if($relative) {
			$url = '';
		} else {
			$url = 'http';
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
				$url .= 's';
			
			$url .= '://' . $_SERVER['SERVER_NAME'];
			
			if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80) {
				$url .= ':80';
		}
		}
		$url .= $_SERVER['REQUEST_URI'];
		if($includeParameters === false) {
			$ua = explode('?');
			if(count($ua) > 1) {
				array_pop($ua);
			}
			$url = join('?', $ua);
		}
		return $url;
	}
	
	public final function render() {
		$this -> _smarty->assign('this', $this);
		// display it
		$this -> _smarty->display(BASEPATH . '/bright/site/templates/' . $this -> maintemplate);
	}
	
	public final function debug($variable) {
		echo '<pre>';
		print_r($variable);
		echo '</pre>';
	}
}