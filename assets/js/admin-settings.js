(function($) {
    'use strict';

    $(document).ready(function() {
        $('.size-input').on('input', function() {
            var $input = $(this);
            var value = $input.val();
            var $preview = $input.closest('.size-field-container').find('.size-preview');
            
            $preview.css('font-size', value + 'px');
            $preview.find('.preview-size').text(value + 'px');
        });

        $('.size-input').on('change', function() {
            var $input = $(this);
            var value = parseInt($input.val(), 10);
            var min = parseInt($input.attr('min'), 10);
            var max = parseInt($input.attr('max'), 10);

            if (value < min) {
                $input.val(min);
            } else if (value > max) {
                $input.val(max);
            }

            var $preview = $input.closest('.size-field-container').find('.size-preview');
            $preview.css('font-size', $input.val() + 'px');
            $preview.find('.preview-size').text($input.val() + 'px');
        });
    });
})(jQuery);
