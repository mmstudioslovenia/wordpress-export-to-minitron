(function($) {
    'use strict';

    $('.wpmt_select2').select2({
        placeholder: wpmt_ajax.select_groups,
        allowClear: true
    });
    
    $(document).on('click', '#start-manually', function(event)
    {
        var data = {
			'action': 'batch_update'
		};
        
        $.post(ajaxurl, data, function(response) {
            var response = JSON.parse(response);
			if (response)
            {
                $.each(response, function (index, value) {
                    setTimeout(function() {
                        
                        console.log(value);
                        var dt = new Date();
                        var currentHours = dt.getHours();
                        currentHours = ("0" + currentHours).slice(-2);
                        
                        var currentSeconds = dt.getSeconds();
                        currentSeconds = ("0" + currentSeconds).slice(-2);
                        
                        var time = currentHours + ":" + dt.getMinutes() + ":" + currentSeconds;
                    
                        // Draw div with data from the response
                        var msgClass = (value.ok) ? 'green' : 'red';
                        var html = '<div>';
                            html += '<span class="time">' + time + '</span>';
                            html += '<strong><em>#' + (index + 1) + '</em> '+ value.email +' </strong>';
                            html += '<span class="msg ' + msgClass + '">' + value.msg + '</span>';
                        html += '</div>';
                        
                        $('#wizard-console').append(html);
                        
                    }, 800 * index);
                });
            }
		});
    });

})(jQuery);
