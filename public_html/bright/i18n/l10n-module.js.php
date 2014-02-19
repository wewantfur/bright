<?php header("Content-type: application/javascript")?>
angular.module('my-l10n-nl', ['l10n']).config(['l10nProvider', function(l10n){
    l10n.add('nl-nl', <?php echo file_get_contents(dirname(__FILE__) . '/nl.json')?>);
}])