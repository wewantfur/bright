/**
 * Template overview controller
 */
bright = angular.module('bright');
bright.controller("templatesCtrl",
        ['$scope', '$http', '$state', 'TemplateService',
            function($scope, $http, $state, TemplateService) {

                TemplateService.getTemplates().then(function(templates) {
                    $scope.templates = templates;
                });

            }]);