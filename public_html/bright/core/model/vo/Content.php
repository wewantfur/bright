<?php
namespace bright\core\model\vo;

use bright\core\interfaces\IContent;

abstract class Content extends VO implements IContent{
	
	function __construct() {
		$this -> contentId = (int) $this -> contentId;
        $this -> revisionId = (int) $this -> revisionId;
		$this -> templateId = (int) $this -> templateId;

        if(!$this -> content)
            $this -> content = new \stdClass();
	}
	
	public $contentId;
    public $revisionId;
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
}