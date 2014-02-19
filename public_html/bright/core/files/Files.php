<?php

namespace bright\core\files;

/**
 * Handles all the filesystem activities (creating files & folders)
 * @author Ids
 *
 */
use bright\core\utils\iterators\BrightDirectoryIterator;
use bright\core\exceptions\AuthException;
use bright\core\auth\Authorization;
use bright\core\exceptions\FilesException;
use bright\core\exceptions\Exception;
use bright\core\utils\StringUtils;
use bright\core\model\vo\File;
use bright\core\model\vo\Folder;
use bright\core\Utils;
use bright\core\utils\iterators\SortedDirectoryIterator;

class Files extends FilesAbstract {

	/**
	 * Deletes a file
	 * @param String $file The name of file to delete
	 * @param String $parent The (relative) path to the parent folder of the file
	 * @throws FilesException
	 * @return Array The files in $parent
	 */
	public static function deleteFile($file, $parent) {

		$parent = self::validateFolder($parent);
		$file = self::validateFilename($file);

		$fp = self::getFullPath($parent);

		// We're allowed to delete, now check if file exists
		if (!is_file($fp . $file)) {
			throw new FilesException('', FilesException::FILE_NOT_FOUND);
		}

		// We're allowed to delete, now check if its not a dir
		if (is_dir($fp . $file)) {
			throw new FilesException('', FilesException::NOT_A_FILE);
		}

		$result = @unlink($fp . $file);

		if ($result === false) {
			throw new FilesException('', FilesException::CANNOT_DELETE_FILE);
		}

		return self::getFiles($parent);
	}

	public static function getAllFiles() {

	}

	/**
	 * Gets all the files in the given dir
	 * @param string $parent The parent folder
	 * @throws Exception
	 */
	public static function getFiles($parent = null) {
		$parent = self::validateFolder($parent);
		$path = self::getFullPath($parent);

		$contents = scandir($path);

		if (!StringUtils::endsWith($parent, '/')) {
			$parent .= '/';
		}

		$files = array();
		foreach ($contents as $item) {
			if (!is_dir($path . $item)) {
				$f = new File();
				$f->path = $parent . $item;
				$f->label = $item;
				$info = pathinfo($path . $item);
				$f->extension = $info['extension'];
				$f->size = filesize($path . $item);
				$files[] = $f;
			}
		}
		return $files;
	}


	/**
	 * Moves a file
	 * @param String $file The name of the file
	 * @param String $oldparent The path of the current folder
	 * @param String $newparent The path of the new folder
	 * @throws FilesException
	 * @return Array The contents of the old folder
	 */
	public static function moveFile($file, $oldparent, $newparent) {
		$file = self::validateFilename($file);

		$oldparent = self::validateFolder($oldparent);
		$newparent = self::validateFolder($newparent);

		$oldfp = self::getFullPath($oldparent);
		$newfp = self::getFullPath($newparent);

		if (!file_exists($oldfp . $file)) {
			throw new FilesException($file, FilesException::FILE_NOT_FOUND);
		}

		if (file_exists($newfp . $file)) {
			throw new FilesException('', FilesException::FILE_ALREADY_EXISTS);
		}

		$result = @rename($oldfp . $file, $newfp . $file);
		if ($result === false) {
			throw new FilesException('', FilesException::CANNOT_MOVE_FILE);
		}

		return self::getFiles($oldparent);
	}

	/**
	 * Alters a file
	 * @param unknown_type $file
	 * @param unknown_type $parent
	 * @throws Exception
	 */
	public static function setFile($file, $parent) {
		throw new Exception(__METHOD__, Exception::NOT_IMPLEMENTED);
	}


	/**
	 * Uploads a file
	 * @throws AuthException
	 */
	public static function upload() {
		// Are we allowed to upload?
		if (!Authorization::inGroup(Authorization::GR_FILEMANAGER)) {
			throw new AuthException(Authorization::GR_FILEMANAGER, AuthException::NOT_IN_GROUP);
		}

		$folder = self::validateFolder($_POST['folder']);

		$filename = self::sanitizeFilename($_FILES['files']['name'][0], $folder);

		$result = @move_uploaded_file($_FILES['files']['tmp_name'][0], self::getFullPath($folder) . $filename);
		return $result;
	}
	

}