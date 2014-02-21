var dnd_app = angular.module('dragDrop', []);

dnd_app.directive('draggable', function() {
	return function(scope, element) {
		// this gives us the native JS object
		var el = element[0];

		el.draggable = true;

		el.addEventListener('dragstart', function(e) {
			var siblings = el.parentNode.getElementsByTagName(el.nodeName);
			var i = 0;
			while(siblings[i] != this && i < siblings.length) {
				i++;
			}
				
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setDragImage(document.getElementById('dummy'),-5,-5);
			e.dataTransfer.setData('oldIndex', i);
			this.classList.add('drag');
			
		}, false);

		el.addEventListener('dragend', function(e) {
			this.classList.remove('drag');
			e.preventDefault();
		}, false);
	};
});

dnd_app.directive('droppable', function() {
	return {
		scope : {
			drop : '&',
			bin : '='
		},
		link : function(scope, element) {
			// again we need the native object
			var el = element[0];
			var prevY = null;

			el.addEventListener('dragover', function(e) {
				e.dataTransfer.dropEffect = 'move';
				// allows us to drop
				if (e.preventDefault)
					e.preventDefault();
				
				this.classList.add('over');
				e.preventDefault();
			}, false);

			el.addEventListener('dragenter', function(e) {
				this.classList.add('over');
				var direction = prevY && prevY > e.pageY ? 'up' : 'down';
				prevY = e.pageY;
				var targetNode = document.elementFromPoint(e.pageX, e.pageY);
				var original = this.querySelector('.drag');
				
				while(targetNode.nodeName.toUpperCase() != original.nodeName.toUpperCase() && targetNode != el) {
					targetNode = targetNode.parentElement;
				}
				
				if(targetNode != el) {
					if(direction == 'down') {
						targetNode = targetNode.nextSibling;
						while(targetNode != null && targetNode.nodeName.toUpperCase() != original.nodeName.toUpperCase()) {
							targetNode = targetNode.nextSibling;
						}
					}
					var siblings = el.getElementsByTagName(original.nodeName);
					var i = 0;
					while(siblings[i] != targetNode && i < siblings.length) {
						i++;
					}
					if(direction == 'down')
						i--;
					
					scope.newIndex = i;
					this.insertBefore(original, targetNode);
				}
				
				e.preventDefault();
			}, false);

			el.addEventListener('dragleave', function(e) {
				this.classList.remove('over');
				e.preventDefault();
			}, false);

			el.addEventListener('drop', function(e) {
				// Stops some browsers from redirecting.
				if (e.stopPropagation)
					e.stopPropagation();

				this.classList.remove('over');

				var oldIndex = e.dataTransfer.getData('oldIndex');
				var newIndex = scope.newIndex;
				
				// call the passed drop function
				scope.$apply(function(scope) {
					var fn = scope.drop();
					if ('undefined' !== typeof fn) {
						fn(oldIndex, newIndex);
					}
				});

				e.preventDefault();
			}, false);
		}
	};
});

dnd_app.controller('DragDropCtrl', function($scope) {
	$scope.handleDrop = function(item, bin) {
		alert('Item ' + item + ' has been dropped into ' + bin);
	};
});