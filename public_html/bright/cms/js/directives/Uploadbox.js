bright = angular.module('bright');
bright.directive('uploadbox', function() {
	return {
		restrict: 'E',
		link: function(scope, element, attr, ctrl) {
			$('html,body').on('dragenter dragleave', function(e) {
				e.stopPropagation();
				e.preventDefault();
				$(element).css("visibility", "visible");
			});
			$(element).on('dragover', function(e) {
				e.stopPropagation();
				e.preventDefault();
			});
			
			$(element).on('drop', function(e) {
				$(element).css("visibility", "hidden");
				e.stopPropagation();
				e.preventDefault();
				var files = e.originalEvent.dataTransfer.files;
				if (files.length > 0) {
					scope.$apply(function(){
						//scope.uploads = [];
						for (var i = 0; i < files.length; i++) {
							scope.uploads.push(files[i]);
						}
					});
					
					var fd = new FormData();
					angular.forEach(scope.uploads, function(item,i) {
						if(!item.isUploading) {
							item.isUploading = true;
							item.error = false;
							fd.append('dir', scope.folder);
							fd.append('uploadedFile', item);
							var xhr = new XMLHttpRequest();
							xhr.upload.addEventListener("progress", function(evt) {
								scope.$apply(function(){
//									scope.uploads[i].progress = Math.round((evt.position / evt.total) * 100);
//									scope.uploads[i].cssprogress = "width: " + scope.uploads[i].progress + "%";
									item.progress = Math.round((evt.position / evt.total) * 100);
									item.cssprogress = "width: " + item.progress + "%";
								});
							}, false);
							xhr.addEventListener("load", function(evt) {
								switch(evt.currentTarget.status) {
									case 200:
										// Success!
										scope.$apply(function(){
											item.complete = true;
											scope.files.push({name:evt.currentTarget.responseText, selected: false});
										});
										break;
									//case 413:
									default:
										// Upload too big
										scope.$apply(function(){
											item.error = true;
										});
										break;
								}
							}, false);
							xhr.addEventListener("error", function(evt) {
								console.log('error', evt);
							}, false);
							xhr.addEventListener("abort", function(evt) {
								console.log('abort', evt);
							}, false);
							xhr.open("POST", "/breeze/controllers/UploadCtrl.php");
							xhr.send(fd);
						}
					});
				}
			});
		}
	};
});