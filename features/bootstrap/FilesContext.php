<?php

/**
 * Features context.
 */
use bright\core\files\Folders;

use bright\core\exceptions\FilesException;

use bright\core\files\Files;

use bright\core\model\vo\File;

use Behat\Behat\Context\BehatContext;

class FilesContext extends BehatContext
{

	private $result;

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters)
	{
		// Initialize your context here
		unset($_SESSION['bright']);
		$this -> result = null;
		/**
		 * Copy temp files structure for testing
		 */
		$this -> _rrmdir(BASEPATH . 'files');
		$this -> _rcopy(BASEPATH .'../test/files/', BASEPATH . 'files');
		
	}
	
	/**
	 * @Transform /^file (.*)$/
	 */
	public function castPathToFile($string) {
		$paths = explode('/', $string);
		array_splice($paths, 0,0, array('/'));
		$fname = array_pop($paths);
		$p = '';
		foreach($paths as $path) {
			$p .= $path;
			Folders::getFolders($p);
		}
		$files = Files::getFiles($p);
		foreach($files as $file) {
			if($file -> label == $fname) {
				return $file;
			}
		}
		
		throw new FilesException('', FilesException::FILE_NOT_FOUND);
	}
	
	/** 
	 * @Then /^the file "([^"]*)" should exist$/
	 */
	public function theFileShouldExist(File $file)
	{
		if(file_exists(BASEPATH . UPLOADFOLDER . $file -> path)) {
			return true;
		} else {
			throw new FilesException($file -> path, FilesException::FILE_NOT_FOUND);
		}
	}
	
	
	
	private function _rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") 
						$this->_rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	private function _rcopy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->_rcopy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
}