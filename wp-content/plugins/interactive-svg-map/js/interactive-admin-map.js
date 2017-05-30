// A $( document ).ready() block.
jQuery(document).ready(function () {

    (function (jQuery) {
        jQuery(function () {

            if (jQuery('.mp_background').length) {
                jQuery('.mp_background').wpColorPicker();
            }
            
            if (jQuery('.mp_border').length) {
                jQuery('.mp_border').wpColorPicker();
            }
            
            if (jQuery('.mp_region').length) {
                jQuery('.mp_region').wpColorPicker();
            }
            
            if (jQuery('.mp_region_hover').length) {
                jQuery('.mp_region_hover').wpColorPicker();
            }
            
            if (jQuery('.mp_color').length) {
                jQuery('.mp_color').wpColorPicker();
            }
            
            if (jQuery('.mp_selected_color').length) {
                jQuery('.mp_selected_color').wpColorPicker();
            }

        });
    })(jQuery);
});