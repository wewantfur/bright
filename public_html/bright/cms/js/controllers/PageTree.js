bright = angular.module('bright');
bright.controller("pageTreeCtrl", [ '$scope', '$state', 'PageService', 'ConfigService',
	function($scope, $state, PageService,ConfigService) {
		PageService.getPages().then(function(pages) {
			
			if(typeof pages.tree != 'array') {
				$scope.pagetree = [pages.tree];
			} else {
				$scope.pagetree = pages.tree;
			}
		});

		
		/**
		 * Toggle for opening and closing a leaf
		 */
		$scope.pageOpenClose = function() {
			var item = this.item;
			item.isopen = !item.isopen;
		};
		
		$scope.hasChildren = function() {
			return (this.item.children && this.item.children.length > 0);
		};
		
		$scope.pageSelect = function() {
			this.item.selected = true;
//			$state.transitionTo('pages.edit', {pageId:this.item.pageId, type:'page'});
		};
		
		$scope.pageEdit = function() {
			$state.transitionTo('pages.edit', {pageId:this.item.pageId, type:'page'});
		};

		$scope.updateWidths = function(widths) {
			$scope.updatePreferences('pages.dividers', widths);
		};
		
	} ]);