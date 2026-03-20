$(document).ready(function() {
    // Password Toggle using jQuery
    $(document).on('click', '.password-toggle', function() {
        const icon = $(this);
        const inputId = icon.attr('data-target');
        const input = $('#' + inputId);

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Logout Confirmation using jQuery
    $(document).on('click', '.logout-link', function(e) {
        if (!confirm("Are you sure you want to log out?")) {
            e.preventDefault();
        }
    });
});