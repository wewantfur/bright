bright = angular.module('bright');
bright.service('PageService', function($http, $q) {
	var pages, selectedPage;
	var isLoading = false;
	
	return {
		
		/**
		 * Saves a page
		 * @param data The page to save
		 * @returns All updates pages
		 */
		setPage: function(data) {
			var deferred = $q.defer();
			$http.post('/bright/json/core/factories/PageFactory/setContent', {arguments: [data]}).success(function(data) {
				deferred.resolve(data);
			});
			
			return deferred.promise;
		},
		
		setPages: function(data) {
			pages = data;

		},
		
		/**
		 * Gets the (root) folders
		 * @param parent
		 * @returns
		 */
		getPages : function() {
			var deferred = $q.defer();
			if(typeof(pages) == 'undefined') {
				$http.get('/bright/json/core/content/Pages/getPagesForBE').success(function(data) {
					if(data.status == 'OK') {
						pages = data.result;
						deferred.resolve(data.result);
					}
				});
			} else {
				deferred.resolve(pages);
			}
			return deferred.promise;
		}

	};
})