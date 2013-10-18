bright = angular.module('bright');
bright.controller("filesCtrl", 
	['$scope', '$http', '$state', 'FileService',
	function($scope, $http, $state, FileService) {
		
//		$('.divider').divider({widths: [25,75]});
		
		FileService.getFolders().then(function(folders) {
//			if(typeof folders != 'array') {
//				$scope.folders = [folders];
//			} else {
				$scope.folders = folders;
//			}
//			$scope.folders = folders;
			console.log($scope.folders);
			
		});
		
		$scope.folderOpenClose = function() {
			var item = this.item;
			item.isopen = !item.isopen;
			if(item.isopen) {
				$http.post('/bright/json/core/files/Files/getFolders', {arguments:[item.path]}).success(function(data) {
					if(data.status == 'OK') {
						item.children = data.result;
						FileService.setFolders($scope.folders);
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
		setTimeout(function() {
			$('.divider').divider({widths: [25,75]});
			
		}, 100);
	}]);