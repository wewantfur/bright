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

                $scope.editTemplate = function() {
                    console.log('GetTemplate');
                    alert('boink');
                    //$state.transitionTo('templates', {templateId: evt});
                };

                $scope.columns = [{value: 'icon', headerTemplate: $scope.hTemplate, label: '', width: '30px', template: '<i class="fa fa-{{row.icon}}"></i>'},
                    {value: 'templateId', headerTemplate: $scope.hTemplate, label: '#', width: '50px'},
                    {value: 'label', headerTemplate: $scope.hTemplate, label: 'Label', width: '20%'},
                    {value: 'displaylabel', headerTemplate: $scope.hTemplate, label: 'Display label', width: '20%'},
                    {value: 'type', headerTemplate: $scope.hTemplate, label: 'Type', width: '30px', template: '<i class="fa fa-{{getType(row.type)}}"></i>'}];

                $scope.getType = function(type) {
                    console.log('Getting type', type);
                    switch (type) {
                        case 1:
                            return 'file-text'; 		// page
                        case 2:
                            return 'th-list';		 	// list
                        case 3:
                            return 'calendar'; 			// event
                        case 4:
                            return 'puzzle-piece';		// element
                        case 5:
                            return 'map-marker';		// marker
                        case 6:
                            return 'user';				// user

                    }
                };

            }]);