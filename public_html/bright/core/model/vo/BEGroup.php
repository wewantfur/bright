<?php
namespace bright\core\model\vo;

class BEGroup {
	
	public $GID;
	public $name;
	public $file_mountpoints = array();
	public $page_mountpoints = array();
	
	function __construct() {
		$this -> GID = (int) $this -> GID;
	}
	
	public function __toString() {
		return $this -> name;
	}
}
