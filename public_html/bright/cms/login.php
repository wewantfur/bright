<?php
	use bright\core\auth\Authorization;
	
require_once(dirname(__FILE__) . '/../core/Bootstrap.php');
	
if(isset($_POST['login'])) {
	$auth = new Authorization();
	
	try {
		$auth -> authBE($_POST['email'],$_POST['password']);
		header("Location: /bright/cms/");
		exit;
	}catch(AuthException $ex) {
		
	}
}?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head></head>
	<body>
	
		<form method="POST" action="/bright/cms/login.php">
			<label for="email">E-mail address</label>
			<input type="email" id="email" name="email" />

			<label for="password">Password</label>
			<input type="password" id="password" name="password" />
		
			<input type="submit" name="login" value="Login" />
			
		</form>
	</body>
</html>

