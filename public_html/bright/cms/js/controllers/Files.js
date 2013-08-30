bright = angular.module('bright');
bright.controller("FilesCtrl", 
	['$scope', '$http', '$state', 'fileService',
	function($scope, $http, $state, fileService) {
		
		$('.divider').divider({widths: [25,75]});
		
		fileService.getFolders().then(function(folders) {
			$scope.folders = folders;
			
		});
		
		$scope.folderOpenClose = function() {
			var item = this.item;
			item.isopen = !item.isopen;
			if(item.isopen) {
				$http.post('/bright/json/core/files/Files/getFolders', {arguments:[item.path]}).success(function(data) {
					if(data.status == 'OK') {
						item.children = data.result;
						fileService.setFolders($scope.folders);
					}
				});
			}
		};
		
		$scope.folderSelect = function() {
			$scope.selectedFolder = this.item;
			
			$http.post('/bright/json/core/files/Files/getFiles', {arguments:[$scope.selectedFolder.path]}).success(function(data) {
				if(data.status == 'OK') {
					$scope.files = data.result;
				}
			});
		};
	}]);