<?php
namespace bright\core\model\vo;

class Folder extends VO {
	public $label;
	public $path;
	
	public $children;
	public $haschildren = true;
	
	public $isopen = false;
	public $isroot = false;
	
}