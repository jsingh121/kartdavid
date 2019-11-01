require(["jquery", 'jquery/ui', "domReady!"], function ($) {

    $(document).on('click', 'span.design-removal', function (e) {
        var designId = $(this).attr('design-id');
        var designElement = $(this);
        designElement.css('pointer-events', 'none');

        $.ajax({
            type: "POST",
            url: "/customiser/design/delete/",
            data: {design_id: designId},
            complete: function (data) {
                if (data.responseJSON && data.responseJSON.error_status) {
                    $('.design-images .message').html(data.responseJSON.message).show();
                    $("html, body").animate({
                        scrollTop: 0
                    }, 500);

                    $('.design-images .message').delay(2500).fadeOut(800);
                } else {
                    designElement.parent().fadeOut(1500);
                }
            }
        });
    });

    $(document).on('mouseover', 'div.design-column', function () {
        $(this).css('background', '#f5f5f5');
    });

    $(document).on('mouseleave', 'div.design-column', function () {
        $(this).css('background', '#fff');
    });

    $(document).on('click', '.design-column > img', function () {
        var productId = $(this).next().attr('product-id');
        var designId = $(this).next().attr('design-id');

        $('#design-redirect #product-id').val(productId);
        $('#design-redirect #design-id').val(designId);
        $('#design-redirect').submit();
    });

});