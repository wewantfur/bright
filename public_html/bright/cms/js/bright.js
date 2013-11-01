var bright = angular.module('bright',
		// dependancies
		['ui.compat', 
//		 'ui.bootstrap.dialog',
		 'ui.bootstrap.modal',
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
	  ['$rootScope', '$state', '$stateParams', '$templateCache', 'AuthService', 
		function($rootScope, $state, $stateParams, $templateCache, AuthService) {
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
			
			$templateCache.put("template/modal/backdrop.html", 
					'<div class="backdrop"></div>');
			
			$templateCache.put("template/modal/window.html", 
				'<div class="modal fade {{ windowClass }}" ng-class="{in: animate}" ng-style="{\'z-index\': 1050 }" ng-transclude></div>');
		} ]);
