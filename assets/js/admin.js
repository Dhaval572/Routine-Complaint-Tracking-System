$(document).ready(function() {
  // Auto-hide alerts after 5 seconds
  setTimeout(function() {
    $('.alert').fadeOut('slow');
  }, 5000);
  
  // Add hover effect to table rows
  $('tr').hover(
    function() {
      $(this).css({
        'background-color': '#f8f9fc',
        'transform': 'scale(1.01)',
        'box-shadow': '0 5px 15px rgba(0, 0, 0, 0.05)'
      });
    },
    function() {
      $(this).css({
        'background-color': '',
        'transform': '',
        'box-shadow': ''
      });
    }
  );
  
  // Add hover effect to nav links
  $('.nav-link').hover(
    function() {
      $(this).css({
        'background-color': 'rgba(255, 255, 255, 0.15)',
        'color': 'white',
        'transform': 'translateY(-2px)'
      });
    },
    function() {
      $(this).css({
        'background-color': '',
        'transform': ''
      });
    }
  );
  
  // Add hover effect to delete button
  $('.btn-gradient-danger').hover(
    function() {
      $(this).css({
        'transform': 'translateY(-2px)',
        'box-shadow': '0 6px 15px rgba(231, 74, 59, 0.4)'
      });
    },
    function() {
      $(this).css({
        'transform': '',
        'box-shadow': '0 4px 10px rgba(231, 74, 59, 0.3)'
      });
    }
  );
  
  // Add hover effect to back button
  $('.btn-gradient-secondary').hover(
    function() {
      $(this).css({
        'transform': 'translateY(-3px)',
        'box-shadow': '0 6px 15px rgba(108, 117, 125, 0.4)'
      });
    },
    function() {
      $(this).css({
        'transform': '',
        'box-shadow': '0 4px 10px rgba(108, 117, 125, 0.3)'
      });
    }
  );
  
  // Confirm delete
  $('form[name="delete-form"]').on('submit', function(e) {
    if (!confirm('Are you sure you want to delete this department head?')) {
      e.preventDefault();
    }
  });
});