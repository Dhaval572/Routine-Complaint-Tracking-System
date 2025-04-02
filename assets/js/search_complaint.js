document.addEventListener('DOMContentLoaded', function() {
    initializeAlerts();
});

function initializeAlerts() {
    const alerts = document.querySelectorAll('.custom-alert');
    
    alerts.forEach(alert => {
        // Initialize Bootstrap alert
        new bootstrap.Alert(alert);
        
        // Auto-close after 5 seconds
        setTimeout(() => {
            fadeOutAlert(alert);
        }, 5000);
        
        // Handle close button
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                fadeOutAlert(alert);
            });
        }
    });
}

function fadeOutAlert(alert) {
    alert.style.animation = 'slideOutAlert 0.5s forwards';
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 500);
}