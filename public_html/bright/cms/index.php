<?php
	use bright\core\auth\Authorization;
	
	require_once(dirname(__FILE__) . '/../core/Bootstrap.php');
        
        if (isset($_GET['setup'])) {
            \bright\core\config\Setup::SetupBright();
        }

        if(!Authorization::IsBEAuth()) {
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
	<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'>
<!-- 	<link href="css/screen.css" rel="stylesheet"  media="screen" /> -->
<!-- 	<link href="css/template-icons.css" rel="stylesheet"  media="screen" /> -->
<!-- 	<link href="css/jquery.divider.css" rel="stylesheet"  media="screen" /> -->
	<?php
		// Load all js files in /css folder
		
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
<body class="bright" ng-cloak ng-controller="brightCtrl">
	<nav class="main-nav">
		<h1>Bright CMS</h1>
<!--  			<li ng-class="{ active: $state.includes('events') }"><a href="#/events" l10n-text="modules.events">Events</a></li> -->
<!-- 			<li ng-class="{ active: $state.includes('maps') }"><a href="#/maps" l10n-text="modules.maps">Maps</a></li> -->
<!-- 			<li ng-class="{ active: $state.includes('users') }"><a href="#/users" l10n-text="modules.w">Users</a></li> -->
<!-- 			<li ng-class="{ active: $state.includes('settings') }"><a href="#/settings" l10n-text="modules.settings">Settings</a></li> -->
		<ul>
			<li ng-class="{ active: $state.includes('pages') }"><a href="#/pages" l10n-text="modules.pages">Pages</a></li>
			<li ng-class="{ active: $state.includes('files') }"><a href="#/files" l10n-text="modules.files">Files</a></li>
			<li ng-class="{ active: $state.includes('templates') }"><a href="#/templates" l10n-text="modules.templates">Templates</a></li>
			<li ng-class="{ active: $state.includes('administrators') }"><a href="#/administrators" l10n-text="modules.administrators">Administrators</a></li>
			<li class="right">
				<span l10n-text="general.loggedinas:administrator.name">Logged in as {{administrator.name}}</span>
				<ul>
					<li l10n-text="general.myprofile">My profile</li>
					<li><a href="logout.php" l10n-text="general.logout">Logout</a></li>
				</ul>
			</li>
		</ul>
		<footer>&copy; <?php echo date('Y')?> Fur VOF</footer>
	</nav>
	<div id="wrapper">
		<div ui-view>
		
		</div>
		
		<pre>
			  $state = {{$state.current.name}}
			  $stateParams = {{$stateParams}}
		</pre>
	</div>
	
	<script src="//code.jquery.com/jquery-2.1.0.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.js"></script>
	<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
	<script src="../i18n/l10n-module.js.php"></script>

	<?php
		// Load all js files in /js folder
		$preload = array('js/bright.js', 'js/libs/jquery.ui.widget.js', 'js/libs/jquery.fileupload.js');
		//, 'js/components/scroll.js', 'l10n/getModule/nl.js'
		foreach($preload as $js) {
			echo "<script type='text/javascript' src='$js'></script>\r\n\t";
		}
		$folder = dirname(__FILE__) . '/js';
		
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder,FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		$ts = time();
		foreach($objects as $name => $object){
			if(!$object -> isDir()) {
				if(substr($name, -3, 3) === '.js') {
					$n = substr($name, strlen($folder) -2);
					$n = str_replace('\\', '/', $n);
					if(!in_array($n, $preload))
						echo "<script type='text/javascript' src='$n?v=$ts'></script>\r\n\t";
				}
			}
		}
	?>
	
</body>
</html>
