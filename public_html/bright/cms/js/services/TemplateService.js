bright = angular.module('bright');
bright.service('TemplateService', function($q, $http) {
	var templates; 
	var isLoading = false;
	return {
		setTemplates: function(data) {
			templates = data;

		},
		
		getFieldTypes: function() {
			var def = $q.defer();
			$http.post('/bright/json/core/content/Fields/getFieldTypes').success(function(data) {
				if(data.status == 'OK') {
					def.resolve(data.result);
				}
			});
			return def.promise;
			
		},
		
		getTemplate: function(id) {
			var def = $q.defer();
			$http.post('/bright/json/core/factories/TemplateFactory/getTemplate', {'arguments':[id]}).success(function(data) {
				if(data.status == 'OK') {
					def.resolve(data.result);
				}
			});
			return def.promise;
		},
		
		/**
		 * Gets all the templates
		 * @returns
		 */
		getTemplates : function() {
			var def = $q.defer();
			if(typeof(templates) == 'undefined') {
				if(!isLoading) {
					isLoading = true;
					$http.get('/bright/json/core/factories/TemplateFactory/getTemplates').success(function(data) {
						if(data.status == 'OK') {
							templates = data.result;
							def.resolve(templates);
						}
						isLoading = false;
					});
				}
			} else {
				def.resolve(templates);
			}
			return def.promise;
		}
	};
});