<?php
namespace bright\core\config;

class Config {
	
	public final function getSettings() {
		return (object) array('languages' => explode(',', AVAILABLELANG));
	}
}