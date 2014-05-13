/**
 * Directive for the image plugin
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
										controller: 'dialogCtrl', 
										tpl: 'partials/Files.html',
										ctrl: 'filesCtrl',
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