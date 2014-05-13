bright = angular.module('bright');
/**
 * @todo: Check if this thing works without a $modalInstance
 */
bright.controller("foldersCtrl", [ '$scope', '$modalInstance', 'FolderService',
	function($scope, $modalInstance, FolderService) {
		$scope.folders = [];

		FolderService.GetAllFolders(null).then(function(folders) {
			$scope.folders = folders;
			$scope.folderSelect($scope.folders[0]);
		});

		$scope.folderSelect = function(folder) {
			if(typeof(folder) == 'undefined') {
				$scope.selectedFolder = this.item;
			} else {
				$scope.selectedFolder = folder;
			}
			
		};

		$scope.folderOpenClose = function() {
			var item = this.item;
			item.isopen = !item.isopen;
		};
		
		$scope.ok = function () {
			if($scope.selectedFolder) {
				$modalInstance.close($scope.selectedFolder);
			}
	    };
		
		$scope.cancel = function() {
	        $modalInstance.dismiss('cancel');
		}
	}]);