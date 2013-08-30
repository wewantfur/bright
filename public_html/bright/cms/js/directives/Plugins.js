angular.module('bright').directive('plugin', [ '$compile', function(compile) {
	return {
		restrict : 'A',
		
		scope: {
			content: '=',
			field: '=',
			plugin: '=',
			lang: '='
		},
		link : function($scope, elem, attrs) {
			if(!$scope.content.hasOwnProperty($scope.field.label)) {
				$scope.content[$scope.field.label] = {};
			}
			if(!$scope.content[$scope.field.label].hasOwnProperty($scope.lang)) {
				$scope.content[$scope.field.label][$scope.lang] = null;	
			}
			var el = compile('<div ' + $scope.plugin + ' data-field="field" data-content="content[field.label][lang]" />')($scope);
			elem.append(el);
		}
	};
} ]);