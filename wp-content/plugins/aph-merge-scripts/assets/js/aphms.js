jQuery(document).ready(function($)
{
	var $panel = $('#aphms-panel').hide();
	$('#wp-admin-bar-aphms-admin-bar').click(function()
	{
		$panel.slideToggle();
	});
	
	$('#aphms-adminbar-showinfo').click(function(){
		$('#aphms-adminbar-info').fadeToggle();
		return false;
	});

	$panel.find('a.submit').click(function()
	{
		$submit = $(this);
		if ($submit.hasClass('button-disabled'))
			return false;

		$submit.addClass('button-disabled');
		
		var $wrap = $('#aphms-submit-wrapper'),
			$loader = $wrap.find('.aphms-loader').show(),
			$msg 	= $wrap.find('.aphms-msg').html('').hide();
		
		$.ajax({
			type: 'POST',
			url: aphms.ajaxurl,
			dataType: 'text',
			data: {
				action: 'aphms-save-assets',
				nonce: aphms.nonce,
				input: $('#aphms-form').serialize()
			},
			success: function(r) {
				$loader.hide();
				console.log(r);
				$obj = $.parseJSON(r);
				$msg.html($obj.data.msg).fadeIn('fast');
				setTimeout(function()
				{
					$msg.fadeOut('fast');
					$submit.removeClass('button-disabled');
				}, 4000);
				
			},
			error: function(r) {
				$loader.hide();
				$submit.removeClass('button-disabled');
				$msg.html('An error accured').fadeIn('fast');
			}
		});
		return false;
	});
	
	var $not_merged = $('#aphms-notmerged');
	if ($not_merged.length > 0)
	{
		$not_merged.show().appendTo($('#wp-admin-bar-aphms-admin-bar').children().eq(0));
	}
});