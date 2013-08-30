/**
 * Directive for the string plugin
 * Attributes:
 * - label String
 * - displaylabel String
 * - data Object / json
 * - value String
 */
bright = angular.module('bright');
bright.directive('list', function() {
	return { 
		template: '<div class="plugin list"><label for=""><span>{{field.displaylabel}}</span><input type="text" /></label></div>',
		restrict: 'A',
		scope: {
			content: '=',
			field: '=',
		},
		link: function($scope, element, attr, ctrl) {
		}
	};
});