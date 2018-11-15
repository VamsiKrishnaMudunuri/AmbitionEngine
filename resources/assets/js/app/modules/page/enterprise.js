$(function() {
    var $imgs = $('#imgs');
    var photos = $imgs.data('photos');

    var classes = [
        'gallery-top',
        'gallery-left',
        'gallery-right',
        'gallery-most-right',
    ];

    clickableImg($imgs, null, {
        images: photos,
        cells: 4,
        align: false,
        loading: skin.loading.sm,
        onGridRendered: function($grid) {

            // need to hack a lil bit as the the grid rendering is not as simple as
            // horizontal row, these galleries's grid using a different layout for
            // rendering
            $grid.find('.imgs-grid-image').each(function(i, elem) {

                var imageWrap = $(elem).find('.image-wrap');
                var img = imageWrap.find('img');
                var container = $('.' + classes[i]);

                container
                    .css({
                        'backgroundImage': 'url(' + img.attr('src') + ')',
                        cursor: 'pointer'
                    })
                    .html($(elem))
                    .find('img')
                    .attr('src', '')
                    .end()
                    .find('.image-wrap').height((container).height());

            });

            $grid.remove()
        }

    });
});