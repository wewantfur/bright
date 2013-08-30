(function($) {
	$.fn.divider = function(options) {
		
		if(this.hasClass('fur-divider'))
			return this;
		
		var settings = $.extend( {
		      'widths'         : []
		    }, options);
		
		var widths = [];
		var self = this;
		this.addClass('fur-divider');
		var usesettingswidth = false;
		
		if(settings.widths.length == this.children('div').length) {
			var total = 0;
			for (var i = 0; i < settings.widths.length; i++) {
			    total += parseInt(settings.widths[i]);
			}
			
			if(Math.round(total / 100) == 1)
				usesettingswidth = true;
				
		}
		var w = this.width() / this.children('div').length;
		this.children('div').each(function(index, el){
			if(usesettingswidth) {
				w = self.width() * (settings.widths[index] / 100);
			}
			var ow = $(this).addClass('pull-left').css("width", w + "px").outerWidth();
			w -= (ow-w);
			// Correct width with padding
			$(this).addClass('pull-left').css("width", w - 10 + "px");
		});
		this.children().each(function(index, el) {
			$(this).after('<q><span>||</span></q>');
		});
		// Remove last divider
		this.children().last().remove();
		
		this.on('mousedown.divider', 'q', function(de) {
			// Store clicked divider
			var q = $(this);
			self.addClass('unselectable');
			// Total width LEFT of divider, exlcuding first div LEFT of divider 
			var lw = 0;
			// Total width RIGHT of divider, exlcuding first div RIGHT of divider
			var rw = 0;
			q.prev().prevAll().each(function(index,el) {
				lw += $(el).width();
			});
			q.next().nextAll().each(function(index,el) {
				rw += $(el).width();
			});
			
			widths = [];
			self.children('div').each(function() {
				// store percentual widths of divs
				widths.push($(this).width() / self.width());
			});
			self.on('mousemove.divider', function (me) {
				// Get all previous divs
				var twl = me.pageX - self.offset().left; // total width left
				var nw = q.prev().width() - (twl-lw);
				console.log(twl, lw, nw);
				q.prev().width(twl-lw);
				q.next().width(q.next().width() + nw);
			});
		});
		
		this.on('mouseup.divider', function(ue) {
			self.removeClass('unselectable');
			self.off('mousemove.divider');
		});
		
		return this;
	};
})(jQuery);