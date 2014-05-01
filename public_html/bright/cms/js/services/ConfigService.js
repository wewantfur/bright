bright = angular.module('bright');
bright.service('ConfigService', function($q, $http) {
	var settings;
	var loading = false;
	return {
		getSettings : function() {
			var def = $q.defer();
			if(typeof(settings) == 'undefined' && !loading) {
				loading = true;
				$http.get('/bright/json/core/config/Config/getSettings').success(function(data) {
					if(data.status == 'OK') {
						settings = data.result;
						def.resolve(settings);
					}
					isLoading = false;
				});
			} else if(!isLoading) {
				def.resolve(settings);
				
			}
			return def.promise;
		},
		
		/**
		 * Updates the new settings
		 * @param data
		 * @returns
		 */
		setPreferences: function(preferences) {
			var deferred = $q.defer();
			$http.post('/bright/json/core/config/Config/setPreferences', {arguments: [preferences]}).success(function(data) {
				deferred.resolve(data);
			});
			
			return deferred.promise;
		},
		
		getLanguageName: function(lang) {
			switch(lang) {
				case 'nl':
					return 'Nederlands';
				case 'en':
					return 'English';
			}
			return '[language]';
		}
	};
});