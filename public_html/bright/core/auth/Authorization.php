<?php
namespace bright\core\auth;

use bright\core\exceptions\Exception;

use bright\core\content\Pages;
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

	/**
	 * Authenticates a backend user
	 * @param string $email
	 * @param string $pass
	 * @throws AuthException
	 */
	public static function authBE($email, $pass) {
		try {
			// Log out current user
			self::getBEUser();
			self::logoutBE();
		} catch(AuthException $e) {/*Swallow it*/
		}

		$email = filter_var($email, FILTER_VALIDATE_EMAIL);
		$pass = filter_var($pass, FILTER_SANITIZE_STRING);

		if($email === false || $email === null)
			throw new AuthException('NOT_AN_EMAILADDRESS', AuthException::NOT_AN_EMAILADDRESS);

		if($pass === false || $pass === null)
			throw new AuthException('INVALID_PASSWORD', AuthException::INVALID_PASSWORD);

		// First select the hashed password from the db
		$hash = Model::getInstance() -> getField("SELECT bu.password FROM be_users bu WHERE bu.email = ?", array($email));
		if(!$hash) {
			throw new AuthException('WRONG_CREDENTIALS', AuthException::UNKNOWN_USER);
				
		}
		if(!PasswordUtils::validate_password($pass, $hash))
			throw new AuthException('WRONG_CREDENTIALS', AuthException::INVALID_PASSWORD);
			

		$query = "SELECT bu.*, bg.GID, bg.name as groupname, pm.pageId as page_mountpoint, fm.path as file_mountpoint
		FROM be_users bu
		LEFT JOIN be_usergroups bug ON bu.UID = bug.UID
		LEFT JOIN be_groups bg ON bug.GID = bg.GID
		LEFT JOIN file_mountpoints fm ON fm.GID = bg.GID
		LEFT JOIN pages_mountpoints pm ON pm.GID = bg.GID
		WHERE bu.email = ?";


		$result = Model::getInstance() -> getRows($query, array($email), '\bright\core\model\vo\BEUser');

		if(!$result)
			throw new AuthException('WRONG_CREDENTIALS', AuthException::WRONG_CREDENTIALS);

		$beuser = Utils::stripVO($result[0]);
		
		Model::getInstance() -> updateRow("UPDATE be_users SET lastlogin=NOW() WHERE UID=?", array($beuser -> UID));
		$beuser -> lastlogin = date(\DateTime::W3C);

		foreach ($result as $row) {
			if($row -> settings != null) {
				$beuser -> settings = json_decode($row -> settings);
			}
			if($row -> GID != null) {
				$g = new BEGroup();
				$g -> GID = (int)$row -> GID;
				$g -> name = $row -> groupname;
				$beuser -> groups[] = $g;

				if($row -> GID == Authorization::GR_SU) {
					$beuser -> page_mountpoints[] = Pages::getBERoot();
				}
			}
			if($row -> file_mountpoint != null) {
				$beuser -> file_mountpoints[] = $row -> file_mountpoint;
			}
			if($row -> page_mountpoint != null) {
				$beuser -> page_mountpoints[] = (int)$row -> page_mountpoint;
			}
		}

		$_SESSION['bright']['be_user'] = serialize($beuser);

		return $beuser;
	}

	/**
	 * Logs out the currently logged in backend user
	 */
	public static function logoutBE() {
		self::$_beuser = null;
		unset($_SESSION['bright']['be_user']);
	}

	public static function setBEUser(BEUser $user) {
		$hash = PasswordUtils::create_hash($user -> password);

	}

	/////////////////////////////////////////////////
	///////////		STATIC METHODS		/////////////
	/////////////////////////////////////////////////


	/**
	 * Gets the currently logged in backend user
	 * @throws AuthException
	 * @return \bright\core\model\vo\BEUser
	 */
	public static function getBEUser() {
		if(!isset($_SESSION['bright']['be_user'])) {
			throw new AuthException('NO BE USER AUTH', AuthException::NO_USER);
		}
		if(!self::$_beuser)
			self::$_beuser = unserialize($_SESSION['bright']['be_user']); 

		return self::$_beuser;
	}
	
	/**
	 * Checks if the current logged in user belongs to the given group
	 * @param int $group
	 * @throws Exception
	 * @return boolean
	 */
	public static function inGroup($group) {
		$group = filter_var($group, FILTER_VALIDATE_INT);
		if($group === false || $group === null)
			throw new Exception('', Exception::INCORRECT_PARAM_INT);
		
		$bu = self::getBEUser();
		
		foreach($bu -> groups as $bugroup) {
			if($bugroup -> GID == Authorization::GR_SU) 
				return true;
			if($bugroup -> GID == $group) 
				return true;
		}
		return false;
	}

	/**
	 * Checks if there is a backend user authenticated
	 * @return boolean
	 */
	public static function isBEAuth() {
		return isset($_SESSION['bright']['be_user']);
	}

}
