<?php
namespace bright\core\model\vo;

/**
 * Defines a field in a template
 * @author ids
 *
 */
class TemplateField {
	public $label;
	public $templateId;
	public $idx;
	public $displaylabel;
	public $fieldtype;
	public $data;
	
	function __construct($label = null, $displaylabel = null, $fieldtype = null, $data = null) {
		if($label)
			$this -> label = $label;
		
		if($displaylabel)
			$this -> displaylabel = $displaylabel;
		
		if($fieldtype)
			$this -> fieldtype = $fieldtype;
		
		if($data)
			$this -> data = $data;
	}
}