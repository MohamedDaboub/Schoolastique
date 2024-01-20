jQuery(document).ready(function($) {
    $('#display_on_home').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).val('on');
        } else {
            $(this).val('off');
        }
    });
});
