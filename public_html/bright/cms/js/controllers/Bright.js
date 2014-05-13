bright = angular.module('bright');
bright.controller("brightCtrl", [ '$scope', 'ConfigService', 'l10n',
                                  
	function($scope, ConfigService, l10n) {
		$scope.hTemplate = '<span>{{col.value|headername}}</span>';

		ConfigService.getSettings().then(function(data) { $scope.settings = data; });
		
		/**
		 * Updates the user preferences (divider widths, sorting, etc)
		 * @param string prop The property to update
		 * @param mixed value The new value of the property
		 */
		$scope.updatePreferences = function(prop, value) {
			
			if($scope.administrator.preferences == null) {
				$scope.administrator.preferences = {};
			}
			
			var props = prop.split('.');
			var property = props.pop();
			var prefs = $scope.administrator.preferences;
			
			for(var i = 0; i < props.length; i++) {
				if(!prefs.hasOwnProperty(props[i]))
					prefs[props[i]] = {};
				
				prefs = prefs[props[i]];
			}
			
			prefs[property] = value;
			
			ConfigService.setPreferences($scope.administrator.preferences).then(function(data) {
				$scope.administrator.preferences = data;
			});
		};
		
		$scope.getPreference = function(prop, defaultValue) {
			if(!$scope.administrator)
                return null;

			if($scope.administrator.preferences == null)
				return defaultValue;
			
			var props = prop.split('.');
			var prefs = $scope.administrator.preferences;
			
			for(var i = 0; i < props.length; i++) {
				if(!prefs.hasOwnProperty(props[i]))
					return defaultValue;
				
				prefs = prefs[props[i]];
			}
			
			return prefs;
		};
		
	} ]);