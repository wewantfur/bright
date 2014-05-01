bright = angular.module('bright');
bright.controller("brightCtrl", [ '$scope', 'ConfigService',
	function($scope, ConfigService) {
		$scope.hTemplate = '<span>{{col.value|headername}}</span>';

		ConfigService.getSettings().then(function(data) {		$scope.settings = data; });
	
		$scope.getDate = function(date) {
			return 'Datum!';
		};
		
		$scope.updatePreferences = function() {
			console.log('updateSettings');
			
			ConfigService.setPreferences($scope.administrator.preferences).then(function(data) {
				
				console.log('New Settings');
				$scope.administrator.preferences = data;
				
			});
		};
		
	} ]);