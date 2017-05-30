(function($) {
$(document).ready(function($)
{
	/* $('#aphms-merge-script-option').change(function()
	{
		var $this = $(this),
			$next_tr = $this.parents('tr').eq(0).next();
		if ($this.val() == 0)
		{
			$('#aphms-minify-script-options').fadeOut('fast');
			$next_tr.fadeOut('fast');
		} else {
			$('#aphms-minify-script-options').fadeIn('fast');
			$next_tr.fadeIn('fast');
		}
	}); */
	
	$('#aphms-option-minify').change(function(){
		if ($(this).val() == 'minify-yui')
		{
			$('#aphms-check-exec').show();
		} else {
			$('#aphms-check-exec').hide();
		}
	});
	
	$('a.aphms-show-handles').click(function(){
		
		$(this).parent().next().fadeToggle();
		return false;
	})
	$('#aphms-check-exec').click(function(){
		$.ajax({
			type: 'POST',
			url: ajaxurl,
			dataType: 'text',
			data: {
				action: 'aphms-check-exec',
				nonce: aphms.nonce
			},
			success: function(r) {
				
				console.log(r);
				
			},
			error: function(r) {
				$loader.hide();
				$submit.removeClass('button-disabled');
				$msg.html('An error accured').fadeIn('fast');
			}
		});
		return false;
	});
	
	$('#aphms-use-remote-files').change(function()
	{
		if (this.value == 3)
		{
			$('#aphms-local-copy-day').fadeIn('fast');
		} else {
			$('#aphms-local-copy-day').fadeOut('fast');
		}
	});
	
	$('#aphms-show-local-copy').click(function()
	{
		$('#aphms-list-local-copy').fadeToggle();
		return false;
	})
	
});
})(jQuery);