bright = angular.module('bright');
bright.service('AdministratorService', ['$q', '$http', 'ExceptionService', function($q, $http, ExceptionService) {
	var users,groups;
	
	var findUser = function (id) {
		var nu = users.length;
		while(--nu > -1) {
			if(users[nu].UID == id)
				return users[nu].name;
		}
	};
	
	return {

		getBEUsers: function() {
			var def = $q.defer();

			if(typeof(users) == 'undefined') {
				
				$http.get('/bright/json/core/auth/Administrators/getAdministrators').success(function(data) {
					if(data.status == 'OK') {
						users = data.result;
						def.resolve(users);
					}
				}).error(function(data) {
					def.reject(ExceptionService.errorHandler(data));
				});
			} else {
				def.resolve(users);
			}
			
			return def.promise;
		},
		
		
		/**
		 * Gets a backend group
		 * @param GID The gid of the group
		 * @returns promise
		 */
		getBEGroup: function(GID) {
			var def = $q.defer();
			
			$http.post('/bright/json/core/auth/Administrators/getGroup', {arguments: [GID]}).success(function(data) {
				if(data.status == 'OK') {
					def.resolve(data.result);
				}
			}).error(function(data) {
				def.reject(ExceptionService.errorHandler(data));
			});
			
			
			return def.promise;
		},
		
		getBEGroups: function() {
			var def = $q.defer();
			
			if(typeof(groups) == 'undefined') {
				
				$http.get('/bright/json/core/auth/Administrators/getGroups').success(function(data) {
					if(data.status == 'OK') {
						groups = data.result;
						def.resolve(groups);
					}
				}).error(function(data) {
					def.reject(ExceptionService.errorHandler(data));
				});
			} else {
				def.resolve(groups);
			}
			
			return def.promise;
		},
				
	
	
	};
}]);