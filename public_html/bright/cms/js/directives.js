'use strict';

/* Directives */
angular.module('directives', [])
	.directive('stopEvent', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            element.bind(attr.stopEvent, function (e) {
                e.stopPropagation();
            });
        }
    };
}).directive('scrollFix', function($window) {
	return function(scope, element, attrs) {
		var windowEl = angular.element($window);
		windowEl.on('scroll', function() {
			$('.listheader').css({top: 'inherit', position: ''});
			
			if(	$('.listheader').offset() && $('body').offset() && 
				windowEl.scrollTop() > ($('.listheader').offset().top - $('body').offset().top)+12) {
				
				$('.listheader').css({top: '60px', position: 'fixed'});
			} else {
				$('.listheader').css({top: 'inherit', position: ''});
			}
		});
	};
});