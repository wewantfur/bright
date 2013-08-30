<?php
/**
 * Logs out the current user
 */
use bright\core\auth\Authorization;

require_once(dirname(__FILE__) . '/../core/Bright.php');

$auth = new Authorization();
$auth -> logoutBE();
