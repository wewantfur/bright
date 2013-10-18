<?php 
namespace bright\core\exceptions;

use bright\core\exceptions\Exception;


class FilesException extends Exception {

	const FOLDER_NOT_FOUND = 4001;
	const FILE_NOT_FOUND = 4002;
	const CANNOT_DELETE_FILE = 4003;
	const NOT_A_FILE = 4004;
	const INVALID_FOLDER_NAME = 4005;
	const INVALID_FILE_NAME = 4006;
	const FOLDER_ALREADY_EXISTS = 4007;
	const FILE_ALREADY_EXISTS = 4008;
	const CANNOT_CREATE_DIR = 4009;
	const CANNOT_CREATE_FILE = 4010;
	const CANNOT_MOVE_FILE = 4011;
	const CANNOT_DELETE_FOLDER = 4012;
}