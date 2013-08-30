<?php
namespace bright\core\exceptions;

use bright\core\exceptions\Exception;

class AuthException extends Exception {
	const NO_USER = 1001;
	const PERMISSION_DENIED = 1002;
	const NOT_OWNER = 1003;
	const NOT_IN_GROUP = 1004;
	const WRONG_CREDENTIALS = 1005;
	const PASSWORD_MISMATCH = 1006;
	const NOT_AN_EMAILADDRESS = 1007;
	const INVALID_PASSWORD = 1008;
	const UNKNOWN_USER = 1009;
}