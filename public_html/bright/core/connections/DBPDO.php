<?php
namespace bright\core\connections;

use bright\core\utils\Logger;

use bright\core\exceptions\DatabaseException;

use bright\core\exceptions\Exception;

use bright\core\interfaces\IDB;

class DBPDO implements IDB {

	const MODE_ROW_ALL = 1;
	const MODE_ROW = 2;
	const MODE_FIELD_ALL = 3;
	const MODE_FIELD = 4;
	
	private $db;

	function __construct() {
		/* Connect to an ODBC database using driver invocation */
		$dsn = 'mysql:dbname='.DB_DATABASE.';host='. DB_HOST . ';port=' . DB_PORT;

		try {
			$this -> db = new \PDO($dsn, DB_USER, DB_PASSWORD, array(	\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
																		\PDO::ATTR_PERSISTENT => true));
			$this -> db -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this -> db -> setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		} catch (\PDOException $e) {
			Logger::log("Logging!", $e -> getMessage());
			throw new DatabaseException($e->getMessage(), DatabaseException::DB_ERROR);
		}
		
	}
	
	public function close() {
		$this -> db = null;
	}
	
	public function getField($query, $args = null) {
		return $this -> fetch($query, $args, null, self::MODE_FIELD);
	}
	
	public function getFields($query, $args = null) {
		return $this -> fetch($query, $args, null, self::MODE_FIELD_ALL);
	}
	
	public function getRow($query, $args = null, $type = '\StdClass') {
		return $this -> fetch($query, $args, $type, self::MODE_ROW);
	}
	
	public function getRows($query, $args = null, $type = '\StdClass') {
		return $this -> fetch($query, $args, $type, self::MODE_ROW_ALL);
	}
	
	public function deleteRow($query, $args = null) {
		if(!$args) {
			$stmt = $this -> db -> query($query);
		} else {
			$stmt = $this -> db -> prepare($query);
			if(!is_array($args))
				$args = array($args);
			$stmt -> execute($args);
		}
		return $stmt -> rowCount();
	}
	
	public function updateRow() {
// 		throw new Exception("UPDATE ROW NOT IMPLEMENTED");
		$na = func_num_args();
		$args = func_get_args();
		// Normal update query
		if($na == 1) {
			$this -> db -> query($args[0]);
		} else {
			$stmt = $this -> db -> prepare($args[0]);
			$stmt -> execute($args[1]);
		}
		
		return $this -> db -> lastInsertId();
	}
	
	private function fetch($query, $args = null, $type = '\StdClass', $mode) {
		$stmt = null;

		try {
			if(!$args) {
				
				$stmt = $this -> db -> query($query);
			} else {
				$stmt = $this -> db -> prepare($query);
			}
		
		} catch (\PDOException $e) {
			throw new DatabaseException($e->getMessage(), DatabaseException::DB_ERROR);
		}
		
		if($type == '\StdClass') {
			$stmt -> setFetchMode(\PDO::FETCH_OBJ);
		} else if($mode == self::MODE_ROW || $mode == self::MODE_ROW_ALL) {
			$stmt -> setFetchMode(\PDO::FETCH_CLASS, $type);
		}
		
		if($args) {
			if(!is_array($args))
				$args = array($args);
			$stmt -> execute($args);
		}
		
		switch($mode) {
			case self::MODE_ROW_ALL:
			case self::MODE_ROW:
				if($mode == self::MODE_ROW_ALL) {
					$result = $stmt -> fetchAll();
				} else {
					$result = $stmt -> fetch();
					
				}
				break;

			case self::MODE_FIELD_ALL:
			case self::MODE_FIELD:
				$stmt -> setFetchMode (\PDO::FETCH_COLUMN , 0);
				
				if($mode == self::MODE_FIELD_ALL) {
					$result = $stmt -> fetchAll();
				} else {
					$result = $stmt -> fetchColumn(0);
					
				}
				break;
		}
				
		$stmt -> closeCursor();
		return $result;
	}
}
