$(function() {
    var $instances = $('.fenrizbes-crop-bundle-instance');

    $instances.each(function() {
        var $instance = $(this);
        var $image    = $instance.find('img');
        var $left     = $instance.find('input.fenrizbes-crop-bundle-instance-left');
        var $top      = $instance.find('input.fenrizbes-crop-bundle-instance-top');
        var $width    = $instance.find('input.fenrizbes-crop-bundle-instance-width');
        var $height   = $instance.find('input.fenrizbes-crop-bundle-instance-height');

        var initial_data = {
            left:   parseInt($left.val()),
            top:    parseInt($top.val()),
            width:  parseInt($width.val()),
            height: parseInt($height.val())
        };

        $image
            .cropper({
                checkImageOrigin: false,
                rotatable:        false,
                zoomable:         false,
                responsive:       false,
                background:       false,
                aspectRatio:      initial_data.width / initial_data.height,
                minCropBoxWidth:  parseInt($image.data('min-width')),
                minCropBoxHeight: parseInt($image.data('min-height')),

                crop: function(data) {
                    $left.val(Math.round(data.x));
                    $top.val(Math.round(data.y));
                    $width.val(Math.round(data.width));
                    $height.val(Math.round(data.height));
                }
            })
            .on('built.cropper', function() {
                var cropper = $image.data('cropper');
                var ratio   = cropper.image.width / cropper.image.naturalWidth;

                cropper.cropBox.minWidth  = cropper.options.minCropBoxWidth  * ratio;
                cropper.cropBox.minHeight = cropper.options.minCropBoxHeight * ratio;

                cropper.setCropBoxData({
                    left:   initial_data.left   * ratio + cropper.cropBox.minLeft,
                    top:    initial_data.top    * ratio + cropper.cropBox.minTop,
                    width:  initial_data.width  * ratio,
                    height: initial_data.height * ratio
                });
            });
    });
});