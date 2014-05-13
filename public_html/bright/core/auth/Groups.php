<?php
namespace bright\core\auth;

use bright\core\exceptions\AuthException;
use bright\core\model\Model;
use bright\core\Utils;

class Groups {
	
	/**
	 * Gets a group by it's GID
	 * @param int $GID The GID of the group
	 * @return BEGroup The group
	 */
	public static function GetGroup($GID) {

		$query = "SELECT bg.*, fm.path as fm, pm.lft as pm FROM be_groups bg
                    LEFT JOIN file_mountpoints fm ON fm.GID=bg.GID
                    LEFT JOIN pages_mountpoints pm ON pm.GID=bg.GID
                    WHERE bg.GID=?";

        $result = Model::GetInstance() -> getRows($query, array($GID), '\bright\core\model\vo\BEGroup');
		$group = null;

        if(!$result)
            return null;

        $group = Utils::stripVO($result[0]);

        foreach($result as $row) {
            if($row -> pm) {
                $group -> page_mountpoints[] = (int)$row -> pm;
            }
            if($row -> fm) {
                $group -> file_mountpoints[] = $row -> fm;
            }
        }
	
		return $group;
	
	}
	
	/**
	 * Gets all backend usergroups
	 */
	public static function GetGroups() {
		$query = "SELECT * FROM be_groups";
		$result = Model::GetInstance() -> getRows($query, array(), '\bright\core\model\vo\BEGroup');
		return $result;
	
	}
	
	/**
	 * Creates or updates a group
	 * @param BEGroup $group The group to create / update
	 * @throws AuthException
	 */
	public static function setGroup($group) {
		if(!Authorization::UserIsInGroup(Authorization::GR_WEBMASTER))
			throw new AuthException(Authorization::GR_WEBMASTER, AuthException::NOT_IN_GROUP);
	}
}