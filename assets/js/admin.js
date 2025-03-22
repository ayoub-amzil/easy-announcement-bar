jQuery(document).ready(function($){
    $('.color-picker').wpColorPicker();

    $('input[name="easy_announcement_bar_settings[speed]"]').on('change', function() {
        let val = Math.max(5, Math.min(60, $(this).val()));
        $(this).val(val);
    });
});