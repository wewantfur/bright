'use strict';

/* States */

angular.module('states', []).config(
		[ '$stateProvider', '$routeProvider', '$urlRouterProvider',
				function($stateProvider, $routeProvider, $urlRouterProvider) {

					$routeProvider.when('/', {
						template : '<p class="lead">Welcome to Bright!</p>'
					});

					$stateProvider.state('files', {
						url : '/files',
						templateUrl : 'partials/files.html',
						controller : 'filesCtrl'
					})

					.state('templates', {
						url : '/templates',
						templateUrl : 'partials/templates.html',
						controller : 'templatesCtrl'
					})

					.state('administrators', {
						url : '/administrators',
						templateUrl : 'partials/administrators.html',
						controller : 'administratorsCtrl'
					})

					.state('group', {
						url : '/groups/{GID}',
						templateUrl : 'partials/groups-edit.html',
						controller : 'groupCtrl'
					})

					.state('pages', {
						url : '/pages',
						abstract : true,
						templateUrl : 'partials/pages.html',
						controller : 'pageTreeCtrl'
					})

					.state('pages.list', {
						url : '',
						title : 'Pages',
						controller : 'pagesCtrl',
						templateUrl : 'partials/pages-list.html'

					}).state('pages.edit', {
						url : '/{type}/{pageId}',
						controller : 'contentCtrl',
						templateUrl : 'partials/pages-edit.html'

					});
				} ]);

// .state('events', {
// url: '/events',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
// .state('maps', {
// url: '/maps',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
// .state('users', {
// url: '/users',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
// .state('files', {
// url: '/files',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
// .state('administrators', {
// url: '/administrators',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
// .state('templates', {
// url: '/templates',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
// .state('settings', {
// url: '/settings',
// abstract: true,
// templateUrl: 'partials/unimplemented.html',
// controller: ['$scope','$http','$state', brightCtrl]})
