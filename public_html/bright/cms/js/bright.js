var bright = angular.module('bright',
		// dependancies
		['ui.compat', 
		 'ui.bootstrap.dialog',
//		 'ui.tinymce',
		 'Plugin',
		 'ngGrid',
		 'angularTree',
		 'directives',
		 'states']).

    run(
	  ['$rootScope', '$state', '$stateParams', 'authService', 
		function($rootScope, $state, $stateParams, authService) {
			$rootScope.$state = $state;
			$rootScope.$stateParams = $stateParams;
			$rootScope.title = '';
			
			authService.getBEUser().then(function($beuser) {
				$rootScope.administrator = $beuser;
			});
			
			
			$rootScope.openFileExplorer = function() {
				console.log('openFileExplorer');
			};
		} ]).
	controller('sortableCtrl', ['$scope', function($scope) {
		$scope.columns = ['icon', 'id', 'label'];
	}]);
