<?php
namespace bright\core\model\vo;

class Page extends Content {
	
	function __construct() {
		parent::__construct();
		
		$this -> pageId = (int) $this -> pageId;
		$this -> index = (int) $this -> index;
		$this -> parentId = (int) $this -> parentId;
		$this -> lft = (int) $this -> lft;
		$this -> rgt = (int) $this -> rgt;
	}
	
	public $pageId;
	
	public $publicationdate;
	public $expirationdate;
	public $alwayspublished;

	public $showinnavigation;
	public $index;

	public $parent;
	public $parentId;
	public $lft;
	public $rgt;
	
	public $isopen=false;
	
	public $locked = false;
	
	public $felogin;
}