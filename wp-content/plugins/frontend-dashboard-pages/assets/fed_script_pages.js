jQuery(document).ready(function ($) {
        var b = $('.bc_fed');
        b.on('click', 'input[name=fed_menu_key]', function (e) {
            var click = $(this);
            click.closest('.fed_pages_menu_item_container').find('.fed_page_menu_item').toggleClass('hide');
        });
    }
);
