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
	 * Add something to the log file
	 */
	public static function log() {
		$statements = func_get_args();
		$handle = fopen(dirname(__FILE__) . '/logs/log.txt', 'a');
		foreach($statements as $statement) {
			if(!is_scalar($statement))
				$statement = var_export($statement, true);
		
			fwrite($handle, $statement . "\n");
		}
		fclose($handle);
	}
	
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
	
	
	/**
	 * Check if haystack starts with needle
	 * @param String $haystack
	 * @param String $needle
	 */
	public static function startsWith($haystack, $needle) {
		trigger_error('This method is deprecated, use StringUtils::startsWith', E_USER_DEPRECATED);
		return StringUtils::startsWith($haystack, $needle);
	}
	
	/**
	 * Check if haystack ends with needle
	 * @param String $haystack
	 * @param String $needle
	 */
	public static function endsWith($haystack, $needle) {
		trigger_error('This method is deprecated, use StringUtils::endsWith', E_USER_DEPRECATED);
		return StringUtils::endsWith($haystack, $needle);
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