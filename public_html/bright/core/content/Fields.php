<?php
namespace bright\core\content;

use bright\core\model\Model;

class Fields {
	
	public static function getFieldTypes() {
		$fields = Model::GetInstance() -> getRows("SELECT * FROM plugins ORDER BY core DESC, label ASC");
		return $fields;
	}
}