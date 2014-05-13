bright = angular.module('bright');
bright.controller("filesCtrl",
	['$scope', '$http', '$state', 'FolderService', 'FileService',
	function($scope, $http, $state, FolderService, FileService) {

        $scope.uploadoptions = {url: '/bright/json/core/files/Files/upload', autoUpload: true};
		$scope.displayMode = $scope.getPreference('files.displayMode', 'list');

		/**
		 * Gets the folders from the backend and selects the first folder
		 */
		FolderService.GetRootFolders().then(function(folders) {
			$scope.folders = folders;
			$scope.folderSelect(folders[0]);
		});

		$scope.$on('fileuploadstop', function(e, f) {
			// Trigger get files
			$scope.folderSelect($scope.selectedFolder);
		});

		$scope.folderOpenClose = function() {
			var item = this.item;
			item.isopen = !item.isopen;
			if(item.isopen) {

                FolderService.GetFolders(item.path).then(function(folders) {
                    item.children = folders;
                    FolderService.SetFolders($scope.folders);
                })

			}
		};

		$scope.folderSelect = function(folder) {
			if(typeof(folder) == 'undefined') {
				$scope.selectedFolder = this.item;
			} else {
				$scope.selectedFolder = folder;
			}
            FileService.GetFiles($scope.selectedFolder.path).then(function(files) {
                $scope.files = files;
            });
		};

        $scope.setFile = function() {
            angular.forEach($scope.files, function(item) {
                item.selected = false;
            })
            this.file.selected = true;
        }

		$scope.setDisplayMode = function(val) {
			$scope.displayMode = val;
			$scope.updatePreferences('files.displayMode', val);
		};


		$scope.updateWidths = function(widths) {
			$scope.updatePreferences('files.dividers', widths);
		};
	}]);