(function($) {
	"use strict";
	$(document).ready(function(){		
		var range1 = $('.range-box-font-size'),
			value1 = $('.range-show-font-size');
		value1.html(range1.attr('value') + 'px');
		range1.on('input', function(){
			value1.html(this.value + 'px');
		}); 

		var range2 = $('.range-box-line-height'),
			value2 = $('.range-show-line-height');
		value2.html(range2.attr('value') + 'px');
		range2.on('input', function(){
			value2.html(this.value + 'px');
		});
		
		var range3 = $('.range-box-box-width'),
			value3 = $('.range-show-box-width');
		value3.html(range3.attr('value') + 'px');
		range3.on('input', function(){
			value3.html(this.value + 'px');
		});
		
		var range4 = $('.range-box-border-width'),
			value4 = $('.range-show-border-width');
		value4.html(range4.attr('value') + 'px');
		range4.on('input', function(){
			value4.html(this.value + 'px');
		});
		
		var range5 = $('.range-box-border-radius'),
			value5 = $('.range-show-border-radius');
		value5.html(range5.attr('value') + 'px');
		range5.on('input', function(){
			value5.html(this.value + 'px');
		});
		
		var range6 = $('.range-box-distance-bottom'),
			value6 = $('.range-show-distance-bottom');
		value6.html(range6.attr('value') + '%');
		range6.on('input', function(){
			value6.html(this.value + '%');
		});
		
		var range7 = $('.range-box-distance-leftright'),
			value7 = $('.range-show-distance-leftright');
		value7.html(range7.attr('value') + 'px');
		range7.on('input', function(){
			value7.html(this.value + 'px');
		});
		
		var range8 = $('.range-box-excerpt-words'),
			value8 = $('.range-show-excerpt-words');
		value8.html(range8.attr('value') + ' words');
		range8.on('input', function(){
			value8.html(this.value + ' words');
		});
		
		var range9 = $('.range-box-image-width'),
			value9 = $('.range-show-image-width');
		value9.html(range9.attr('value') + 'px');
		range9.on('input', function(){
			value9.html(this.value + 'px');
		});
		
		var range10 = $('.range-box-image-height'),
			value10 = $('.range-show-image-height');
		value10.html(range10.attr('value') + 'px');
		range10.on('input', function(){
			value10.html(this.value + 'px');
		});

	});
})(jQuery);