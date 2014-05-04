<?php
namespace bright\core\interfaces;

interface IPlugin {
	public function store($id, $data);
	function normalize($data);
	
	function isValid($data, $fieldData);
}