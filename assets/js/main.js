// Wait for document to be ready
$(document).ready(function() {
    // Add smooth scrolling
    $('a[href*="#"]').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
        }, 500, 'linear');
    });

    // Add fade-in animation to cards
    $('.card').addClass('fade-in');

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle account deleted modal
    if ($('#accountDeletedModal').length) {
        $('#accountDeletedModal').modal('show');
        history.replaceState({}, document.title, window.location.pathname);
    }

    // Responsive navbar collapse
    $('.navbar-toggler').on('click', function() {
        $('.navbar-collapse').collapse('toggle');
    });
});