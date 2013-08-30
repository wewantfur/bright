<?php
namespace bright\core\utils;

class StringUtils {
	
	/**
	 * Check if haystack starts with needle
	 * @param String $haystack
	 * @param String $needle
	 */
	public static function startsWith($haystack, $needle) {
		return !strncmp($haystack, $needle, strlen($needle));
	}
	
	/**
	 * Check if haystack ends with needle
	 * @param String $haystack
	 * @param String $needle
	 */
	public static function endsWith($haystack, $needle) {
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
	
		return (substr($haystack, -$length) === $needle);
	}
}