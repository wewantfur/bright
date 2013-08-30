<?php
namespace bright\core\model;

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
	
// 	public function insertRow($query, $args) {
// 		return $this -> db -> insertRow($query, $args);
// 	}
	
	/**
	 * Inserts a row as prepared statement
	 * @param string $sql The query to prepare
	 * @param string $paramtypes A string of parameter types (length must match the number of params)
	 * @param mixed... $params One or more parameters
	 * @return int The id of the inserted row, null on failure
	 * @throws Exception::DB_ERROR
	 * @throws Exception::INCORRECT_PARAM_LENGTH
	 */
	public function insertPrepared() {
		
		$args = func_get_args();
		if(func_num_args() < 3)
			throw new Exception('This method requires at least 3 arguments', Exception::INCORRECT_PARAM_LENGTH);
		
		$stmt = $this -> _doPrepared($args);
		$id = $this -> db -> insert_id;
		$stmt->close();
		return $id;
	}
	
	public function insertRowNamed($sql, $obj) {
		$stmt = $this -> db -> prepare($sql);
		if($stmt === false) {
			$this -> _handleError($sql);
		}
		$stmt -> execute($obj);
		if($stmt -> errno != 0) {
			$this -> _handleError($sql);
		}
		return $stmt;
	}
	
	/**
	 * Insert, update or delete a row
	 */
	public function updateRow() {		
		return call_user_func_array(array($this -> db, 'updateRow'), func_get_args());
	}
	
	/**
	 * Performs a prepared statement
	 * @param array $args An array of arguments containing:
	 * 				string $sql The query to prepare
	 * 				string $paramtypes A string of parameter types (length must match the number of params)
	 * 				array... $params One or more parameters
	 * @return \mysqli_stmt The statement
	 */
	private function _doPrepared($args) {
		$sql = array_shift($args);
		$types = array_shift($args);
		$params = array_shift($args);
		$params = array_merge(array($types), $params);
		$stmt = $this -> db -> prepare($sql);
		if($stmt === false) {
			$this -> _handleError($sql);
		}
		
		call_user_func_array(array($stmt, 'bind_param'), Utils::makeValuesReferenced($params));
		$stmt -> execute();
		if($stmt -> errno != 0) {
			$this -> _handleError($sql);
		}
		return $stmt;
	}
	
	private function _handleError($query) {
		if(!LIVESERVER) {
			$msg = "An error occured:\r\n";
			$msg .= $this -> db -> error;
			$msg .= "\r\n\r\nQuery responsible:\r\n$query"; 
			throw new Exception($msg, Exception::DB_ERROR);
		}
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