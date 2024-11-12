(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize tabs
        $('#text-size-tabs').tabs();

        // Handle size input changes
        $('.size-input').on('input', function() {
            var $input = $(this);
            var value = $input.val();
            var $preview = $input.closest('.size-field-container').find('.size-preview');
            
            $preview.css('font-size', value + 'px');
            $preview.find('.preview-size').text(value + 'px');
        });

        // Validate input values on change
        $('.size-input').on('change', function() {
            var $input = $(this);
            var value = parseInt($input.val(), 10);
            var min = parseInt($input.attr('min'), 10);
            var max = parseInt($input.attr('max'), 10);

            // Ensure value is within bounds
            if (value < min) {
                $input.val(min);
            } else if (value > max) {
                $input.val(max);
            }

            // Update preview
            var $preview = $input.closest('.size-field-container').find('.size-preview');
            $preview.css('font-size', $input.val() + 'px');
            $preview.find('.preview-size').text($input.val() + 'px');
        });

        // Store active tab in sessionStorage
        $('#text-size-tabs').on('tabsactivate', function(event, ui) {
            sessionStorage.setItem('activeTextSizeTab', ui.newTab.index());
        });

        // Restore active tab from sessionStorage
        var activeTab = sessionStorage.getItem('activeTextSizeTab');
        if (activeTab !== null) {
            $('#text-size-tabs').tabs('option', 'active', parseInt(activeTab, 10));
        }
    });
})(jQuery);
