<?php
namespace bright\core\auth;

use bright\core\exceptions\AuthException;

class Groups {
	
	/**
	 * Gets a group by it's GID
	 * @param int $GID The GID of the group
	 * @return BEGroup The group
	 */
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
	
	/**
	 * Creates or updates a group
	 * @param BEGroup $group The group to create / update
	 * @throws AuthException
	 */
	public static function setGroup($group) {
		if(!Authorization::inGroup(Authorization::GR_WEBMASTER))
			throw new AuthException(Authorization::GR_WEBMASTER, AuthException::NOT_IN_GROUP);
	}
}