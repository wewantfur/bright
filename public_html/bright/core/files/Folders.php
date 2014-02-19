<?php

namespace bright\core\files;

use bright\core\model\vo\Folder;

class Folders extends FilesAbstract {
	
	
	/**
	 * Deletes a folder
	 * @param String $folder The name of the folder to delete
	 * @param String $parent The (relative) path of the parent folder
	 * @throws FilesException
	 * @return Array The subfolders of $parent
	 */
	public static function deleteFolder($folder, $parent) {
		$parent = self::validateFolder($parent);
		$folder = self::validateFolder($parent . $folder);
	
		$fp = self::getFullPath($folder);
	
		if (!is_dir($fp)) {
			throw new FilesException($folder, FilesException::FOLDER_NOT_FOUND);
		}
	
		$result = @rmdir($fp);
	
		if ($result === false) {
			throw new FilesException($folder, FilesException::CANNOT_DELETE_FOLDER);
		}
	
		return self::getFolders($parent);
	}
	
	
	
	public static function getAllFolders() {
		if (!Authorization::inGroup(Authorization::GR_WEBMASTER)) {
			throw new AuthException(Authorization::GR_WEBMASTER, AuthException::NOT_IN_GROUP);
		}
	
		$brit = new SortedDirectoryIterator(new \RecursiveIteratorIterator(new BrightDirectoryIterator(BASEPATH . UPLOADFOLDER), \RecursiveIteratorIterator::CHILD_FIRST));
		$f = new Folder();
		$f->label = substr(UPLOADFOLDER, 0, -1);
		$f->path = '';
		$folders = array_merge(array($f), iterator_to_array($brit));
		$tree = self::_buildDirectoryStructure($folders, '');
	
		$f->children = $tree;
		return array($f);
	}
	
	
	/**
	 * Returns the subfolders of the given parent
	 * @param String $parent The path of the parent folder
	 * @return Array An array of Folder VO's
	 */
	public static function getFolders($parent = null) {
	
		if (!$parent) {
			return self::_getBaseFolders();
		}
		$parent = self::validateFolder($parent);
	
		$path = self::getFullPath($parent);
	
		$contents = scandir($path);
	
		$folders = array();
		foreach ($contents as $item) {
			if ($item != '.' && $item != '..' && is_dir($path . $item)) {
				$f = new Folder();
				$f->path = $parent . $item;
				$f->label = $item;
				$f->haschildren = count(glob(BASEPATH . UPLOADFOLDER . $f->path . '/*', GLOB_ONLYDIR)) > 0;
				$folders[] = $f;
			}
		}
		return $folders;
	}
	
	
	/**
	 * Creates a new folder
	 * @param String $folder The name of the new folder
	 * @param String $parent The path of the parent folder
	 * @throws FilesException
	 * @return Array The subfolders of $parent
	 */
	public static function setFolder($folder, $parent) {
		$folder = filter_var($folder, FILTER_SANITIZE_STRING);
		$parent = self::validateFolder($parent);
	
		// Sanitize input
		if (strpos($folder, '..') || preg_match('/[^A-z0-9_\-]/', $folder)) {
			throw new FilesException('', FilesException::INVALID_FOLDER_NAME);
		}
	
		$fp = self::getFullPath($parent);
	
		// We're allowed to create, now check if file exists
		if (is_dir($fp . $folder)) {
			throw new FilesException('', FilesException::FOLDER_ALREADY_EXISTS);
		}
	
		$result = @mkdir($fp . $folder);
	
		if ($result === false) {
			throw new FilesException('', FilesException::CANNOT_CREATE_DIR);
		}
	
		return self::getFolders($parent);
	}
	
	/**
	 *
	 * @param unknown_type $elements
	 * @param unknown_type $parent
	 */
	private static function _buildDirectoryStructure($elements, $parent = '') {
		$branch = array();
	
		foreach ($elements as $element) {
			if ($element->path == $parent . $element->label) {
				$children = self::_buildDirectoryStructure($elements, $parent . $element->label . '/');
				if ($children) {
					$element->children = $children;
				}
				$branch[] = $element;
			}
		}
	
		return $branch;
	}
	
	
	
	/**
	 * Gets the 'entrypoints' for the currently logged in administrator
	 * @todo Fetch real entry points
	 */
	private static function _getBaseFolders() {
		$f = new Folder();
		$f->label = 'Files';
		$f->path = '/';
		$f->haschildren = true;
		$f->isroot = true;
		return array($f);
	}
}