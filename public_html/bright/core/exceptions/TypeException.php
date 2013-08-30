<?php
namespace bright\core\exceptions;

use bright\core\exceptions\Exception;

class TypeException extends Exception {
	const TYPE_EXCEPTION = 9001;
	
	const TYPE_INT = 'int';
	const TYPE_STRING = 'string';
	const TYPE_OBJECT = 'object';
	const TYPE_BOOLEAN = 'bool';
	const TYPE_DOUBLE = 'double';
	
	function __construct($msg, $code, $previous, $expected, $got) {
		parent::__construct("Incorrect parameter type, expected $expected, got $got", $code, $previous);
	}
}