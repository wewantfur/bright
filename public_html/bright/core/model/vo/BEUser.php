<?php
namespace bright\core\model\vo;

class BEUser {
	
	public $UID;
	public $email;
	public $password;
	public $name;
	
	public $lastlogin;
	public $settings;
	
	public $default_GID;
	public $pages_GID;
	public $files_GID;
	public $events_GID;
	public $maps_GID;
	public $users_GID;
	public $elements_GID;
	
	public $groups = array();
	public $file_mountpoints = array();
	public $page_mountpoints = array();
	
	function __construct() {
		
		$this -> UID = (int) $this -> UID;
		$this -> password = '';
		$this -> default_GID = (int) $this -> default_GID;
		$this -> pages_GID = (int) $this -> pages_GID;
		$this -> files_GID = (int) $this -> files_GID;
		$this -> events_GID = (int) $this -> events_GID;
		$this -> maps_GID = (int) $this -> maps_GID;
		$this -> users_GID = (int) $this -> users_GID;
		$this -> elements_GID = (int) $this -> elements_GID;
	}
}