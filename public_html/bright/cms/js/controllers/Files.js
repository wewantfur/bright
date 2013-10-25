bright = angular.module('bright');
bright.controller("filesCtrl", 
	['$scope', '$http', '$state', 'FileService',
	function($scope, $http, $state, FileService) {
		$scope.uploadoptions = {url: '/bright/json/core/files/Files/upload', autoUpload: true};
		$scope.displayMode = 'list';
		
//		$('.divider').divider({widths: [25,75]});
		
		FileService.getFolders().then(function(folders) {
			$scope.folders = folders;
//			$scope.selectedFolder = folders[0];
			$scope.folderSelect(folders[0]);
		});
		
		$scope.$on('fileuploadstop', function(e, f) {
			console.log('fileuploadstop', e, f);
			// Trigger get files
			$scope.folderSelect($scope.selectedFolder);
		})
		
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
		
		$scope.folderSelect = function(folder) {
			if(typeof(folder) == 'undefined') {
				$scope.selectedFolder = this.item;
			} else {
				$scope.selectedFolder = folder;
			}
			$http.post('/bright/json/core/files/Files/getFiles', {arguments:[$scope.selectedFolder.path]}).success(function(data) {
				if(data.status == 'OK') {
					$scope.files = data.result;
				}
			});
		};
		
		$scope.setDisplayMode = function(val) {
			console.log(val);
			$scope.displayMode = val;
		}
		
		setTimeout(function() {
			$('.divider').divider({widths: [25,75]});
			
		}, 100);
	}]);