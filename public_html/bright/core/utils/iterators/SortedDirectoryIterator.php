<?php
namespace bright\core\utils\iterators;

class SortedDirectoryIterator extends \SplHeap {
	public function __construct(\Iterator $iterator) {
		foreach($iterator as $item) {
			$this->insert($item);
		}
	}
	public function compare($b, $a) {
		return strcmp($a -> path, $b -> path);
	}
}