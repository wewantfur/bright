<?php
namespace bright\core\utils\iterators;

use bright\core\model\vo\Folder;


class BrightDirectoryIterator extends \FilterIterator implements \Iterator, \RecursiveIterator {
	private $_pos = 0;
	private $_folders;
	private $_path;
	private $_depth;
	private $_parent;
	
	public function __construct($path) {
		parent::__construct(new BrInnerIterator($path));
		$this -> _path = str_replace('//', '/', $path . '/');
	}
	
	public function accept() {
		return $this->getInnerIterator()->current() != '.'
				&& $this->getInnerIterator()->current() != '..'
				&& is_dir($this -> _path . $this->getInnerIterator()->current());
	}
	
	public function current() {
		$f = new Folder();
		$f -> label = $this->getInnerIterator()->current();
		$f -> path = str_replace(BASEPATH . UPLOADFOLDER, '', $this -> _path . $f -> label);
// 		$f -> haschildren = count(scandir($this -> _path . $f -> label)) > 2;
		$f -> haschildren = count(glob($this -> _path . $f -> label . '/*' , GLOB_ONLYDIR)) > 0;
		return $f;
	}
	
	public function hasChildren() {
		return count(scandir($this -> _path . $this->getInnerIterator()->current())) > 2;
	}
	public function getChildren() {
		return new BrightDirectoryIterator($this -> _path .  $this->getInnerIterator()->current());
	}
	
}

class BrInnerIterator implements \Iterator {
	public function __construct($path) {
		$this -> _pos = 0;
		$this -> _path = $path . '/';
		$this -> _folder = scandir($path);
	}

	public function accept() {
		return $this -> _folder[$this ->_pos] != '.'
		&& $this -> _folder[$this ->_pos] != '..'
		&& is_dir($this -> _path . $this -> _folder[$this ->_pos]);
	}

	public function current() {
		return $this -> _folder[$this -> _pos];
	}

	public function key() {
		return $this -> _pos;
	}

	public function next() {
		++$this -> _pos;
	}

	public function rewind() {
		$this -> _pos = 0;
	}

	public function valid() {
		return isset($this -> _folder[$this ->_pos]);
	}
}