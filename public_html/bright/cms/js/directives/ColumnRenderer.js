bright = angular.module('bright');
bright.directive('columnrenderer', function() {
	return {
		restrict : 'A',
		link : function(scope, elem, attrs, ctrl) {
			var observers = [];
			observers.push(attrs.$observe('column', function(value) {
				if (value) {

					switch (value) {
						case 'icon':
							$(elem).empty().append('<i class="tpl-icon tpl-'+ scope.$parent.page[value] +'"></i>');
						break;
						default:
							var atag = document.createElement('a');
							atag.setAttribute('href', '#');
							$(atag).on('click', function(e) {
								e.preventDefault();
								scope.$emit('tableColumnTextClick', {item: scope.$parent.page, column: value});
							});
							$(atag).text(scope.$parent.page[value]);
							$(elem).append(atag);
					}
				}
			}));
			observers.push(attrs.$observe('width', function(value) {
				if(value) {
					$(elem).css('width', value);
				}
			}));
			
			observers.push(scope.$on("$destroy", function() {
				$('a', elem).each(function() {
					$(this).off('click');
					var no = observers.length;
					while(--no > -1) {
						console.log('deregistering observr');
						observers[no]();
					}
				});
			}));
		}
	};
});
