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



	public static function getAllFiles() {

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
		if (!Authorization::UserIsInGroup(Authorization::GR_FILEMANAGER)) {
			throw new AuthException(Authorization::GR_FILEMANAGER, AuthException::NOT_IN_GROUP);
		}

		$folder = self::validateFolder($_POST['folder']);

		$filename = self::sanitizeFilename($_FILES['files']['name'][0], $folder);

		$result = @move_uploaded_file($_FILES['files']['tmp_name'][0], self::getFullPath($folder) . $filename);
		return $result;
	}
	

}