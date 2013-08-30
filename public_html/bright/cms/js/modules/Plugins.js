var PluginModule = angular.module('Plugin', []);

PluginModule.factory('PluginAPI', function() {
    return {
        status: null,
        data: null,
        message: null,
        validate: function(data) {
            this.status = 'success';
            this.message = msg;
        },
        error: function(msg) {
            this.status = 'error';
            this.message = msg;
        },
        clear: function() {
            this.status = null;
            this.message = null;
        }
    };
});