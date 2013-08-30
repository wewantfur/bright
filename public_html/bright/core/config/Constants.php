<?php
namespace bright\core\config;

/**
 * Holds all the important information about this project,
 * like database connection, url, directories, etc.
 * You can extend this class and add your custom fields (just define them protected / public)
 * NOTE: You can only use scalar values
 * @author Ids
 *
 */
class Constants {
	
	protected $BASEPATH;
	
	protected $DB_HOST;
	protected $DB_USER;
	protected $DB_PASSWORD;
	protected $DB_DATABASE;
	protected $DB_PORT = 3306;

	protected $USEFTPFORFOLDERS = false;
	protected $FTPSERVER;
	protected $FTPUSER;
	protected $FTPPASS;
	protected $FTPBASEPATH;

	protected $LIVESERVER = true;

	protected $BASEURL;

	protected $MAILINGFROM;
	protected $MAILINGBOUNCE;

	protected $SYSMAIL;

	protected $SMTP;
	protected $SMTPPORT = 25;

	protected $TRANSPORT;

	protected $UPLOADFOLDER = 'files/';
	protected $CMSFOLDER = 'bright/cms/';

	protected $PACKAGE = 'bright\site\\';
	
	protected $SITENAME;
	protected $AVAILABLELANG;
	protected $LOGO;

	protected $PHPTHUMBERRORIMAGE;

	/**
	 * When true, all paths have the language prefixed (/nl/home/; /en/home/), when false, it uses the TLD to determine the language (domain.nl / domain.de)
	 * @var boolean
	 * @since 1.2
	 */
	protected $USEPREFIX = true;

	/**
	 * When true, the tld is used to determine the language. When false, you have to manage it yourself
	 * @var boolean
	 * @since 1.10
	 */
	protected $USETLD = true;
	
	/**
	 * When true, the header of the browser is used to determine the language. When false, you have to manage it yourself
	 * @var boolean
	 * @since 1.10
	 */
	protected $USEHEADER = true;

	/**
	 * Defines whether or not to generate a sitemap
	 * @var boolean
	 *
	 */
	protected $GENERATESITEMAP = true;

	/**
	 * Used by phpThumb & upload script
	 * @var string
	 */
	protected $IMAGE_MODES = array();

	/**
	 * Used by phpThumb & upload script
	 * @var string
	 */
	protected $IMAGEMAGICK_PATH = null;

	/**
	 * @var array An array of custom constants
	 */
	protected $CUSTOM = array();

	/**
	 * 
	 * @var boolean When true, deprecation messages are shown (only when LIVESERVER is FALSE)
	 */
	protected $SHOWDEPRECATION = true;
	
	/**
	 * @var array An array of custom routes, where the key is the route and the value is the view
	 */
	protected $ROUTES = null;

	/**
	 * @var string When running multiple instances of bright on the same domain, set a prefix for every instance;
	 */
	protected $SESSION_PREFIX = 'bright';
	
	function __construct() {
		$fa = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
		$fa = array_splice($fa, 0, count($fa)-3);
		$this -> BASEPATH = implode(DIRECTORY_SEPARATOR, $fa) . DIRECTORY_SEPARATOR;
		$this -> _fixSlashes(array('UPLOADFOLDER', 'CMSFOLDER', 'BASEURL'));
		
		$vars = get_class_vars(get_class($this));
		foreach($vars as $var => $val) {
			switch($var) {
				case 'ROUTES': 
				case 'IMAGE_MODES': 
					define($var, json_encode($this->$var));
					break;
				case 'SESSION_PREFIX':
					define($var, $this->$var);
					define('BSP', $this->$var);
					break;
				case 'CUSTOM':
					foreach($val as $cvar => $cval) {
						define($cvar, $cval);
					}
					break;
				default:
					define($var, $this->$var);
			}
		}
		
// 		echo BASEPATH,$this -> BASEPATH;
// 		echo 'Constructor ready';
		
	}
	
	private function _fixSlashes($fields) {
		foreach($fields as $field) {
			$this -> $field = rtrim($this -> $field, '/\\') . '/';
			$this -> $field = ltrim($this -> $field, '/\\');
		}
		
	}
}