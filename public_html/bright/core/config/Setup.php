<?php
namespace bright\core\config;

use bright\core\auth\Authorization;
use bright\core\model\Model;

/**
 * First time run setup
 * @author Ids
 *
 */
class Setup {
	
    public static function SetupBright() {
        $ng = Model::getInstance() -> getField("SELECT count('GID') FROM be_groups");
        if($ng == 0) {
            // Groups not initialized
            /*	
             * const GR_SU = 1;
	const GR_WEBMASTER = 2;
	const GR_SITEMANAGER = 3;
	const GR_FILEMANAGER = 4;
	const GR_EVENTMANAGER = 5;
	const GR_MAPSMANAGER = 6;
	const GR_MAILINGMANAGER = 7;
	const GR_USERMANAGER = 8;
	const GR_ELEMENTMANAGER = 9;*/
            $groups = array(Authorization::GR_SU => 'SuperUser',
                Authorization::GR_WEBMASTER => 'Webmaster',
                Authorization::GR_SITEMANAGER => 'Site manager',
                Authorization::GR_FILEMANAGER => 'Media manager',
                Authorization::GR_EVENTMANAGER => 'Calendar manager',
                Authorization::GR_MAPSMANAGER => 'Maps manager',
                Authorization::GR_MAILINGMANAGER => 'Mailing manager',
                Authorization::GR_USERMANAGER => 'User manager',
                Authorization::GR_ELEMENTMANAGER => 'Element manager');
            
            $sql = "INSERT INTO be_groups (GID, name, locked) VALUES";
            $sqla = [];
            foreach($groups as $key => $name) {
                $sqla[] = "($key, '$name', 1)";
            }
            $sql .= implode(",\r\n", $sqla);
            Model::getInstance() -> updateRow($sql);
        }
    }
    
    public static function CreateTables() {
    	$tables[] = "CREATE TABLE IF NOT EXISTS `administratorpermissions` (`id` int(11) NOT NULL AUTO_INCREMENT,`administratorId` int(11) NOT NULL,`permission` varchar(30) NOT NULL,PRIMARY KEY (`id`))ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `templatefields` (`fieldId` int(11) NOT NULL AUTO_INCREMENT,`templateId` int(11) NOT NULL,`label` varchar(50) NOT NULL,`displaylabel` varchar(50) NOT NULL,`index` int(11) NOT NULL,`fieldtype` varchar(25) NOT NULL,`data` text,PRIMARY KEY (`fieldId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `templates` (`templateId` int(11) NOT NULL AUTO_INCREMENT,`label` varchar(20) NOT NULL,`displaylabel` varchar(50) NOT NULL,`icon` varchar(25) NOT NULL,`type` int(11) NOT NULL DEFAULT '1',`parser` int(11) NOT NULL DEFAULT '1',`enabled` int(1) NOT NULL DEFAULT '1',`maxchildren` int(11) NOT NULL DEFAULT '-1',`allowedparents` varchar(25) DEFAULT NULL,`allowedchildren` varchar(25) DEFAULT NULL,`groups` varchar(25) DEFAULT NULL,PRIMARY KEY (`templateId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `be_groups` (`GID` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(255) NOT NULL,`locked` tinyint(1) NOT NULL,PRIMARY KEY (`GID`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `be_usergroups` (`UID` int(3) NOT NULL,`GID` int(3) NOT NULL,UNIQUE KEY `UID` (`UID`,`GID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `be_users` (`UID` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(255) NOT NULL,`email` varchar(255) NOT NULL,`password` varchar(255) NOT NULL,`settings` text,`default_GID` int(3) DEFAULT '2',`pages_GID` int(3) DEFAULT NULL,`files_GID` int(3) DEFAULT NULL,`events_GID` int(3) DEFAULT NULL,`maps_GID` int(3) DEFAULT NULL,`users_GID` int(3) DEFAULT NULL,`elements_GID` int(3) DEFAULT NULL,`lastlogin` datetime DEFAULT NULL,PRIMARY KEY (`UID`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `content` (`contentId` int(11) NOT NULL AUTO_INCREMENT,`templateId` int(11) NOT NULL,`creationdate` datetime NOT NULL,`modificationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `createdby` int(11) NOT NULL,`modifiedby` int(11) DEFAULT NULL,`UID` int(3) DEFAULT NULL,`GID` int(3) DEFAULT NULL,`chmod` int(3) DEFAULT NULL, PRIMARY KEY (`contentId`),KEY `templateId` (`templateId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `fields` (`id` int(11) NOT NULL AUTO_INCREMENT,`contentId` int(11) NOT NULL,`lang` varchar(3) NOT NULL,`field` varchar(255) NOT NULL,`deleted` tinyint(1) NOT NULL DEFAULT '0',PRIMARY KEY (`id`),UNIQUE KEY `contentId_2` (`contentId`,`lang`,`field`),KEY `contentId` (`contentId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `file_mountpoints` (`GID` int(11) NOT NULL,`path` varchar(255) NOT NULL,KEY `GID` (`GID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `pages_mountpoints` (`GID` int(11) NOT NULL,`pageId` int(11) NOT NULL,KEY `GID` (`GID`),KEY `pageId` (`pageId`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `pages` (`pageId` int(11) NOT NULL AUTO_INCREMENT,`contentId` int(11) DEFAULT NULL,`parentId` int(11) DEFAULT NULL,`label` varchar(255) DEFAULT NULL,`publicationdate` datetime NOT NULL,`expirationdate` datetime NOT NULL,`alwayspublished` tinyint(1) NOT NULL DEFAULT '1',`showinnavigation` int(11) NOT NULL DEFAULT '1',`index` int(11) NOT NULL DEFAULT '0',`felogin` tinyint(1) DEFAULT NULL,`lft` int(11) NOT NULL,`rgt` int(11) NOT NULL,PRIMARY KEY (`pageId`),UNIQUE KEY `pageId_UNIQUE` (`pageId`),UNIQUE KEY `contentId` (`contentId`),KEY `label` (`label`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    	$tables[] = "CREATE TABLE IF NOT EXISTS `parsers` (`parserId` int(11) NOT NULL AUTO_INCREMENT, `label` varchar(20) NOT NULL, PRIMARY KEY (`parserId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    }
}