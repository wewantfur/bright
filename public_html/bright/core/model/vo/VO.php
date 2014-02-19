<?php
namespace bright\core\model\vo;

class VO {
	public function __toString() {
		return $this -> label;
	}
}