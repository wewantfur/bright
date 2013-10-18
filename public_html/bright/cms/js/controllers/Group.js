bright = angular.module('bright');
bright.controller("groupCtrl", [ '$scope', '$state', 'AdministratorService',
	function($scope, $state, AdministratorService ) {
		if($state.params.GID) {
			AdministratorService.getBEGroup($state.params.GID).then(function(group) {
				console.log(group);
				$scope.group = group;
			});
		}
	} ]);