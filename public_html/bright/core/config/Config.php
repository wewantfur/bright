<?php
namespace bright\core\config;

use bright\core\utils\Logger;

use bright\core\auth\Authorization;

use bright\core\model\Model;

class Config {
	
	public final function getSettings() {
		return (object) array(
				'sitename' => SITENAME,
				'languages' => explode(',', AVAILABLELANG));
	}
	
	public final function getPreferences() {
		$beuser = Authorization::getBEUser();
		return $beuser -> preferences;
		
	}
	
	/**
	 * Updates the preferences of the current logged in administrator
	 * @param object $data
	 * @return object The new preferences
	 */
	public final function setPreferences($data) {
		$beuser = Authorization::getBEUser();
		Logger::log($data);
		Model::getInstance() -> updateRow("UPDATE be_users SET preferences=? WHERE UID=?", [json_encode($data),$beuser -> UID]);
		Authorization::updateBEUser();
		
		return self::getPreferences();
		
	}
}