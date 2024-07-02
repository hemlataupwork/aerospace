jQuery(document).ready(function($) {
    // Your jQuery code using $ as an alias for jQuery
    $('.password-field').each(function() {
        var passInput = $(this).find('.form-control');
        var passToggle = $(this).find('.password-toggle > i');

        passToggle.on('click', function() {
            if (passInput.attr('type') === "password") {
                passInput.attr('type', 'text');
                passToggle.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passInput.attr('type', 'password');
                passToggle.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
});
