<?php
namespace bright\core\exceptions;

use bright\core\exceptions\Exception;

class ContentException extends Exception {
	const NO_TEMPLATE = 4100;
	const CANNOT_SAVE = 4101;
	const INVALID_PLUGIN_NAME = 4102;
	const INVALID_PLUGIN_FIELD_NAME = 4103;
}