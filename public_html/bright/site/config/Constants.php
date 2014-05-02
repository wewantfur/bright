<?php
namespace bright\site\config;
/**
 * @package site.Config
 */
use bright\core\frontend\Router;

require_once(dirname(__FILE__) . '/../../core/config/Constants.php');
/**
 * @author wewantfur
 * @version 1.2
 * @package site.Config
 */
class Constants extends \bright\core\config\Constants {
	// Put default values here
	protected $DB_HOST = 'localhost';
// 	protected $DB_PORT = '8889';
	protected $DB_USER = 'root';
	protected $DB_PASSWORD = 'Bl03m51ng3l222';
	protected $DB_DATABASE = 'bright';

	protected $LIVESERVER = false;

	protected $BASEURL = 'http://bright.localhost/';

	protected $MAILINGFROM = 'info@wewantfur.com';
	protected $MAILINGBOUNCE = 'info@wewantfur.com';

	protected $SYSMAIL = 'info@wewantfur.com';

	protected $SMTP = 'smtp.ziggo.nl';
	protected $SMTPPORT = 25;

	protected $TRANSPORT = 'smtp';



	protected $SITENAME = 'Bright CMS';
	protected $AVAILABLELANG = 'nl,en,fy';

	protected $LOGINPAGE = 'login/';
	protected $USEPREFIX = true;

	protected $CMSFOLDER = 'bright/cms-dev/';
	
	protected $MYCUSTOMVAR = 'BLAAT!';
	//protected $CMSFOLDER = 'bright/cms/';

	function __construct() {

		// Override settings here
		if(isset($_SERVER['HTTP_HOST'])) {
			switch($_SERVER['HTTP_HOST']) {
				case 'bright.camel.localhost':
					$this -> DB_HOST = 'localhost';
					$this -> DB_USER = 'root';
					$this -> DB_PASSWORD = 'Bl03m51ng3l';
					$this -> DB_DATABASE = 'bright_camel';
					$this -> BASEURL = 'http://bright.camel.localhost/';
					break;
			}
		}
		
		parent::__construct();
		
		$this -> ROUTES = array('/homepage' => 'homepageView');
	}
}
new Constants();