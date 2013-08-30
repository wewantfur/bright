<?php
namespace bright\core\frontend;

use bright\core\Utils;

use bright\core\content\Pages;

use bright\core\model\Model;

/**
 * Handles the conversion of urls to views
 * You can add custom routes with the addRoute method.
 * You should do that in your constants file, since that
 * is initialized before this class
 * @author Ids
 *
 */
class Router {
	
	const SPECIAL_403 = 403;
	const SPECIAL_404 = 404;
	
	/**
	 * Adds a custom route. These routes take precedence over the database
	 * @param string $path The path to trigger on
	 * @param string $view The name of the view class
	 * @param mixed $data Optional data to pass to the view
	 */
	public function addRoute($path, $view, $data = null) {
		Model::getInstance() -> routes[$path] = array($view, $data);
	}
	
	/**
	 * Searches the routes and the database for the given path
	 * @todo implement database search;
	 * @param unknown_type $path
	 */
	public function getRoute($path) {
		Utils::log("Requesting $path");
		// Remove start & trailing slashes
		if(Utils::endsWith($path, '/'))
			$path = substr($path, 0, -1);
		
		if(Utils::startsWith($path, '/'))
			$path = substr($path, 1);
		$view = null;
		if(is_array(Model::getInstance() -> routes)) {
			if(array_key_exists($path, Model::getInstance() -> routes)) {
				$viewname = PACKAGE . Model::getInstance() -> routes[$path][0];
				try {
					$view = new $viewname(Model::getInstance() -> routes[$path][1]);
				} catch(Exception $e) {
					/*Swallow it*/
				}
			}
		}
		if($view == null) {
			// Find in database
			$pages = new Pages();
			if($path == '') {
				// easy, homepage
				$data = $pages -> getHomepage();
				if($data) {
					$viewname = '\\' . PACKAGE . 'views\\'. ucfirst($data -> template) . 'View';
					$view = new $viewname($data);
				}
			}
		}
		if($view)
			$view -> render();
	}
	
	public function getSpecial($status) {
		
	}
	
	public function init() {
		$status = 200;
		if(isset($_SERVER['REDIRECT_STATUS'])) {
			// no need to check the url, serve a special page
			switch($_SERVER['REDIRECT_STATUS']) {
				case self::SPECIAL_403:
				case self::SPECIAL_404:
					$this -> getSpecial($_SERVER['REDIRECT_STATUS']);
					return;
			}
		}
		
		// Set the default language
		$this -> _setLanguage();
		
		if(strpos($_SERVER['REQUEST_URI'], '/index.php') === 0 && isset($_GET['pid'])) {
			$pages = new Pages();
			$path = $pages -> getPath($pid);
			if($path) {
				// IF path is valid, redirect...
				header('Location: ' . (USEPREFIX) ? BASEURL . $_SESSION[BSP]['language'] . '/' . $path : BASEURL . $path);
				exit;
			} else {
				// Else: 404
				$this -> serveSpecial(self::$SPECIAL_404);
				return;
			}
		}
		
		// Normal request
		if(isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/index.php') !== 0) {
			unset($_GET['path']);
			if(isset($_SERVER['REDIRECT_URL'])) {
				// easy
				$uri = $_SERVER['REDIRECT_URL'];
				$nodes = $this -> getRoute($uri); 
			}
			$uri = $_SERVER['REQUEST_URI'];
		} else {
// 			// Just serve the homepage
			$nodes = $this -> getRoute('');
		}
// 		$bright_is404 = ($bright_aTreenodes && is_numeric($bright_aTreenodes[0]) && (int) $bright_aTreenodes[0] == 404);
// 		// Path not found
// 		if($bright_is404){
// 			$this -> serveSpecial(Serve::$SPECIAL_404, __LINE__);
// 			return;
// 		}
		
// 		$this -> servepage($bright_aTreenodes);
	}
	
	private function _setLanguage() {
		// Find out language
		$langs = explode(',', AVAILABLELANG);
		$bright_lang = $langs[0];
		if(!USEPREFIX) {
			if(USETLD) {
				$bright_tlda = explode('.', $_SERVER['HTTP_HOST']);
				$bright_tld = array_pop($bright_tlda);
				$bright_preferred = $bright_lang;
				// TLD Base language
				switch($bright_tld) {
					case 'uk':
					case 'com':
						$bright_preferred = 'en';
						break;
					case 'at':
						$bright_preferred = 'de';
						break;
					default:
						$bright_preferred = $bright_tld;
				}
				// Check if the preferred language is available, otherwise, fallback to default language
				if(strpos(AVAILABLELANG, $bright_preferred) !== false) {
					$_SESSION[BSP]['language'] = $bright_preferred;
				} else {
					$_SESSION[BSP]['language'] = $bright_lang;
				}
			} else {
				if(!isset($_SESSION[BSP]['language'])) {

					$_SESSION[BSP]['language'] = $bright_lang;
				}
			}

		} else {
			if(isset($_COOKIE['language'])) {
				$_SESSION[BSP]['language'] = $_COOKIE['language'];
			}
			if(!isset($_SESSION[BSP]['language'])) {
				if(USEHEADER === true && isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {

					$x = explode(',', $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
					foreach ($x as $val) {
						#check for q-value and create associative array. No q-value means 1 by rule
						if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches)) {
							$lang[$matches[1]] = (float)$matches[2];
						} else {
							$lang[$val] = 1.0;
						}
					}
					#return default language (highest q-value)
					$qval = 0.0;
					$deflang = $bright_lang;
					foreach ($lang as $key => $value) {
						if(in_array($key, $langs)) {
							if ($value > $qval) {
								$qval = (float)$value;
								$deflang = $key;
							}
						}
					}
					$_SESSION[BSP]['language'] = $deflang;
				}
				if(!isset($_SESSION[BSP]['language']))
					$_SESSION[BSP]['language'] = $bright_lang;

				setcookie('language', $_SESSION[BSP]['language'], strtotime('+1 year'), '/');
			}
		}
	}
}