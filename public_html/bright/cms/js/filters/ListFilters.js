angular.module('bright').filter('adminname', ['AuthService', function(AuthService) {
	return function (input) {
		if(input == null)
			return null;
		
		return AuthService.getBEUserName(Number(input));
	};
}]).filter('headername', ['l10n', function(l10n) {
	return function (input) {
		if(input == null)
			return null;
		
		return l10n.get("lists.headers." + input);
	};
}]);