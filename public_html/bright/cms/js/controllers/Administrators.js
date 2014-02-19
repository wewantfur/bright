bright = angular.module('bright');
bright.controller("administratorsCtrl", ['$scope', '$state', 'AdministratorService',
    function($scope, $state, AdministratorService) {
        $scope.columns = [{value: 'UID', headerTemplate: $scope.hTemplate},
            {value: 'email', headerTemplate: $scope.hTemplate},
            {value: 'name', headerTemplate: $scope.hTemplate},
            {value: 'lastlogin', headerTemplate: $scope.hTemplate}];
        $scope.groupcolumns = [{value: 'locked', headerTemplate: '<span></span>', template: '<span><i class="tpl-icon tpl-lock" ng-show="row.locked"></i></span>'},
            {value: 'GID', headerTemplate: $scope.hTemplate},
            {value: 'name', headerTemplate: $scope.hTemplate}];

        AdministratorService.getBEUsers().then(function(users) {
            $scope.administrators = users;
        });

        AdministratorService.getBEGroups().then(function(groups) {
            $scope.groups = groups;
        });

        $scope.onGroupDblClick = function(evt) {
            if (evt.locked != 1) {
                $state.transitionTo('group', {GID: evt.GID});
            } else {
                // @todo Show notification
            }
        };
    }]);