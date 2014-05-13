angular.module('bright').filter('fromMysqlDate', ['$filter', function($filter) {
	/**
	 * Converts MySQL dates to JS dates, then applies the given format to it 
	 */
	return function (input, format) {
		var d = new Date(input);
		return $filter('date')(d, format);
	};
}]);