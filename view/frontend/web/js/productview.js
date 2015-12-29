define(['jquery'], function($) {
    function processGuestProductView(productId) {
        var productviews = [];
        if ($.cookie('productviews')) {
            productviews = $.cookie('productviews');
        }

        productviews += productId + ',';
        $.cookie('productviews', productviews);
    }

    function processProductView(viewUrl) {
        $.ajax({url: viewUrl, success: function(data){
        }});
    }

    return function (config, element) {
        processGuestProductView(config.productId);
        processProductView(config.productViewUrl);
    };
});
