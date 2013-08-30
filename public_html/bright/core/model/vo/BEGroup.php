<?php
namespace bright\core\model\vo;

class BEGroup {
	
	public $GID;
	public $name;
	
	function __construct() {
		$this -> GID = (int) $this -> GID;
	}
}