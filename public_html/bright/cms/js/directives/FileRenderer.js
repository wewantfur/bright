angular.module('bright').directive('fileRenderer', function() {
	return {
		restrict : 'A',
		scope: false,
		link : function($scope, elem, attrs) {
			console.log($scope, attrs);
			$scope.$watch(function() { return attrs.displayMode}, function(val){
				if(val == 'grid') {
					switch(attrs.ext) {
						case "jpg":
						case "jpeg":
						case "png":
						case "gif":
						case "pdf":
							$(elem).css({background: 'url("' + attrs.filename + '")'});
							break;
					}
					
				} else {
					$(elem).attr('style', '');
				}
			}, true);
		}
	};
});