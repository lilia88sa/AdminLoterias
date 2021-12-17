/**
 * Created by Frank on 19/05/2020.
 */

/* global $ */
$(document).ready(function () {

    'use strict';

    // Initialize the jQuery File UploadService widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'server/php/'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(/\/[^/]*$/, '/cors/result.html?%s')
    );

// Change this to the location of your server-side upload handler:
    $('#fileupload').fileupload({
        // Setup Doka Image Editor:
        doka: Doka.create(),
        edit:
        Doka.supported() &&
        function(file) {
            return this.doka.edit(file).then(function(output) {
                return output && output.file;
            });
        },
        autoUpload: false,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 999000,

        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
           .test(window.navigator.userAgent),
        // previewMaxWidth: 120,
        // previewMaxHeight: 120,
        // previewCrop: true,

        //disableImageResize:false,
        // imageMaxWidth: 1024,
        // imageMaxHeight: 1024,
        // imageCrop: true // Force cropped images
    });

    $('#fileupload').bind('fileuploadsubmit', function (e, data) {
        var inputs = data.context.find(':input');
        if (inputs.filter(function () {
                return !this.value && $(this).prop('required');
            }).first().focus().length) {
            data.context.find('button').prop('disabled', false);
            return false;
        }
        //console.log(inputs.serializeArray());
        //data.formData = inputs.serializeArray();
        data.formData = $('#fileupload').serializeArray();
    });

    // Load existing files:
    $('#fileupload').addClass('fileupload-processing');
    var user_id_photo = $('#user_id_photo').val();
    //console.log($('#fileupload').attr('action'));
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        data: {
            user_id_photo : user_id_photo
        },
        dataType: 'json',
        context: $('#fileupload')[0]
    })
        .always(function() {
            $(this).removeClass('fileupload-processing');
        })
        .done(function(result) {
            $(this)
                .fileupload('option', 'done')
                .call(this, $.Event('done'), { result: result });
        });

});

