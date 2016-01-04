define([
    'jquery',
    'upsellProducts'
], function($) {

    function getUpsellContent(url) {
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function (data) {
            $('#personalised-upsells-container').html(data).promise().done(function(){
                $('.upsell').upsellProducts();
            });
        });
    }

    return function (config, element) {
        getUpsellContent(config.upsellAjaxUrl);
    };
});
