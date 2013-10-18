bright = angular.module('bright');
bright.service('AuthService', function($q, $http) {
	var user;
	var users;
	
	var findUser = function (id) {
		var nu = users.length;
		while(--nu > -1) {
			if(users[nu].UID == id)
				return users[nu].name;
		}
	};
	
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
		},
		getBEUserNames: function() {
			$http.get('/bright/json/core/auth/Administrators/getAdministratorNames').success(function(data) {
				if(data.status == 'OK') {
					users = data.result;
				}
			});
		},
		
		getBEUserName: function(id) {
			return findUser(id);
		}
	
	
	};
});