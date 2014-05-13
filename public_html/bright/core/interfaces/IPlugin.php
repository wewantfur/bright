<?php
namespace bright\core\interfaces;

interface IPlugin {
	function store($id, $data);

    function get();

	function normalize($data);
	
	function isValid($data, $fieldData);
}