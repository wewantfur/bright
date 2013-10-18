<?php
namespace bright\core\auth;

use bright\core\content\Pages;
use bright\core\utils\PasswordUtils;
use bright\core\model\vo\BEUser;
use bright\core\model\vo\BEGroup;
use bright\core\Utils;
use bright\core\model\Model;
use bright\core\exceptions\AuthException;


class Administrators {
	
	/**
	 * Gets all the names of the backend users
	 */
	public static function getAdministratorNames() {
		$query = "SELECT UID, name FROM be_users bu";
		$result = Model::getInstance() -> getRows($query, array(), '\bright\core\model\vo\BEUser');
		return $result;
	}
	
	/**
	 * Gets all backend users
	 */
	public static function getAdministrators() {
		$query = "SELECT bu.* FROM be_users bu";
// 					LEFT JOIN be_usergroups bug ON bu.UID = bug.UID 
// 					LEFT JOIN be_groups bg ON bug.GID = bg.GID
// 					LEFT JOIN file_mountpoints fm ON fm.GID = bug.GID
// 					LEFT JOIN pages_mountpoints pm ON pm.GID = bug.GID

		$result = Model::getInstance() -> getRows($query, array(), '\bright\core\model\vo\BEUser');
		foreach($result as &$admin) {
			$admin -> password = null;
		}
		return $result;
	}
	
	public static function getGroup($GID) {
		$query = "SELECT *, fm.path as fm, pm.pageId as pm FROM be_groups bg
				LEFT JOIN file_mountpoints fm ON fm.GID=bg.GID
				LEFT JOIN pages_mountpoints pm ON pm.GID=bg.GID
				WHERE bg.GID=?";
		$result = Model::getInstance() -> getRows($query, array($GID), '\bright\core\model\vo\BEGroup');
		$group = null;
		if($result) {
			$group = Utils::stripVO($result[0]);
			foreach($result as $row) {
				if($row -> pm) {
					$group -> page_mountpoints[] = (int)$row -> pm;
				}
				if($row -> fm) {
					$group -> file_mountpoints[] = $row -> fm;
				}
			}
		}
		
		return $group;
		
	}

	/**
	 * Gets all backend usergroups
	 */
	public static function getGroups() {
		$query = "SELECT * FROM be_groups";
		$result = Model::getInstance() -> getRows($query, array(), '\bright\core\model\vo\BEGroup');
		return $result;
		
	}
}