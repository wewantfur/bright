bright = angular.module('bright');
bright.service('authService', function($q, $http) {
	var user;
	return {
		getBEUser : function() {
			var def = $q.defer();
			
			if(typeof(user) == 'undefined') {
				
				$http.get('/bright/json/core/auth/Authorization/getBEUser').success(function(data) {
					if(data.status == 'OK') {
						user = data.result;
						def.resolve(user);
					}
				});
				
			} else {
				def.resolve(user);
			}
			
			return def.promise;
		}
	};
});