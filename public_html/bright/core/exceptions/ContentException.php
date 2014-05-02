<?php
namespace bright\core\exceptions;

use bright\core\exceptions\Exception;

class ContentException extends Exception {
	const NO_TEMPLATE = 4100;
	const CANNOT_SAVE = 4101;
}