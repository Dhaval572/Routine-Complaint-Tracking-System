document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap alerts
    var alertList = document.querySelectorAll('.alert');
    alertList.forEach(function (alert) {
        new bootstrap.Alert(alert);
    });

    // Auto-close alerts after 5 seconds
    setTimeout(function () {
        alertList.forEach(function (alert) {
            fadeOutAlert(alert);
        });
    }, 5000);

    // Add fade out animation for close button
    alertList.forEach(function (alert) {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                fadeOutAlert(alert);
            });
        }
    });

    // Function to fade out alerts
    function fadeOutAlert(alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-10px)';
        setTimeout(function () {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 500);
    }

    // Add hover effects to info cards
    const infoCards = document.querySelectorAll('.info-card');
    infoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Add pulse animation to search button
    const searchBtn = document.querySelector('.btn-search');
    if (searchBtn) {
        searchBtn.classList.add('animate-pulse');
    }
});