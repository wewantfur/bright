<?php 
namespace bright\core;

/**
 *
 * @author Ids
 * @package bright\api
 * @version 3.0
 */
use bright\core\utils\StringUtils;

class Utils {
	
	/**
	 * Mysql statement / call_user_func_array bug,
	 * @see http://stackoverflow.com/questions/2045875/pass-by-reference-problem-with-php-5-3-1
	 * @param array $arr
	 */
	public static function makeValuesReferenced($arr){
    	$refs = array();
    	foreach($arr as $key => $value)
	        $refs[$key] = &$arr[$key];
	    return $refs;
	}
	
	/**
	 * Forces a certain protocol to a link
	 * @param string $link
	 * @param string $_protocol
	 */
	public static function sanitize_http_protocol($link,$_protocol='http'){
		return $_protocol.'://'.array_pop(explode('://',$link));
	}
	
	
	
	public static function createTree(&$pages) {
		$root = array_shift($pages);
		if(!empty($pages)) {
			$parent = $root;
			foreach($pages as $page) {
				while(!self::_isChildOf($page, $parent)) {
					$parent = $parent -> parent;
				}
				$page -> parent = $parent;
				$parent -> children[] = $page;
				$parent = $page;
			} 
		}
		return $root;
	}
	
	/**
	 * Generates a salt
	 * @return String
	 */
	public static function salt() {
		return mcrypt_create_iv(222);
	}
	
	/**
	 * Strips all the properties from a vo which is not defined in the class
	 * @param Object $vo
	 * @return The stripped vo
	 */
	public static function stripVO($input) {
		$vo = clone $input;
		$r = new \ReflectionClass(get_class($vo));
		$properties = $r-> getProperties();
		$props = array();
		foreach($properties as $prop) {
			$props[] = $prop->getName();
		}
		foreach($vo as $key => $value) {
				
			if(!in_array($key, $props)) {
				unset($vo -> $key);
			}
		}
		return $vo;
	}
	
	private static function _isChildOf($child, $parent) { 
        return ($child -> lft > $parent -> lft && $child -> rgt < $parent -> rgt); 
    } 
}