bright = angular.module('bright');
bright.controller("PagesCtrl", 
	['$scope', '$http', '$state', 'pageService',
	function($scope, $http, $state, pageService) {
		
		$scope.columns = [{label:'icon', displayLabel:'', width: '20px'},
		                  {label:'pageId', displayLabel:'#', width: '20px'},
		                  {label:'label', displayLabel:'Label', width: '20%'},
		                  {label:'modificationdate', displayLabel:'Last modified', width: '20%'},
		                  {label:'modifiedby', displayLabel:'Modified by', width: '20%'}
		                  ];
		
		pageService.getPages().then(function(pages) {
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
		
		$scope.$on('tableColumnTextClick', function( event, arg) {
			switch(arg.column) {
				case 'label':
					$state.transitionTo('pages.edit', {pageId:arg.item.pageId, type:'page'});
					break;
					
			}
		});
		
		$scope.editPage = function(pageId) {
			$state.transitionTo('pages.edit', {pageId:pageId, type:'page'});
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
				console.log(item.pageId, this.page.pageId,item.pageId == this.page.pageId)
				if(item.pageId == this.page.pageId)
					return true;
			});
			return false;
		};
	}])
	
//.controller("TreeCtrl", 
//	['$scope', '$http', '$state', 'pageService',
//	function($scope, $http, $state, pageService) {
//		
////		$scope.$watch( function () { return pageService.getTree(); }, function ( result ) {
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
		['$scope', '$http', '$state', 'pageService', 'templateService',
		 function($scope, $http, $state, pageService, templateService) {
			$scope.$watch( function () { return templateService.getTemplates(); }, function ( templates ) {
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