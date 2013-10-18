<?php
namespace bright\core\files;

/**
 * Handles all the filesystem activities (creating files & folders)
 * @author Ids
 *
 */
use bright\core\exceptions\AuthException;

use bright\core\auth\Authorization;

use bright\core\exceptions\FilesException;

use bright\core\exceptions\Exception;

use bright\core\utils\StringUtils;

use bright\core\model\vo\File;

use bright\core\model\vo\Folder;

use bright\core\Utils;


class Files {
	
	public static function deleteFile($file, $parent) {
		
		$parent = self::_sanitizeFolder($parent);
		$file = self::_sanitizeFile($file);
		
		$fp = self::_getFullPath($parent);
		
		// We're allowed to delete, now check if file exists
		if(!is_file($fp . $file))
			throw new FilesException('', FilesException::FILE_NOT_FOUND);
		
		// We're allowed to delete, now check if its not a dir
		if(is_dir($fp . $file))
			throw new FilesException('', FilesException::NOT_A_FILE);
		
		$result = @unlink($fp . $file);
		
		if($result === false)
			throw new FilesException('', FilesException::CANNOT_DELETE_FILE);
		
		return self::getFiles($parent);
	}
	
	public static function deleteFolder($folder, $parent) {
		$parent = self::_sanitizeFolder($parent);
		$folder = self::_sanitizeFolder($parent . $folder);
		
		$fp = self::_getFullPath($folder);
		
		if(!is_dir($fp)) {
			throw new FilesException($folder, FilesException::FOLDER_NOT_FOUND);
		}
		
		$result = @rmdir($fp);
		
		if($result === false)
			throw new FilesException($folder, FilesException::CANNOT_DELETE_FOLDER);
		
		return self::getFolders($parent);
		
	}
	
	/**
	 * Gets all the files in the given dir
	 * @param string $parent The parent folder
	 * @throws Exception
	 */
	public static function getFiles($parent = null) {
		$parent = self::_sanitizeFolder($parent);
		$path = self::_getFullPath($parent);
	
		$contents = scandir($path);
			
		$files = array();
		foreach($contents as $item) {
			if(!is_dir($path . $item)) {
				$f = new File();
				$f -> path = $parent . $item;
				$f -> label = $item;
				$info = pathinfo($path . $item);
				$f -> extension = $info['extension'];
				$f -> size = filesize($path . $item);
				$files[] = $f;
			}
		}
		return $files;
	}
	
	public static function getFolders($parent = null) {
		
		if($parent) {
			$parent = self::_sanitizeFolder($parent);
		} else {
			return self::_getBaseFolders();
		}
		
		$path = self::_getFullPath($parent);
		
		$contents = scandir($path);
			
		$folders = array();
		foreach($contents as $item) {
			if($item != '.' && $item != '..' && is_dir($path . $item)) {
				$f = new Folder();
				$f -> path = $parent . $item;
				$f -> label = $item;
				$f -> haschildren = count(glob(BASEPATH . UPLOADFOLDER . $f -> path . '/*' , GLOB_ONLYDIR)) > 0;
				$folders[] = $f;
			}
		}
		return $folders;
		
	}
	
	public static function moveFile($file, $oldparent, $newparent) {
		$file = self::_sanitizeFile($file);
		
		$oldparent = self::_sanitizeFolder($oldparent);
		$newparent = self::_sanitizeFolder($newparent);
		
		$oldfp = self::_getFullPath($oldparent);
		$newfp = self::_getFullPath($newparent);
		
		if(!file_exists($oldfp . $file))
			throw new FilesException($file, FilesException::FILE_NOT_FOUND);
		
		if(file_exists($newfp . $file))
			throw new FilesException('', FilesException::FILE_ALREADY_EXISTS);
		
		$result = @rename($oldfp . $file, $newfp . $file);
		if($result === false)
			throw new FilesException('', FilesException::CANNOT_MOVE_FILE);
		
		return self::getFiles($oldparent);
	}
	
	public static function setFile($file, $parent) {
		throw new Exception(__METHOD__ , Exception::NOT_IMPLEMENTED);
	}
	
	public static function setFolder($folder, $parent) {
		$folder = filter_var($folder, FILTER_SANITIZE_STRING);
		$parent = self::_sanitizeFolder($parent);
		
		// Sanitize input
		if(strpos($folder, '..') || preg_match('/[^A-z0-9_\-]/', $folder))
			throw new FilesException('', FilesException::INVALID_FOLDER_NAME);
		
		$fp = self::_getFullPath($parent);
		
		// We're allowed to create, now check if file exists
		if(is_dir($fp . $folder))
			throw new FilesException('', FilesException::FOLDER_ALREADY_EXISTS);
		
		$result = @mkdir($fp . $folder);
		
		if($result === false)
			throw new FilesException('', FilesException::CANNOT_CREATE_DIR);
		
		return self::getFolders($parent);
	}
	
	/**
	 * Gets the 'entrypoints' for the currently logged in administrator
	 */
	private static function _getBaseFolders() {
		$f = new Folder();
		$f -> label = 'Files';
		$f -> path = '/';
		$f -> haschildren = true;
		$f -> isroot = true;
		return array($f);
	}
	
	/**
	 * Checks if the given path is accessible by the be_user
	 * @param string $path
	 * @throws Exception
	 * @return boolean
	 */
	private static function _isAllowedPath($path) {
		$path = filter_var($path, FILTER_SANITIZE_STRING);
		if($path === false || $path === null)
			throw new Exception('', Exception::INCORRECT_PARAM_STRING);
		
		$bu = Authorization::getBEUser();
		if($path == '/')
			$path = '';
		
		foreach($bu -> file_mountpoints as $mp) {
			if($path == '' && $mp == '') {
				return true;
			} else {
				if(StringUtils::startsWith($path, $mp))
					return true;
				
			}
		}
		
		// Is super user?
		foreach($bu -> groups as $group) {
			if($group -> GID == Authorization::GR_SU)
				return true;
		}
		return false;
	}
	
	private static function _getFullPath($path) {
		if($path == '/') {
			$path = '';
		} else if(!StringUtils::endsWith($path, '/'))
			$path .= '/';
		
		return BASEPATH . UPLOADFOLDER . $path;
	}
	
	private static function _sanitizeFile($file) {
		$file = filter_var($file, FILTER_SANITIZE_STRING);
		
		if($file === false || $file === null || trim($file) == '') 
			throw new FilesException('', FilesException::INVALID_FILE_NAME);
		
		$file = trim($file);
		// Sanitize input
		if(strpos($file, '..') !== false || preg_match('/[^A-z0-9_\-\.]/', $file))
			throw new FilesException('', FilesException::INVALID_FILE_NAME);
		
		return $file;
	}
	
	/**
	 * Checks if the given path is existing and accessible by the beuser
	 * @param string $path
	 * @throws FilesException
	 * @throws AuthException
	 * @return string The path
	 */
	private static function _sanitizeFolder($path) {
		$path = filter_var($path, FILTER_SANITIZE_STRING);
		if($path === false || $path == null || strpos($path, '..') !== false)
			throw new FilesException('', FilesException::INVALID_FOLDER_NAME);
		
		// Check if beuser has access
		if(!self::_isAllowedPath($path))
			throw new AuthException('', AuthException::PERMISSION_DENIED);
		
		// Store original
		$opath = $path;
		if($path == '/')
			$path = '';
		
		if(!is_dir(BASEPATH . UPLOADFOLDER . $path))
			throw new FilesException($path, FilesException::FOLDER_NOT_FOUND);
		
		return $opath;
	}
}