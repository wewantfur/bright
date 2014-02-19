bright = angular.module('bright');
bright.service('ExceptionService', function($q, $http) {
	return {
		errorHandler : function(data) {
			alert(data.message);
			return data.message;
		}
	};
});