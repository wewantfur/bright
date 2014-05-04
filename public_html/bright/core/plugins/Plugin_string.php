<?php
namespace bright\core\plugins;

use bright\core\interfaces\IPlugin;
use bright\core\plugins\AbstractPlugin;


class Plugin_string extends AbstractPlugin implements IPlugin {
	
	protected function getPluginName() {
		return 'string';
	}
	
	protected function getFieldNames() {
		return ['string'];
	}
	
	public function normalize($data) {
		if(!$data)
			return null;
		
		return $data -> string;
	}
	
	public function isValid($data, $fieldData) {
		return $data != null && isset($data -> string) && $data -> string != ''; 
	}
}