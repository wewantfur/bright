bright = angular.module('bright');
bright.controller("groupCtrl", [ '$scope', '$state', '$modal', 'AdministratorService', 'PageService',
	function($scope, $state, $modal, AdministratorService, PageService) {
		if($state.params.GID) {
			
			// First, get all pages
			PageService.getPages().then(function(pages) {
				$scope.pages = pages;
				
				// Now, get the group itself
				AdministratorService.getBEGroup($state.params.GID).then(function(group) {
					for(var j = 0; j < group.page_mountpoints.length; j++) {
						var np = $scope.pages.list.length;
						for(var i = 0; i < np; i++) {
							if($scope.pages.list[i].pageId == group.page_mountpoints[j]) {
								group.page_mountpoints[j] = $scope.pages.list[i];
								i = np;
							}
						}	
					}
					
					for(var j = 0; j < group.file_mountpoints.length; j++) {
						var fm = group.file_mountpoints[j];
						if(fm.lastIndexOf('/') == fm.length-1) {
							fm = fm.substring(0, fm.length-1);
						}
						group.file_mountpoints[j] = fm;
					}
					$scope.group = group;
				});
			});
			
			$scope.save = function() {
				AdministratorService.setBEGroup($scope.group).then(function(result) {
					
				});
			}
			
			$scope.cancel = function() {
				
			}
			
			/**
			 * Open a popup for folder browsing
			 */
			$scope.addFileMountpoint = function() {
				var modalInstance = $modal.open({
					templateUrl : 'partials/folders.html',
					controller : 'foldersCtrl',
					resolve : {
						items : function() {
							return $scope.items;
						}
					}
				});
				
			    modalInstance.result.then(function (selectedItem) {
			    	if(selectedItem)
			    		$scope.group.file_mountpoints.push(selectedItem.path);
				});
			}
		}
	} ]);