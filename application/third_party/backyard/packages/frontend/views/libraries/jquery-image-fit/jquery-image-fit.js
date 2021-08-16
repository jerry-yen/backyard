(function($) {

    /**
     * 圖片跟著框自動等比縮放
     */
    $.fn.image_fit = function() {
        var container = $(this);
        var image = $('img', container);

        var containerWidth = container.width();
        var containerHeight = container.height();

        console.log(image);
        console.log(containerWidth);

        var imageWidth = image.width();
        var imageHeight = image.height();


        // 橫向圖片
        if (imageWidth > imageHeight) {
            var rate = imageWidth / containerWidth;
            console.log('rate=' + rate);
            imageWidth = containerWidth;
            imageHeight = imageHeight / rate;
        }
        // 直向圖片
        else {
            var rate = imageHeight / containerHeight;
            imageWidth = imageWidth * rate;
            imageHeight = containerHeight;
        }

        image.css({
            'width': imageWidth + 'px',
            'height': imageHeight + 'px'
        });

        // 當圖片載入完成
        $('img').load(function() {

        });
    };
}(jQuery));