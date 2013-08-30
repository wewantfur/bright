/**
 * Template overview controller
 */
bright = angular.module('bright');
bright.controller("TemplatesCtrl", 
	['$scope', '$http', '$state', 'templateService', 
	function($scope, $http, $state, templateService) {
		
		templateService.getTemplates().then(function ( templates ) {
			  $scope.templates = templates;
		});
		
		$scope.gridOptions = {data: 'templates', height: '100%',
								enableColumnResize: true,
								keepLastSelected: false,
								showSelectionCheckbox: true,
								multiSelect: true,
								plugins: [new ngGridFlexibleHeightPlugin()],
				columnDefs: [{field:'icon', displayName:'', width: '30px', cellTemplate:'<div class="ngCellText" ng-class="col.colIndex()"><i class="tpl-icon tpl-{{row.getProperty(col.field)}}"></i></div>'},
			                  {field:'templateId', displayName:'#', width: '50px'},
			                  {field:'label', displayName:'Label', width: '20%'},
			                  {field:'displaylabel', displayName:'Display label', width: '20%'},
			                  {field:'type', displayName:'Type', width: '30px', cellTemplate:'<div class="ngCellText" ng-class="col.colIndex()"><i class="tpl-icon tpl-{{getType(row.getProperty(col.field))}}"></i></div>}'}]};
		
		$scope.getType = function(type) {
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