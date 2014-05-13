bright = angular.module('bright');
bright.service('FileService', ['$http', '$q', function($http, $q) {
	var folders, files, selectedFolder, selectedFile;
	return {

        GetFiles : function(parent) {
            var deferred = $q.defer();

            $http.post('/bright/json/core/factories/FileFactory/GetFiles', {arguments:[parent]}).success(function(data) {
                if(data.status == 'OK') {
                    files = data.result;
                    deferred.resolve(data.result);
                }
            });
            return deferred.promise;
        },
		
		SetFiles: function(data) {
			files = data;
		}
	};
}]);