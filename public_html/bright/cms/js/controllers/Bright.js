bright = angular.module('bright');
bright.controller("brightCtrl", [ '$scope', 'ConfigService',
	function($scope, ConfigService) {
		$scope.hTemplate = '<span>{{col.value|headername}}</span>';

		ConfigService.getSettings().then(function(data) {		$scope.settings = data; });
	
		$scope.getDate = function(date) {
			return 'Datum!';
		};
	} ]);