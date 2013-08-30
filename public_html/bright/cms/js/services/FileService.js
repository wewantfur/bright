bright = angular.module('bright');
bright.service('fileService', function($http, $q) {
	var folders, files, selectedFolder, selectedFile;
	return {
		setFolders : function(data) {
			folders = data;

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
					$http.get('/bright/json/core/files/Files/getFolders').success(function(data) {
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