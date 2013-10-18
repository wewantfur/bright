/**
 * Template overview controller
 */
bright = angular.module('bright');
bright.controller("templatesCtrl", 
	['$scope', '$http', '$state', 'TemplateService', 
	function($scope, $http, $state, TemplateService) {
		
		TemplateService.getTemplates().then(function ( templates ) {
			  $scope.templates = templates;
		});
//		
//		$scope.gridOptions = {data: 'templates', height: '100%',
//								enableColumnResize: true,
//								keepLastSelected: false,
//								showSelectionCheckbox: true,
//								multiSelect: true,
//								plugins: [new ngGridFlexibleHeightPlugin()],
		$scope.columns = [{value:'icon', headerTemplate:$scope.hTemplate, label:'', width: '30px', template:'<i class="tpl-icon tpl-{{row.icon}}"></i>'},
			              {value:'templateId', headerTemplate:$scope.hTemplate, label:'#', width: '50px'},
			              {value:'label', headerTemplate:$scope.hTemplate, label:'Label', width: '20%'},
			              {value:'displaylabel', headerTemplate:$scope.hTemplate, label:'Display label', width: '20%'},
			              {value:'type', headerTemplate:$scope.hTemplate, label:'Type', width: '30px', template:'<i class="tpl-icon tpl-type-{{row.type}}"></i>'}];
		
		$scope.getType = function(type) {
			console.log('Getting type', type);
			switch(type) {
				case 1:	return 'page_white'; 		// page
				case 2:	return 'page_white_stack'; 	// list
				case 3:	return 'date'; 				// event
				case 4:	return 'brick';				// element
				case 5:	return 'map';				// marker
				case 6:	return 'user';				// user
				
			}
		};
		/*
	const TYPE_PAGE = 1;
	const TYPE_LIST = 2;
	const TYPE_EVENT = 3;
	const TYPE_MARKER = 4;
	const TYPE_USER = 5;*/
	}]);