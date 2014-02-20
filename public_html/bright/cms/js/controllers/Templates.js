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
        {value: 'type', headerTemplate: $scope.hTemplate, label: 'Type', width: '30px', template: '<i class="fa fa-{{row.type|templatetypeicon}}"></i>'}];


    $scope.onGridDoubleClick = function(evt) {
    	$state.transitionTo('template', {templateId: evt.templateId});
    };

}]);