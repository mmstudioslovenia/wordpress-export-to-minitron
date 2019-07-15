jQuery(document).ready(function($) {
    jQuery(document).on('submit', '.wpmt_form', function(event) {
        event.preventDefault();
        var checkedGroups = $('.wpmt_checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        var userEmail = jQuery(this).find('.wpmt_input_email').val();
        var userName = jQuery(this).find('.wpmt_input_name').val();
        var userMobile = jQuery(this).find('.wpmt_input_mobile').val();
        var form = jQuery(this);
        var formBtn = jQuery(this).find('.wpmt_btn');
        var btnText = formBtn.text();
        var successText = formBtn.data('success');
        var failedText = jQuery(this).data('failed');
        var invalidEmail = jQuery(this).data('invalid');
        var emailPattern = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        jQuery('p.wpmt_result').remove();
        
        if (emailPattern.test(userEmail)) {
            form.addClass('wpmt_sending');
            form.find('button').attr('disabled', 'disabled');
            jQuery.ajax({
                    url: wpmt_ajax.url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'minitron_add_subscriber',
                        nonce: wpmt_ajax.nonce,
                        email: userEmail,
                        name: userName,
                        mobile: userMobile,
                        groups: checkedGroups
                    }
                })
                .done(function(data) {
                    form.removeClass('wpmt_sending');
                    form.find('button').attr('disabled', false);
                    console.log(data);
                    if (data.success == true) {
                        form.after('<p class="wpmt_result success">' + successText + '</p>')
                    } else {
                        formBtn.text(btnText);
                        form.after('<p class="wpmt_result failed">' + failedText + '</p>')
                    }

                })
                .fail(function() {
                    form.removeClass('wpmt_sending');
                    form.find('button').attr('disabled', false);
                    formBtn.text(btnText);
                    form.after('<p class="wpmt_result failed">' + failedText + '</p>')
                })

        } else {
            jQuery(this).after('<p class="wpmt_result failed">' + invalidEmail + '</p>')
        }
    });
});