<?php
namespace bright\core\plugins;

use bright\core\utils\Logger;

use bright\core\model\Model;

use bright\core\exceptions\Exception;

use bright\core\exceptions\ContentException;

abstract class AbstractPlugin {
	
	public final function store($id, $data) {
		$fieldNames = $this -> _checkFieldNames($this -> getFieldNames());
		$pluginName = $this -> _checkPluginName($this -> getPluginName());
		
		$fieldSql = implode('`, `', $fieldNames);
		// Add correct number of questionmarks. Add 1 extra for the fieldId
		$qMarks = str_repeat('?,', count($fieldNames)) . '?';
		
		// Create the update SQL part
		$updateSql = array_map(function($item) {
			return "`$item` = VALUES(`$item`)";
		}, $fieldNames);
		$sql = "INSERT INTO plugin_{$pluginName} (fieldId, `{$fieldSql}`) 
										VALUES ({$qMarks}) 
										ON DUPLICATE KEY UPDATE" . implode(",\r\n", $updateSql);
		
		$sqldata = [$id];
		foreach($fieldNames as $fieldName) {
			$sqldata[] = $data -> $fieldName;
		}
		Logger::log($sql);
		$result = Model::getInstance() -> updateRow($sql, $sqldata);
		return $result;
	}
	
	/**
	 * Gets the name of the plugin
	 * @return string The name of the plugin and database table name
	 */
	abstract protected function getPluginName();
	
	/**
	 * Gets all the fields this plugin contains
	 * @return array An array of fieldnames
	 */
	abstract protected function getFieldNames();
	
	/**
	 * Does optional normalization to the data before sending it to the client
	 * For example, the string plugin gets stored as {"string": "The value"},
	 * in the normalize method, it would alter that to return only "The value" to the client
	 * @return mixed
	 */
	abstract public function normalize($data);
	
	private function _checkPluginName($pluginname) {
		$tn = filter_var($pluginname, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[A-z0-9]*$/']]);
		if(!$tn)
			throw new ContentException('ContentException::INVALID_PLUGIN_NAME', ContentException::INVALID_PLUGIN_NAME);
			
		return $tn;
	}
	
	private function _checkFieldNames($fieldNames) {
		if(!is_array($fieldNames))
			throw new Exception(Exception::INCORRECT_PARAM_ARRAY, Exception::INCORRECT_PARAM_ARRAY);
		
		foreach($fieldNames as &$field) {
			$field = filter_var($field, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[A-z0-9]*$/']]);
			if(!$field)
				throw new ContentException('ContentException::INVALID_PLUGIN_FIELD_NAME', ContentException::INVALID_PLUGIN_FIELD_NAME);
		}
		
		return $fieldNames;
	}
} 