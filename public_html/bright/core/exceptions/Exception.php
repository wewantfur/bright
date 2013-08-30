<?php
namespace bright\core\exceptions;

class Exception extends \Exception {
	const NO_BEUSER_AUTH = 1001;

	const DB_ERROR = 2001;
	const INCORRECT_PARAM_INT = 	2002;
	const INCORRECT_PARAM_STRING = 	2003;
	const INCORRECT_PARAM_DOUBLE = 	2004;
	const INCORRECT_PARAM_BOOL = 	2005;
	const INCORRECT_PARAM_EMAIL = 	2006;
	const INCORRECT_PARAM_ARRAY = 	2007;
	const INCORRECT_PARAM_OBJECT = 	2008;
	const INCORRECT_PARAM_ARRAYVAL =2009;
	const INCORRECT_PARAM_LENGTH = 2010;

	const FOLDER_NOT_FOUND = 4001;
	const FILE_NOT_FOUND = 4002;
	
	const DELETE_PAGE_NOT_ALLOWED = 5001;
	const REMOVE_PAGE_NOT_ALLOWED = 5002;
	const PAGE_STILL_IN_TREE = 5003;
	const BACKUP_NOT_FOUND = 5004;
	const NOT_ENOUGH_DATES = 5005;
	const TOO_MANY_DATES = 5006;

	const MISSING_PERMISSION_USER = 8001;
	const USER_DUPLICATE_EMAIL = 8002;

	public function __construct($msg, $code = 0) {

		parent::__construct($msg, $code);
	}
	
	public static function ehandler($e) {
		echo '<pre>';print_r($e);echo'</pre>';
	}
}