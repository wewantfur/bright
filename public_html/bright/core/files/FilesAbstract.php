<?php
namespace bright\core\files;

use bright\core\utils\StringUtils;

use bright\core\auth\Authorization;

use bright\core\exceptions\AuthException;

use bright\core\exceptions\FilesException;

abstract class FilesAbstract {
	
	/**
	 * Checks if the given path is accessible by the be_user
	 * @param string $path
	 * @throws Exception
	 * @return boolean
	 */
	protected static function isAllowedPath($path) {
		$path = filter_var($path, FILTER_SANITIZE_STRING);
		if ($path === false || $path === null) {
			throw new Exception('', Exception::INCORRECT_PARAM_STRING);
		}
	
		$bu = Authorization::getBEUser();
		if ($path == '/')
			$path = '';
	
		foreach ($bu->file_mountpoints as $mp) {
			if ($path == '' && $mp == '')
				return true;
	
			if (StringUtils::startsWith($path, $mp)) {
				return true;
			}
	
		}
	
		// Is super user?
		foreach ($bu->groups as $group) {
			if ($group->GID == Authorization::GR_SU) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Gets the full path of a folder
	 * @param String $path The relative path of the folder
	 */
	protected static function getFullPath($path) {
		if ($path == '/') {
			$path = '';
		} else {
			if (StringUtils::startsWith($path, '/')) {
				$path = substr($path, 1);
			}
	
			if (!StringUtils::endsWith($path, '/')) {
				$path .= '/';
			}
		}
	
		return BASEPATH . UPLOADFOLDER . $path;
	}
	
	/**
	 * Cleans a filename and generates a suffix if the filename exists
	 * @param String $filename The name of the file
	 * @param String $folder The path of the folder
	 * @return String The sanitized filename
	 */
	protected static function sanitizeFilename($filename, $folder) {
		$filename = strtolower(filter_var($filename, FILTER_SANITIZE_STRING));
		$ext = substr($filename, strrpos($filename, '.') + 1);
		$name = substr($filename, 0, strrpos($filename, '.'));
		$sanitized = preg_replace('/[^A-z0-9_\-\.]/i', '-', $name);
		while (strpos($sanitized, '--') !== false) {
			$sanitized = str_replace('--', '-', $sanitized);
		}
	
		$count = -1;
	
		if (strpos($sanitized, '-')) {
			$c = substr($sanitized, strrpos($sanitized, '-') + 1);
			if (is_numeric($c)) {
				$count = $c * -1;
				$sanitized = substr($sanitized, 0, strrpos($sanitized, '-'));
			}
		}
	
		if (file_exists(self::getFullPath($folder) . $sanitized . ".$ext")) {
			while (file_exists(self::getFullPath($folder) . $sanitized . $count . ".$ext")) {
				$count--;
			}
	
			$sanitized = $sanitized . $count;
		}
	
		return $sanitized . '.' . $ext;
	}
	
	/**
	 * Validates if the given string is a valid filename
	 * @param String $file The name to check
	 * @throws FilesException
	 * @return The cleaned / checked filename
	 */
	protected static function validateFilename($file) {
		$file = filter_var($file, FILTER_SANITIZE_STRING);
	
		if ($file === false || $file === null || trim($file) == '') {
			throw new FilesException('', FilesException::INVALID_FILE_NAME);
		}
	
		$file = trim($file);
		// Sanitize input
		if (strpos($file, '..') !== false || preg_match('/[^A-z0-9_\-\.]/', $file)) {
			throw new FilesException('', FilesException::INVALID_FILE_NAME);
		}
	
		return $file;
	}
	
	
	
	/**
	 * Checks if the given path is existing and accessible by the beuser
	 * @param string $path
	 * @throws FilesException
	 * @throws AuthException
	 * @return string The path
	 */
	public static function validateFolder($path) {
		$path = filter_var($path, FILTER_SANITIZE_STRING);
		if ($path === false || $path == null || strpos($path, '..') !== false) {
			throw new FilesException('', FilesException::INVALID_FOLDER_NAME);
		}
	
		// Check if beuser has access
		if (!self::isAllowedPath($path)) {
			throw new AuthException('', AuthException::PERMISSION_DENIED);
		}
	
		// Store original
		$opath = $path;
		if ($path == '/') {
			$path = '';
		}
	
		if (StringUtils::startsWith($path, '/')) {
			$path = substr($path, 1);
		}
	
		if ($path != '' && !StringUtils::endsWith($path, '/')) {
			$path .= '/';
		}
	
		if (!is_dir(BASEPATH . UPLOADFOLDER . $path)) {
			throw new FilesException($path, FilesException::FOLDER_NOT_FOUND);
		}
	
		return $opath;
	}
	
}