/**
 * Directive for the string plugin
 * Attributes:
 * - label String
 * - displaylabel String
 * - data Object / json
 * - value String
 */
bright = angular.module('bright');
bright.directive('image', ['$dialog', function($dialog) {
	return { 
		templateUrl: 'partials/plugins/image.html',
		restrict: 'A',
		scope: {
			content: '=',
			field: '=',
		},
		link: function($scope, element, attr, ctrl) {
			$scope.browse = function() {
				
				var d = $dialog.dialog({templateUrl: 'partials/dialog.html', 
										controller: 'DialogCtrl', 
										tpl: 'partials/Files.html',
										ctrl: 'FilesCtrl',
										backdrop: true,
									    keyboard: true,
									    backdropClick: true});
			    d.open().then(function(result){
		    		if(result) {
		    			alert('dialog closed with result: ' + result);
		    		}
			    });
			};
		}
	};
}]);