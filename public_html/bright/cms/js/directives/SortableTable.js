/**
 * Creates a sortable table for bright cms
 */
bright = angular.module('bright');
bright.directive('sortableTable', function() {
	return {
		restrict: 'E',
		templateUrl: 'partials/sortableTable.html',
		/**
		 * 
		 * @param scope
		 * @param elem
		 * @param attrs Attributes: 
		 * -data-header-class, 
		 * -data-buttons, 
		 * -data-icons, 
		 * -data-multiple-buttons, 
		 * -data-multiple-icons,
		 * -data-columns,
		 * -data-dataprovider,
		 * -data-order-column
		 * @param ctrl
		 * @returns
		 */
		link: function(scope, elem, attrs, ctrl) {
			
		}
	};
});