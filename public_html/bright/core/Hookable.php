<?php
namespace bright\core;

abstract class Hookable {
	
	function __construct() {
		echo get_class($this);
	}
} 