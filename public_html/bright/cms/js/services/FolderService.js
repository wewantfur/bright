bright = angular.module('bright');
bright.service('FolderService', function($http, $q) {
	var folders = null;
	return {
		SetFolders : function(data) {
			folders = data;

		},
		
		/**
		 * Gets the all the folders
		 * @param parent
		 * @returns
		 */
		GetAllFolders : function(parent) {
			var deferred = $q.defer();
			$http.get('/bright/json/core/factories/FolderFactory/GetAllFolders').success(function(data) {
				if(data.status == 'OK') {
					deferred.resolve(data.result);
				}
			});
			return deferred.promise;
		},

        GetRootFolders: function() {

            var deferred = $q.defer();
            if(!folders) {
                $http.get('/bright/json/core/factories/FolderFactory/GetFolders').success(function(data) {
                    if(data.status == 'OK') {
                        folders = data.result;
                        deferred.resolve(folders);
                    }
                });
            } else {
                deferred.resolve(folders);
            }
            return deferred.promise;
        },
		
		/**
		 * Gets the (root) folders
		 * @param parent
		 * @returns
		 */
		GetFolders : function(parent) {
            var deferred = $q.defer();
            console.log("GetFolders: " , parent);
            $http.post('/bright/json/core/factories/FolderFactory/GetFolders', {arguments:[parent]}).success(function(data) {
                if(data.status == 'OK') {
                    folders = data.result;
                    deferred.resolve(folders);
                }
            });
            return deferred.promise;
		}
	};
});