bright = angular.module('bright');
bright.controller("brightCtrl", [ '$scope', 'ConfigService', 'l10n',
                                  
	function($scope, ConfigService, l10n) {
		$scope.hTemplate = '<span>{{col.value|headername}}</span>';

		ConfigService.getSettings().then(function(data) { $scope.settings = data; });
		
		/**
		 * Updates the user preferences (divider widths, sorting, etc)
		 */
		$scope.updatePreferences = function() {
			ConfigService.setPreferences($scope.administrator.preferences).then(function(data) {
				$scope.administrator.preferences = data;
			});
		};
		
	} ]);