<?php
namespace bright\core\model\vo;

class Folder {
	public $label;
	public $path;
	
	public $children;
	public $haschildren = true;
	
	public $isopen = false;
	public $isroot = false;
	
}