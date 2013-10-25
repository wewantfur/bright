var bright = angular.module('bright',
		// dependancies
		['ui.compat', 
		 'ui.bootstrap.dialog',
		 'blueimp.fileupload',
//		 'ui.tinymce',
         'l10n',
         'l10n-tools',
         'my-l10n-nl',
		 'Plugin',
		 'ngfur.grid',
		 'angularTree',
		 'directives',
		 'states']).

    run(
	  ['$rootScope', '$state', '$stateParams', 'AuthService', 
		function($rootScope, $state, $stateParams, AuthService) {
			$rootScope.$state = $state;
			$rootScope.$stateParams = $stateParams;
			$rootScope.title = '';
			
			AuthService.getBEUser().then(function($beuser) {
				$rootScope.administrator = $beuser;
			});
			AuthService.getBEUserNames();
			
			
			$rootScope.openFileExplorer = function() {
				console.log('openFileExplorer');
			};
		} ]);
