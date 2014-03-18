<?php
namespace bright\core\model;

use bright\core\utils\Logger;

use bright\core\Utils;
use bright\core\connections\DBPDO;

use bright\core\Exception;

class Model {
	
	/**
	 * @staticvar Connection The instance of this class
	 */
	static private $instance;
	
	/**
	 * @var resource The mysql-database connection
	 */
	public $db;
	
	/**
	 * @var array An array of additional routes
	 */
	public $routes;
	
	/**
	 * Constructor, opens the mysqldb connection
	 */
	private function __construct(){
		$mode = 'PDO';
		switch($mode) {
			case 'PDO':
				$this -> db = new DBPDO();
				break;
			case 'MYSQLI':
				break;
			case 'MYSQL':
				break;
		}
		
	}
	
	/**
	 * Gets a single instance of the connection class
	 * @static
	 * @return StdClass An instance of the connction class
	 */
	public static function getInstance(){
		if(!isset(self::$instance)){
			$object= __CLASS__;
			self::$instance = new $object;
		}
		return self::$instance;
	}
	
	public function getField($query, $args = null) {
		return $this -> db -> getField($query, $args);
	}
	
	public function getFields($query, $args = null) {
		return $this -> db -> getFields($query, $args);
	}
	
	public function getRows($query, $args = null, $type = '\StdClass') {
		return $this -> db -> getRows($query, $args, $type);
	}
	
	public function getRow($query, $args = null, $type = '\StdClass') {
		return $this -> db -> getRow($query, $args, $type);
	}
	
	/**
	 * Insert, update or delete a row
	 */
	public function updateRow() {		
		return call_user_func_array(array($this -> db, 'updateRow'), func_get_args());
	}
	
	/**
	 * Inserts or updates an object
	 * @param string $table The table to insert to
	 * @param mixed $object The object to insert
	 * @param string $identifier The identifier of the object
	 */
	public function updateObject($table, $object) {
		// Whitelist of tables
		$allowedtables = array('templates' => 'templateId', 'templatefields' => 'fieldId');
		if(!array_key_exists($table, $allowedtables))
			throw new \bright\core\exceptions\Exception('Exception::DB_INVALID_TABLE', \bright\core\exceptions\Exception::DB_INVALID_TABLE);
		
		$identifier = $allowedtables[$table];
		
		$objects = null;
		if(is_array($object)) {
			$objects = $object;
			$object = $object[0];
		} else {
			$objects = array($object);
		}
		$oprops = get_class_vars(get_class($object));
		$fields = array_keys($oprops);
		$qmarks = '(' . str_repeat('?,', count($fields)-1) . '?)';
		
		for($i = 0; $i < count($objects); $i++) {
			$values[] = $qmarks;
		}
		$values = implode(',', $values);
		$jfields = join('`, `', $fields);
		$sql = "INSERT INTO $table 
				(`$jfields`)
				VALUES $values
				ON DUPLICATE KEY UPDATE ";
		$varr = array();
		foreach($objects as $i => $insobject) {
			foreach($oprops as $key => $type) {
				$varr[] = $insobject -> $key;
				if($i == 0 && $key != $identifier) {
					$sql .= "`$key` = VALUES(`$key`),\r\n";
				}
			}
		}
		$sql .= "`$identifier` = LAST_INSERT_ID(`$identifier`)";
		
		$id = 0;
		try {
			$id = Model::getInstance() -> updateRow($sql, $varr);
		} catch(\Exception $e) {
			Logger::log($e-> getCode(), $e -> getMessage(), $sql);
			throw new \bright\core\exceptions\Exception('Exception::DB_ERROR', \bright\core\exceptions\Exception::DB_ERROR);
		}
		
		return $id;
	}

	/**
	 * Destructor
	 */
	function __destruct() {
		//Disconnect
		if(!$this -> db)
			return;
		$this -> db -> close();
	}
}