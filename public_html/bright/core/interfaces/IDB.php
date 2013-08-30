<?php
namespace bright\core\interfaces;

interface IDB {
	public function getRow($query, $args = null, $type = '\StdClass');
	public function getRows($query, $args = null, $type = '\StdClass');
	
	public function getFields($query, $args);
	public function getField($query, $args);
	
	public function updateRow();
	
	public function close();

// 	public function getInstance();
}