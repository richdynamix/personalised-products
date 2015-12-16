define(['jquery'], function($) {
    function processProductView(productId) {
        var productviews = [];
        if ($.cookie('productviews')) {
            productviews = $.cookie('productviews');
        }

        productviews += productId + ',';
        $.cookie('productviews', productviews);
    }

    return function (config, element) {
        processProductView(config.productId);
    };
});
