<?php
namespace bright\core\model\vo;

class Content extends VO{
	
	function __construct() {
		$this -> contentId = (int) $this -> contentId;
		$this -> templateId = (int) $this -> templateId;
	}
	
	public $contentId;
	public $label;
	
	public $creationdate;
	public $modificationdate;
	
	public $createdby;
	public $modifiedby;
	
	public $deleted = 0;
	public $enabled = true;
	
	public $templateId;
	public $template;
	public $icon;
	
	public $content;
	
	public $selected = false;
	
	function __toString() {
		return $this -> label;
	}
}