'use strict';

/* States */

angular.module('states', []).config(['$stateProvider', '$routeProvider', '$urlRouterProvider', 
                                     function ($stateProvider,   $routeProvider,   $urlRouterProvider) {

	$routeProvider
	.when('/', {
		template: '<p class="lead">Welcome to Bright!</p>',
	});
	
	$stateProvider
		.state('files', {
			url: '/files',
            templateUrl: 'partials/files.html',
			controller: 'FilesCtrl'
		})
		
		.state('templates', {
			url: '/templates',
			templateUrl: 'partials/templates.html',
			controller: 'TemplatesCtrl'
		})

    	.state('pages', {
			url : '/pages',
			abstract : true,
			templateUrl : 'partials/pages.html',
			controller : 'PageTreeCtrl'
		})

		.state('pages.list', {
			url : '',
			title:'Pages',
			controller : 'PagesCtrl',
			templateUrl : 'partials/pages-list.html',

		})
        .state('pages.edit', {
        	url:'/{type}/{pageId}',
			controller : 'ContentCtrl',
        	templateUrl : 'partials/pages-edit.html',

        })
        ;
}]);

//		.state('events', {
//			url: '/events',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})
//		.state('maps', {
//			url: '/maps',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})
//		.state('users', {
//			url: '/users',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})
//		.state('files', {
//			url: '/files',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})
//		.state('administrators', {
//			url: '/administrators',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})
//		.state('templates', {
//			url: '/templates',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})
//		.state('settings', {
//			url: '/settings',
//			abstract: true,
//			templateUrl: 'partials/unimplemented.html',
//			controller: ['$scope','$http','$state', BrightCtrl]})