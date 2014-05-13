<?php

namespace bright\core\auth;

use bright\core\exceptions\Exception;
use bright\core\content\Pages;
use bright\core\utils\Logger;
use bright\core\utils\PasswordUtils;
use bright\core\model\vo\BEUser;
use bright\core\model\vo\BEGroup;
use bright\core\Utils;
use bright\core\model\Model;
use bright\core\exceptions\AuthException;

class Authorization {

    /**
     * The group Id of SuperUsers
     * @var int
     */
    const GR_SU = 1;
    const GR_WEBMASTER = 2;
    const GR_SITEMANAGER = 3;
    const GR_FILEMANAGER = 4;
    const GR_EVENTMANAGER = 5;
    const GR_MAPSMANAGER = 6;
    const GR_MAILINGMANAGER = 7;
    const GR_USERMANAGER = 8;
    const GR_ELEMENTMANAGER = 9;

    private static $_beuser = null;

    public static function AddMountPoint($lft) {
        $beUser = self::GetBEUser();
        $GID = $beUser -> default_GID;
        $mountPoints = Model::GetInstance() -> getRowsAsArray("SELECT lft, GID FROM pages_mountpoints WHERE GID=?", $GID);

        if(!$mountPoints)
            $mountPoints = [[1, (int)$GID]];

        $mountPoints[] = ['?', (int)$GID];

        array_walk($mountPoints, function(&$item) {
            $item = "({$item[0]}, {$item[1]})";
        });

        $sql = "INSERT IGNORE INTO pages_mountpoints (lft, GID)
                VALUES " . implode(",\r\n", $mountPoints);
Logger::log($sql);
        Model::GetInstance() -> updateRow($sql, [$lft]);

        self::UpdateBEUser();
    }

