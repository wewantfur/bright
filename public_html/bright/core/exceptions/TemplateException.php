<?php
namespace bright\core\exceptions;

use bright\core\exceptions\Exception;

class TemplateException extends Exception {
	const TEMPLATE_NOT_FOUND = 3001;
	const CANNOT_SAVE_TEMPLATE = 3002;
	const TEMPLATE_IN_USE = 3003;
}