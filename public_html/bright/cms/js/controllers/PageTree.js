bright = angular.module('bright');
bright.controller("PageTreeCtrl", [ '$scope', '$state', 'pageService',
	function($scope, $state, pageService) {
		pageService.getPages().then(function(pages) {
			
			if(typeof pages.tree != 'array') {
				$scope.pagetree = [pages.tree];
			} else {
				$scope.pagetree = pages.tree;
			}
		});
		
		$scope.pageOpenClose = function() {
			var item = this.item;
			item.isopen = !item.isopen;
		};
		
		$scope.hasChildren = function() {
			return (this.item.children && this.item.children.length > 0);
		};
		
		$scope.pageSelect = function() {
			$state.transitionTo('pages.edit', {pageId:this.item.pageId, type:'page'});
		};
		
		setTimeout(function() {
			$('.divider').divider({widths: [25,75]});
			
		}, 100);
	} ]);