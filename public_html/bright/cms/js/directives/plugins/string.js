/**
 * Directive for the string plugin
 * Attributes:
 * - label String
 * - displaylabel String
 * - data Object / json
 * - value String
 */
bright = angular.module('bright');
bright.directive('string',  function() {
	return { 
		templateUrl: 'partials/plugins/string.html',
		restrict: 'A',
		scope: {
			content: '=',
			field: '=',
			
		},
		controller: function($scope, $element, $attrs) {
			$scope.getTemplateUrl = function() {
				switch($scope.type) {
					case 'string':
						return 'partials/plugins/string_input.html';
					case 'html':
						return 'partials/plugins/string_html.html';
					case 'text':
						return 'partials/plugins/string_text.html';
				}
			};
			
			$scope.toggleRTE = function(event) {
				$scope.previewmode = !$scope.previewmode;
				if(!$scope.previewmode) {

					tinymce.init({
						selector: "#" + $scope.rteid,
					    theme: "modern",
//						theme: "modern",
//						add_unload_trigger: false,
//						schema: "html5",
////						inline: true,
//						toolbar: "undo redo",
//						statusbar: false
					});
				}
			};
			
		},
		link: function($scope, elem, attr, ctrl) {
			$scope.type = 'string';
			var ts = (new Date()).getTime();
			$scope.rteid = 'rte_' + Math.round(Math.random() * 1000) + ts;
			$scope.previewmode = true;
			if($scope.field.data != null) {
				$scope.type = $scope.field.data.type;
			}
		}
	};
});