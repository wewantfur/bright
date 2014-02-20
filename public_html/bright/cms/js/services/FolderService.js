bright = angular.module('bright');
bright.service('FolderService', function($http, $q) {
	var folders = null;
	return {
		setFolders : function(data) {
			folders = data;

		},
		
		/**
		 * Gets the all the folders
		 * @param parent
		 * @returns
		 */
		getAllFolders : function(parent) {
			var deferred = $q.defer();
			$http.get('/bright/json/core/files/Folders/getAllFolders').success(function(data) {
				if(data.status == 'OK') {
					deferred.resolve(data.result);
				}
			});
			return deferred.promise;
		},
		
		/**
		 * Gets the (root) folders
		 * @param parent
		 * @returns
		 */
		getFolders : function(parent) {
			var deferred = $q.defer();
			if(!parent) {
				if(!folders) {
					$http.get('/bright/json/core/files/Folders/getFolders').success(function(data) {
						if(data.status == 'OK') {
							folders = data.result;
							deferred.resolve(folders);
						}
					});
				} else {
					deferred.resolve(folders);
				}
			}
			return deferred.promise;
		},
		
		setFiles: function(data) {
			files = data;
		},
		
		getFiles: function() {
			return files;
		}
	};
});