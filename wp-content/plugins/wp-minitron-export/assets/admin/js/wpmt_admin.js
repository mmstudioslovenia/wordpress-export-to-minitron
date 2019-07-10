(function($) {
    'use strict';

    $('.wpmt_select2').select2({
        placeholder: wpmt_ajax.select_groups,
        allowClear: true
    });
    
    $(document).on('click', '#start-manually', function(event)
    {
        var data = {
			'action': 'start_manually'
		};
        
        $.post(ajaxurl, data, function(response) {
			console.log(response);
		});
    });

})(jQuery);
