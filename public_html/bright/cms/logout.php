<?php
/**
 * Logs out the current user
 */
use bright\core\auth\Authorization;

require_once(dirname(__FILE__) . '/../core/Bootstrap.php');

$auth = new Authorization();
$auth -> LogoutBE();
