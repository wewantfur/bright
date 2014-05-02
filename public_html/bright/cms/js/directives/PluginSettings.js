angular.module('bright').directive('pluginSettings', [ '$compile', function(compile) {
	return {
		restrict : 'A',
		
		scope: {
			field: '=',
			plugin: '=',
			lang: '='
		},
		link : function($scope, elem, attrs) {
			console.log($scope);
//			if($scope.content) {
//				if(!$scope.content.hasOwnProperty($scope.field.label)) {
//					$scope.content[$scope.field.label] = {};
//				}
//				if(!$scope.content[$scope.field.label].hasOwnProperty($scope.lang)) {
//					$scope.content[$scope.field.label][$scope.lang] = null;	
//				}
//			}
			var el = compile('<div ' + $scope.field.fieldtype + '-settings data-field="field"  />')($scope);
			elem.append(el);
		}
	};
} ]);