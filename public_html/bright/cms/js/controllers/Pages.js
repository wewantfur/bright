bright = angular.module('bright');
bright.controller("pagesCtrl", 
	['$scope', '$http', '$state', 'PageService',
	function($scope, $http, $state, PageService) {
		
		$scope.columns = [{value:'icon', label:' ', headerTemplate:$scope.hTemplate, width: '20px', template: '<i class="tpl-icon tpl-{{row.icon}}" />'},
		                  {value:'pageId', label:'#', headerTemplate:$scope.hTemplate, width: '20px'},
		                  {value:'label', label:'Label', headerTemplate:$scope.hTemplate, width: '20%'},
		                  {value:'modificationdate', headerTemplate:$scope.hTemplate, label:'Last modified', width: '20%', template: '<span>{{row.modificationdate | date:\'short\'}}</span>'},
		                  {value:'modifiedby', headerTemplate:$scope.hTemplate, label:'Modified by', width: '20%', template: '<span>{{row.modifiedby|adminname}}</span>'}
		                  ];
		
		PageService.getPages().then(function(pages) {
			$scope.pages = pages.list;
		});
		$scope.gridOptions = {data: 'pages', height: '100%',
							columnDefs: [{field:'icon', displayName:'', width: '30px', cellTemplate:'<div class="ngCellText" ng-class="col.colIndex()"><i class="tpl-icon tpl-{{row.getProperty(col.field)}}"></i></div>'},
						                  {field:'pageId', displayName:'#', width: '50px'},
						                  {field:'label', displayName:'Label', width: '20%'},
						                  {field:'modificationdate', displayName:'Last modified', width: '20%'},
						                  {field:'modifiedby', displayName:'Modified by', width: '20%'}]};
		
		$scope.hasSelection = false;
	
		$scope.orderProp = "pageId";
		$scope.reversed = false;
		
		$scope.$on('rowDoubleClick', function( event, row) {
			$state.transitionTo('pages.edit', {pageId:row.pageId, type:'page'});
		});
		
		$scope.editPage = function(pageId) {
			$state.transitionTo('pages.edit', {pageId:pageId, type:'page'});
		};

		$scope.getDate = function(date) {
			return 'Datum!';
		};
		
		$scope.getLabel = function() {
			if(this.page.hasOwnProperty(this.column.label))
				return this.page[this.column.label];
			
			return ":'(";
		};
		
		$scope.selectPage = function() {
			this.page.selected = !this.page.selected;

			$scope.hasSelection = this.page.selected;
		};
		
		$scope.setSort = function(column) {
			if($scope.orderProp != column) {
				$scope.orderProp = column;
				$scope.reversed = false;
			} else {
				$scope.reversed = !$scope.reversed;
			}
		};
		
		$scope.tableSort = function(page) {
			switch($scope.orderProp) {
				case 'pageId':
					return Number(page[$scope.orderProp]);
				
			}
			return page[$scope.orderProp];
		};
		
		$scope.inSelection = function() {
			angular.forEach($scope.selection, function(item) {
				if(item.pageId == this.page.pageId)
					return true;
			});
			return false;
		};
	}])
	
//.controller("TreeCtrl", 
//	['$scope', '$http', '$state', 'PageService',
//	function($scope, $http, $state, PageService) {
//		
////		$scope.$watch( function () { return PageService.getTree(); }, function ( result ) {
////			if (result) {
////				$scope.pagetree = result;
////			}
////		});
//	
//		$scope.pageOpenClose = function() {
//			var item = this.item;
//			item.isopen = !item.isopen;
//		};
//		
//		$scope.hasChildren = function() {
//			return (this.item.children && this.item.children.length > 0);
//		};
//		
//		$scope.pageSelect = function() {
//			$state.transitionTo('pages.edit', {pageId:this.item.pageId, type:'page'});
//		};
//		
//		setTimeout(function() {
//			$('.divider').divider({widths: [25,75]});
//			
//		}, 100);
//	}])
	
.controller("PageCtrl", 
		['$scope', '$http', '$state', 'PageService', 'TemplateService',
		 function($scope, $http, $state, PageService, TemplateService) {
			$scope.$watch( function () { return TemplateService.getTemplates(); }, function ( templates ) {
				  $scope.templates = templates;
			});
			
			$http.post('/bright/json/core/content/Pages/getPage', {arguments:[$state.params.pageId]}).success(function(data) {
				if(data.status == 'OK') {
					$scope.page = data.result;
				}
			});
			
			$scope.isSelectedTemplate = function() {
				console.log('isSelectedTemplate ' + this.tpl.templateId +'=='+ $scope.page.templateId);
				return (this.tpl.templateId == $scope.page.templateId);
			};
		}]);