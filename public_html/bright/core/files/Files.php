<?php
namespace bright\core\files;

/**
 * Handles all the filesystem activities (creating files & folders)
 * @author Ids
 *
 */
use bright\core\model\vo\File;

use bright\core\model\vo\Folder;

use bright\core\Utils;

use bright\core\Exception;

class Files {
	
	public function deleteFile($file, $parent) {
		
	}
	
	public function deleteFolder($folder, $parent) {
		
	}
	
	public function getFiles($parent) {
		$parent = filter_var($parent, FILTER_SANITIZE_STRING);
		if($parent === false || $parent == null)
			throw new Exception($msg, Exception::FOLDER_NOT_FOUND);
			
		if(strpos($parent, '..'))
			throw Exception('', Exception::FOLDER_NOT_FOUND);
		
		if($parent == '/')
			$parent = '';
		
		$path = BASEPATH . UPLOADFOLDER . $parent;
		if(!Utils::endsWith($path, '/'))	$path .= '/';
		if(!Utils::endsWith($parent, '/'))	$parent .= '/';
		
		if(is_dir($path)) {
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
	}
	
	public function getFolders($parent = null) {
		
		if($parent) {
			$parent = filter_var($parent, FILTER_SANITIZE_STRING);
			if($parent == '/')
				$parent = '';
			
			if(strpos($parent, '..'))
				throw Exception('', Exception::FOLDER_NOT_FOUND);
		} else {
			return $this -> _getBaseFolders();
		}
		
		$path = BASEPATH . UPLOADFOLDER . $parent;
		
		if(!Utils::endsWith($path, '/'))	$path .= '/';
		if(!Utils::endsWith($parent, '/'))	$parent .= '/';
		
		if(is_dir($path)) {
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
		
	}
	
	public function moveFile($file, $oldparent, $newparent) {
		
	}
	
	public function setFile($file, $parent) {
		
	}
	
	public function setFolder($folder, $parent) {
		
	}
	
	/**
	 * Gets the 'entrypoints' for the currently logged in administrator
	 */
	private function _getBaseFolders() {
		$f = new Folder();
		$f -> label = 'Files';
		$f -> path = '/';
		$f -> hasChildren = true;
		$f -> isroot = true;
		return array($f);
	}
}