define(['jquery'], function($) {
    function processProductView(baseUrl) {

        $.ajax({
            type: 'GET',
            cache: false,
            url: baseUrl,
            success: function(data) {
                console.log(data);
            },
            error: function() {
                console.log('An error occurred recording the product view');
            }
        });
    }

    return function (config, element) {
        processProductView(config.productViewUrl);
    };
});
