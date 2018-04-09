jQuery(document).ready(function ($) {
    var remove_image = $('.fed_remove_image');
    if (remove_image.length) {
        $('body').on('click', '.fed_remove_image', function (e) {
            console.log('y');
            var closest = $(this).closest('.fed_upload_wrapper');
            closest.find('.fed_upload_input').val('');
            closest.find('.fed_upload_image_container').html('<div class="fed_upload_image_container"><span class="fed_upload_icon fa fa-2x fa fa fa-upload"></span></div>');
            e.preventDefault();
        });
    }
});