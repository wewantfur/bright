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
}]).filter('templatetypeicon', function() {
	return function (input) {
		 switch (input) {
         case 1:
             return 'file-text'; 		// page
         case 2:
             return 'th-list';		 	// list
         case 3:
             return 'calendar'; 		// event
         case 4:
             return 'puzzle-piece';		// element
         case 5:
             return 'map-marker';		// marker
         case 6:
             return 'user';				// user

     }
	};
});