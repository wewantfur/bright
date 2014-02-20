'use strict';

/* Directives */
angular
		.module('directives', [])
		.directive('stopEvent', function() {
			return {
				restrict : 'A',
				link : function(scope, element, attr) {
					element.bind(attr.stopEvent, function(e) {
						e.stopPropagation();
					});
				}
			};
		})
		.directive(
				'scrollFix',
				function($window) {
					return function(scope, element, attrs) {
						var windowEl = angular.element($window);
						windowEl
								.on(
										'scroll',
										function() {
											$('.listheader').css({
												top : 'inherit',
												position : ''
											});

											if ($('.listheader').offset()
													&& $('body').offset()
													&& windowEl.scrollTop() > ($(
															'.listheader')
															.offset().top - $(
															'body').offset().top) + 12) {

												$('.listheader').css({
													top : '60px',
													position : 'fixed'
												});
											} else {
												$('.listheader').css({
													top : 'inherit',
													position : ''
												});
											}
										});
					};
				})
		.directive(
				'tabs',
				function() {
					return {
						restrict : 'E',
						transclude : true,
						scope : {},
						controller : function($scope, $element) {
							var panes = $scope.panes = [];

							$scope.select = function(pane) {
								angular.forEach(panes, function(pane) {
									pane.selected = false;
								});
								pane.selected = true;
							}

							this.addPane = function(pane) {
								if (panes.length == 0)
									$scope.select(pane);
								panes.push(pane);
							}
						},
						template : '<div class="tabbable">'
								+ '<ul class="nav nav-tabs">'
								+ '<li ng-repeat="pane in panes" ng-class="{active:pane.selected}">'
								+ '<a href="" ng-click="select(pane)">{{pane.title}}</a>'
								+ '</li>'
								+ '</ul>'
								+ '<div class="tab-content" ng-transclude></div>'
								+ '</div>',
						replace : true
					};
				})

		.directive(
				'pane',
				function() {
					return {
						require : '^tabs',
						restrict : 'E',
						transclude : true,
						scope : {
							title : '@'
						},
						link : function(scope, element, attrs, tabsCtrl) {
							tabsCtrl.addPane(scope);
						},
						template : '<div class="tab-pane" ng-class="{active: selected}" ng-transclude>'
								+ '</div>',
						replace : true
					};
				});