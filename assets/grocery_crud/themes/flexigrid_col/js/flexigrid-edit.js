;
(function ($) {
    $(document).ready(function ($) {
        $('[data-fancybox="quickAdd"]').fancybox({
            modal: true,
            arrows: false,
            closeExisting: false,
            toolbar: false,
            smallBtn: false,
            clickSlide: false,
            clickOutside: false,
            beforeClose: function () {
                //console.log('beforeClose');
            },
            iframe: {
                preload: true
            },
            baseTpl: '<div class="fancybox-container fbQuickAdd" role="dialog" tabindex="-1">' +
                    '<div class="fancybox-bg"></div>' +
                    '<div class="fancybox-inner">' +
                    '<div class="fancybox-infobar"><span data-fancybox-index></span>&nbsp;/&nbsp;<span data-fancybox-count></span></div>' +
                    '<div class="fancybox-toolbar">{{buttons}}</div>' +
                    '<div class="fancybox-navigation">{{arrows}}</div>' +
                    '<div class="fancybox-stage"></div>' +
                    '<div class="fancybox-caption"></div>' +
                    "</div>" +
                    "</div>"
        });
    });
})(jQuery);

$(function () {
    $('input[name="' + focus + '"]').focus();
    $('button[type="submit"]').click(function () {
        $('input[name="' + focus + '"]').focus();
    });
    $('input, textarea').addClass('form-control');
    var save_and_close = false;

    $('.ptogtitle').click(function () {
        if ($(this).hasClass('vsble'))
        {
            $(this).removeClass('vsble');
            $('#main-table-box #crudForm').slideDown("slow");
        } else
        {
            $(this).addClass('vsble');
            $('#main-table-box #crudForm').slideUp("slow");
        }
    });

    $('#save-and-go-back-button').click(function () {
        save_and_close = true;

        $('#crudForm').trigger('submit');
    });

    $('#crudForm').submit(function () {
        var my_crud_form = $(this);

        $(this).ajaxSubmit({
            url: validation_url,
            dataType: 'json',
            cache: 'false',
            beforeSend: function () {
                $("#FormLoading").show();
            },
            success: function (data) {
                $("#FormLoading").hide();
                if (data.success)
                {
                    $('#crudForm').ajaxSubmit({
                        dataType: 'text',
                        cache: 'false',
                        beforeSend: function () {
                            $("#FormLoading").show();
                        },
                        success: function (result) {

                            $("#FormLoading").fadeOut("slow");
                            data = $.parseJSON(result);
                            if (data.success)
                            {
                                var data_unique_hash = my_crud_form.closest(".flexigrid").attr("data-unique-hash");

                                $('.flexigrid[data-unique-hash=' + data_unique_hash + ']').find('.ajax_refresh_and_loading').trigger('click');

                                if (save_and_close)
                                {
                                    if ($('#save-and-go-back-button').closest('.ui-dialog').length === 0) {
                                        window.location = data.success_list_url;
                                    } else {
                                        $(".ui-dialog-content").dialog("close");
                                        form_success_message(data.success_message);
                                    }

                                    return true;
                                }

                                form_success_message(data.success_message);
                            } else
                            {
                                form_error_message(message_update_error);
                            }
                        },
                        error: function () {
                            form_error_message(message_update_error);
                        }
                    });
                } else
                {
                    $('.field_error').each(function () {
                        $(this).removeClass('field_error');
                    });
                    $('#report-error').slideUp('fast');
                    $('#report-error').html(data.error_message);
                    $.each(data.error_fields, function (index, value) {
                        $('input[name=' + index + ']').addClass('field_error');
                    });

                    $('#report-error').slideDown('normal');
                    $('#report-success').slideUp('fast').html('');

                }
            },
            error: function () {
                alertify.alert(message_update_error);
                $("#FormLoading").hide();

            }
        });
        return false;
    });

    if ($('#cancel-button').closest('.ui-dialog').length === 0) {

        $('#cancel-button').click(function () {

            if ($(this).hasClass('back-to-list'))
            {
                window.location = list_url;
            } else {
                alertify.confirm(message_alert_edit_form,
                        function () {
                            window.location = list_url;
                        });
            }

            return false;
        });

    }
});
