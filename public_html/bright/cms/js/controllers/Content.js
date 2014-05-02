/**
 * The ContentController handles the editing of pages, events, users, everything that is editable.
 * @version 1.0
 * @author ids - Fur
 */
var bright = angular.module('bright');
bright.controller('contentCtrl', 
	['$scope', '$http', '$state', 'ConfigService', 'ContentService', 'TemplateService', 'PageService',
	 function($scope, $http, $state, ConfigService, ContentService, TemplateService, PageService) {
		
		$scope.content = null;
		$scope.templates = null;
//		$scope.settings = null;

		// Fetch data
		TemplateService.getTemplates().then(function(data) {	$scope.templates = data; });
		
		switch($state.params.type) {
			case 'page':
				/**
				 * @todo Move this to a service to allow switching between views and keeping data 
				 */ 
				$http.post('/bright/json/core/content/Pages/getPage', {arguments:[$state.params.pageId]}).success(function(data) {
					if(data.status == 'OK') {
						$scope.content = data.result;
					} else {
						console.log(":'(",data);
						
					}
				}).error(function(data) {
					console.log(":'(",data);
				});
				break;
		}
		
		$scope.getContent = function(lang, field) {
			console.log(lang, field);
		};
		
		$scope.getLanguage = function(lang) {
			return ConfigService.getLanguageName(lang);
		};
		
		$scope.save = function() {
			console.log($scope.content);
			switch($state.params.type) {
				case 'page':
					PageService.setPage($scope.content).then(function(d) {
						console.log(d);
					});
			}
		};
		$scope.cancel = function() {

			switch($state.params.type) {
				case 'page':
					$state.transitionTo('pages.list');
					break;
			}
		};
		
		$scope.setSelectedTemplate = function() {
			
			if($scope.content && $scope.templates) {
				angular.forEach($scope.templates, function(template) {
					if(template.templateId == $scope.content.templateId) {
						$scope.content.template = template;
					}
				});
			}
		};
		
		
		$scope.$watch(function combinedWatch() {
			// Add data which is needed before initialization
			return {
					content: $scope.content,
					settings: $scope.settings,
					templates: $scope.templates};
		}, function(value, old) {

			if( value.templates == null ||  value.settings == null || value.content == null || typeof(value.content) == 'undefined' || 
				typeof(value.templates) == 'undefined' || typeof(value.settings) == 'undefined') {
					// Not all data ready
					return;
			}
//			console.log(old, value);
//			if(old.content) {
//				console.log(old.content.pageId, value.content.pageId);
//				console.log(old.content.templateId, value.content.templateId);
//			}
			if(!old.content || (old.content && (value.content.pageId != old.content.pageId || value.content.templateId != old.content.templateId))) {
				console.log('page or template changed');
				$scope.setSelectedTemplate();
				
				if(Number($scope.content.templateId) > 0) {
					// Get the full template
					TemplateService.getTemplate($scope.content.templateId).then(function(data) {
						$scope.template = data;
					});
				}
			}
				
		}, true);
	}]);