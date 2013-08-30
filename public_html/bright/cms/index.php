<?php
	use bright\core\auth\Authorization;

	require_once(dirname(__FILE__) . '/../core/Bright.php');
	
	if(!Authorization::isBEAuth()) {
		header("Location: login.php");
		exit;
	}
	
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="nl" ng-app="bright">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name='copyright' content='copyright Fur' /> 
	<meta name="author" content="www.wewantfur.com" />
	<link href="http://cdn.wewantfur.com/basicfur/css/basicfur-v1-min.css" rel="stylesheet" />
	<link href="css/screen.css" rel="stylesheet"  media="screen" />
	<link href="css/template-icons.css" rel="stylesheet"  media="screen" />
	<link href="css/jquery.divider.css" rel="stylesheet"  media="screen" />
	<?php
		// Load all js files in /js folder
		
		$folder = dirname(__FILE__) . '/css';
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder,FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object){
			if(!$object -> isDir()) {
				if(substr($name, -4, 4) === '.css') {
					$n = substr($name, strlen($folder) -3);
					echo "<link href='$n' rel='stylesheet' media='screen' />\r\n";
				}
			}
		}
	?>
	<title>Bright</title>
</head>
<body ng-cloak>
	<nav>
		<ul>
			<li ng-class="{ active: $state.includes('pages') }"><a href="#/pages">Pages</a></li>
<!-- 			<li ng-class="{ active: $state.includes('events') }"><a href="#/events">Events</a></li> -->
<!-- 			<li ng-class="{ active: $state.includes('maps') }"><a href="#/maps">Maps</a></li> -->
<!-- 			<li ng-class="{ active: $state.includes('users') }"><a href="#/users">Users</a></li> -->
			<li ng-class="{ active: $state.includes('files') }"><a href="#/files">Files</a></li>
			<li ng-class="{ active: $state.includes('templates') }"><a href="#/templates">Templates</a></li>
<!-- 			<li ng-class="{ active: $state.includes('administrators') }"><a href="#/administrators">Administrators</a></li> -->
<!-- 			<li ng-class="{ active: $state.includes('settings') }"><a href="#/settings">Settings</a></li> -->
			<li class="right">Logged in as {{administrator.name}}
				<ul>
					<li>My profile</li>
					<li>Logout</li>
				</ul>
			</li>
		</ul>
	</nav>
	<div id="wrapper">
		<div ui-view>
		
		</div>
		
		<pre>
			  $state = {{$state.current.name}}
			  $stateParams = {{$stateParams}}
		</pre>
	</div>
	<footer>&copy; 2013 Fur VOF</footer>
	
	<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.js"></script>
	<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>

	<?php
		// Load all js files in /js folder
		
		$folder = dirname(__FILE__) . '/js';
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder,FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		foreach($objects as $name => $object){
			if(!$object -> isDir()) {
				if(substr($name, -3, 3) === '.js') {
					$n = substr($name, strlen($folder) -2);
					echo "<script type='text/javascript' src='$n'></script>\r\n";
				}
			}
		}
	?>
	
</body>
</html>