    /**
     * Authenticates a backend user
     * @param string $email
     * @param string $pass
     * @throws AuthException
     */
    public static function AuthBE($email, $pass) {
        try {
            // Log out current user
            /*self::GetBEUser();*/
            self::LogoutBE();
        } catch (AuthException $e) {/* Swallow it */
            Logger::log(__FUNCTION__ ,$e -> getMessage());
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        if ($email === false || $email === null) {
            throw new AuthException('NOT_AN_EMAILADDRESS', AuthException::NOT_AN_EMAILADDRESS);
        }

        if ($pass === false || $pass === null) {
            throw new AuthException('INVALID_PASSWORD', AuthException::INVALID_PASSWORD);
        }

        // First select the hashed password from the db
        $hash = Model::GetInstance()->getField("SELECT bu.password FROM be_users bu WHERE bu.email = ?", array($email));
        
        if (!$hash) {
            throw new AuthException('WRONG_CREDENTIALS', AuthException::UNKNOWN_USER);
        }
        
        if (!PasswordUtils::Validate_password($pass, $hash)) {
            throw new AuthException('WRONG_CREDENTIALS', AuthException::INVALID_PASSWORD);
        }


        $query = "SELECT bu.*, bg.GID, bg.name as groupname, pm.lft as page_mountpoint, fm.path as file_mountpoint
		FROM be_users bu
		LEFT JOIN be_usergroups bug ON bu.UID = bug.UID
		LEFT JOIN be_groups bg ON bug.GID = bg.GID
		LEFT JOIN file_mountpoints fm ON fm.GID = bg.GID
		LEFT JOIN pages_mountpoints pm ON pm.GID = bg.GID
		WHERE bu.email = ?";


        $result = Model::GetInstance()->getRows($query, array($email), '\bright\core\model\vo\BEUser');

        if (!$result) {
            throw new AuthException('WRONG_CREDENTIALS', AuthException::WRONG_CREDENTIALS);
        }

        $beUser = self::_BuildUserFromResult($result);
        Logger::clear();
        Logger::log($beUser);

        Model::GetInstance()->updateRow("UPDATE be_users SET lastlogin=NOW() WHERE UID=?", array($beUser->UID));
        $beUser->lastlogin = date(\DateTime::W3C);
        
        $_SESSION['bright']['be_user'] = serialize($beUser);
		self::UpdateBEUser();
		        
        return $beUser;
    }

    /**
     * Logs out the currently logged in backend user
     */
    public static function LogoutBE() {
        self::$_beuser = null;
        unset($_SESSION['bright']['be_user']);
    }

    /**
     * @todo Implement
     * @param \bright\core\model\vo\BEUser $user
     */
    public static function SetBEUser(BEUser $user) {
        $hash = PasswordUtils::create_hash($user->password);
    }

    /**
     * Gets the currently logged in backend user
     * @throws AuthException
     * @return \bright\core\model\vo\BEUser
     */
    public static function GetBEUser() {
        if (!isset($_SESSION['bright']['be_user'])) {
            throw new AuthException('NO BE USER AUTH', AuthException::NO_USER);
        }
        if (!self::$_beuser)
            self::$_beuser = unserialize($_SESSION['bright']['be_user']);

        return self::$_beuser;
    }

    /**
     * Checks if the current logged in user belongs to the given group
     * @param int $group
     * @throws Exception
     * @return boolean
     */
    public static function UserIsInGroup($group) {
        $group = filter_var($group, FILTER_VALIDATE_INT);
        if ($group === false || $group === null)
            throw new Exception('', Exception::INCORRECT_PARAM_INT);

        $bu = self::GetBEUser();

        foreach ($bu->groups as $bugroup) {
            if ($bugroup->GID == Authorization::GR_SU)
                return true;
            if ($bugroup->GID == $group)
                return true;
        }
        return false;
    }
    
    /**
     * Updates the user in the session with the data from the database
     * @return \bright\core\model\vo\BEUser
     */
    public static function UpdateBEUser() {
    	$usr = self::GetBEUser();
    	
    	$query = "SELECT bu.*, bg.GID, bg.name as groupname, pm.lft as page_mountpoint, fm.path as file_mountpoint
    	FROM be_users bu
    	LEFT JOIN be_usergroups bug ON bu.UID = bug.UID
    	LEFT JOIN be_groups bg ON bug.GID = bg.GID
    	LEFT JOIN file_mountpoints fm ON fm.GID = bg.GID
    	LEFT JOIN pages_mountpoints pm ON pm.GID = bg.GID
    	WHERE bu.UID = ?";
    	
    	
    	$result = Model::GetInstance()->getRows($query, array($usr -> UID), '\bright\core\model\vo\BEUser');
    	
    	$beUser = Utils::StripVO($result[0]);
    	
    	foreach ($result as $row) {
    		if ($row->preferences != null) {
    			$beUser->preferences = json_decode($row->preferences);
    		}
    		if ($row->GID != null) {
    			$g = new BEGroup();
    			$g->GID = (int) $row->GID;
    			$g->name = $row->groupname;
    			$beUser->groups[] = $g;
    	
    			if ($row->GID == Authorization::GR_SU) {
    				$beUser->page_mountpoints[] = Pages::getBERoot();
    			}
    		}
    		if ($row->file_mountpoint != null) {
    			$beUser->file_mountpoints[] = $row->file_mountpoint;
    		}
    		if ($row->page_mountpoint != null) {
    			$beUser->page_mountpoints[] = (int) $row->page_mountpoint;
    		}
    	}
    	
    	$_SESSION['bright']['be_user'] = serialize($beUser);

    	return $beUser;
    }

    /**
     * Checks if there is a backend user authenticated
     * @return boolean
     */
    public static function IsBEAuth() {
        return isset($_SESSION['bright']['be_user']);
    }

    /**
     * @param array $result
     * @return BEUser
     */
    private static function _BuildUserFromResult($result) {
        $beUser = Utils::StripVO($result[0]);
        array_walk($result, function($item) use (&$beUser) {
            if($item -> GID != null) {
                $beUser -> groups[] = Groups::GetGroup($item -> GID);
            }
        });
        return $beUser;
    }
}
