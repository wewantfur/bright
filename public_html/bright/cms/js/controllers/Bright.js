bright = angular.module('bright');
bright.controller("brightCtrl", [ '$scope',
	function($scope) {
		$scope.hTemplate = '<span>{{col.value|headername}}</span>';
	
		$scope.getDate = function(date) {
			return 'Datum!';
		};
	} ]);