/**
 * Template edit controller. 
 * 
 */
bright = angular.module('bright');
bright.controller("templateCtrl",
['$scope', '$http', '$state', 'TemplateService', 'l10n', '$modal',
function($scope, $http, $state, TemplateService, l10n, $modal) {
	
	$scope.templateTypes = [{id: 1, label: l10n.get('templates.templatetype1')},
	                        {id: 2, label: l10n.get('templates.templatetype2')},
	                        {id: 3, label: l10n.get('templates.templatetype3')},
	                        {id: 4, label: l10n.get('templates.templatetype4')},
	                        {id: 5, label: l10n.get('templates.templatetype5')},
	                        {id: 6, label: l10n.get('templates.templatetype6')},
	                        {id: 7, label: l10n.get('templates.templatetype7')}];
	TemplateService.getFieldTypes().then(function(fields) {
		$scope.fieldtypes = fields;
		
		TemplateService.getTemplate($state.params.templateId).then(function(template) {
			$scope.template = template;
			if(template.hasOwnProperty('type') && template.type != null)
				$scope.template.templateType = $scope.templateTypes[template.type-1];
			
			angular.forEach($scope.template.fields, function(templatefield, key) {
				angular.forEach($scope.fieldtypes, function (field, fkey) {
					if(field.label == templatefield.fieldtype) {
						templatefield.field = field;
					}
				});
			});
		});
	});
	
	/**
	 * Rearranges the fields
	 * @param int oldIndex
	 * @param int newIndex
	 */
	$scope.onFieldDrop = function(oldIndex, newIndex) {
		$scope.template.fields.move(oldIndex, newIndex);
	};

	/**
	 * Adds a new field to the template
	 */
	$scope.onAddFieldClick = function() {
		$scope.template.fields.push({label:'',displaylabel:''});
		console.log('addfield',$scope.template.fields);
	};
	
	/**
	 * Removes the selected field
	 */
	$scope.onRemoveFieldClick = function() {
		$scope.template.fields.splice(this.$index, 1);
	};
    
	/**
	 * Changes the icon to the selected value
	 */
    $scope.selectIcon = function() {
    	var modalInstance = $modal.open({
			templateUrl : 'partials/icons.html',
			controller : 'iconCtrl',
		});
		
	    modalInstance.result.then(function (selectedItem) {
	    	
	    	if(selectedItem != null)
	    		$scope.template.icon = selectedItem;
		});
    };

}]);