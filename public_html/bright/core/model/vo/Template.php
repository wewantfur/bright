<?php
namespace bright\core\model\vo;
class Template {
	
	public $templateId;
	public $icon;
	public $type;
	public $parser = 1;
	public $enabled = 0;
	public $maxchildren = -1;
	public $allowedparents;
	public $allowedchildren;
	public $groups;
	
	public $label;
	public $displaylabel;
	
	function __construct() {
		$this -> type = (int)$this -> type;
		$this -> templateId = (int)$this -> templateId;
		$this -> maxchildren = (int)$this -> maxchildren;
	}
}