$(document).ready(function() {
  // Cache frequently used selectors
  const $alert = $('.alert');
  const $navbarCollapse = $('.navbar-collapse');
  const $navbarNav = $('.navbar-nav');
  const $navItem = $('.nav-item');
  const $dashboardBtn = $('.navbar-nav .btn-danger');
  const $tableRows = $('.table tbody tr');
  const $deptHeadCards = $('.dept-head-card');
  const $deleteForm = $('form[name="delete-form"]');
  
  // Auto-hide alerts after 5 seconds
  if ($alert.length) {
    setTimeout(function() {
      $alert.fadeOut('slow');
    }, 5000);
  }
  
  // Fix navbar responsiveness for all devices
  function fixNavbarResponsiveness() {
    const isMobile = window.innerWidth <= 991;
    
    if (isMobile) {
      // Mobile styles
      $navbarCollapse.css({
        'background': 'linear-gradient(to right, #1e3c72, #2a5298)',
        'padding': '10px',
        'border-radius': '0 0 10px 10px',
        'margin-top': '5px',
        'box-shadow': '0 5px 15px rgba(0, 0, 0, 0.1)'
      });
      
      $navbarNav.css({
        'width': '100%',
        'padding': '5px 0'
      });
      
      $navItem.css({
        'margin': '5px 0',
        'text-align': 'center'
      });
    } else {
      // Desktop styles - reset
      $navbarCollapse.add($navbarNav).add($navItem).css({
        'background': '',
        'padding': '',
        'border-radius': '',
        'margin-top': '',
        'box-shadow': '',
        'width': '',
        'margin': '',
        'text-align': ''
      });
    }
  }
  
  // Fix navbar toggle button behavior
  $('.navbar-toggler').on('click', function() {
    $navbarCollapse.css('max-height', $navbarCollapse.hasClass('show') ? '0' : '300px');
  });
  
  // Dashboard button hover effects
  $dashboardBtn.hover(
    function() {
      $(this).css({
        'transform': 'translateY(-2px)',
        'box-shadow': '0 5px 15px rgba(220, 53, 69, 0.4)',
        'transition': 'all 0.3s ease'
      });
    },
    function() {
      $(this).css({
        'transform': '',
        'box-shadow': '',
        'transition': 'all 0.3s ease'
      });
    }
  );
  
  // Dashboard button click/touch effects
  $dashboardBtn
    .on('mousedown touchstart', function() {
      $(this).css({
        'transform': 'translateY(1px)',
        'box-shadow': '0 2px 5px rgba(220, 53, 69, 0.4)',
        'transition': 'all 0.1s ease'
      });
    })
    .on('mouseup', function() {
      $(this).css({
        'transform': 'translateY(-2px)',
        'box-shadow': '0 5px 15px rgba(220, 53, 69, 0.4)',
        'transition': 'all 0.1s ease'
      });
    })
    .on('touchend', function() {
      const $btn = $(this);
      setTimeout(function() {
        $btn.css({
          'transform': '',
          'box-shadow': '',
          'transition': 'all 0.3s ease'
        });
      }, 300);
    });
  
  // Ensure dashboard button is responsive on different screen sizes
  function adjustDashboardButton() {
    const screenWidth = window.innerWidth;
    
    if (screenWidth <= 576) {
      $dashboardBtn.css({
        'padding': '6px 12px',
        'font-size': '0.9rem',
        'display': 'inline-block',
        'width': '100%',
        'text-align': 'center'
      });
    } else if (screenWidth <= 768) {
      $dashboardBtn.css({
        'padding': '7px 14px',
        'font-size': '0.95rem',
        'display': 'inline-block',
        'width': 'auto'
      });
    } else {
      $dashboardBtn.css({
        'padding': '8px 15px',
        'font-size': '1rem',
        'display': 'inline-block',
        'width': 'auto'
      });
    }
  }
  
  // Apply hover effects to table rows
  function applyTableRowHoverEffects() {
    if ($tableRows.length === 0) return;
    
    $tableRows.hover(
      function() {
        const $row = $(this);
        $row.find('.user-avatar').css('background', 'linear-gradient(to right, #4e73df, #224abe)');
        $row.find('.dept-badge').css('box-shadow', '0 0 10px rgba(54, 185, 204, 0.5)');
        $row.find('.btn-gradient-danger').css({
          'transform': 'translateY(-2px)',
          'box-shadow': '0 5px 15px rgba(231, 74, 59, 0.4)'
        });
      },
      function() {
        const $row = $(this);
        $row.find('.user-avatar').css('background', '#4e73df');
        $row.find('.dept-badge').css('box-shadow', 'none');
        $row.find('.btn-gradient-danger').css({
          'transform': '',
          'box-shadow': ''
        });
      }
    );
  }
  
  // Apply hover effects to mobile cards
  function applyCardHoverEffects() {
    if ($deptHeadCards.length === 0) return;
    
    $deptHeadCards.hover(
      function() {
        const $card = $(this);
        $card.find('.user-avatar').css('background', 'linear-gradient(to right, #4e73df, #224abe)');
        $card.find('.dept-badge').css('box-shadow', '0 0 10px rgba(54, 185, 204, 0.5)');
        $card.find('.btn-gradient-danger').css({
          'transform': 'translateY(-2px)',
          'box-shadow': '0 5px 15px rgba(231, 74, 59, 0.4)'
        });
      },
      function() {
        const $card = $(this);
        $card.find('.user-avatar').css('background', '#4e73df');
        $card.find('.dept-badge').css('box-shadow', 'none');
        $card.find('.btn-gradient-danger').css({
          'transform': '',
          'box-shadow': ''
        });
      }
    );
  }
  
  // Touch device support for mobile cards
  function setupTouchInteractions() {
    if ($deptHeadCards.length === 0) return;
    
    $deptHeadCards
      .on('touchstart', function() {
        const $card = $(this);
        $card.addClass('touch-active');
        $card.css({
          'transform': 'translateY(-3px)',
          'box-shadow': '0 8px 25px rgba(0, 0, 0, 0.1)',
          'border-left': '3px solid #4e73df'
        });
        
        $card.find('.user-avatar').css({
          'background': 'linear-gradient(to right, #4e73df, #224abe)',
          'transform': 'scale(1.05)'
        });
        
        $card.find('.dept-badge').css('box-shadow', '0 0 10px rgba(54, 185, 204, 0.5)');
      })
      .on('touchend', function() {
        const $card = $(this);
        setTimeout(function() {
          $card.removeClass('touch-active');
          $card.css({
            'transform': '',
            'box-shadow': '',
            'border-left': ''
          });
          
          $card.find('.user-avatar').css({
            'background': '#4e73df',
            'transform': ''
          });
          
          $card.find('.dept-badge').css('box-shadow', 'none');
        }, 300);
      });
  }
  
  // Add confirmation for delete
  function setupDeleteConfirmation() {
    $deleteForm.on('submit', function(e) {
      if (!confirm('Are you sure you want to delete this department head?')) {
        e.preventDefault();
      }
    });
  }
  
  // Initialize all functions
  function init() {
    fixNavbarResponsiveness();
    adjustDashboardButton();
    applyTableRowHoverEffects();
    applyCardHoverEffects();
    setupTouchInteractions();
    setupDeleteConfirmation();
  }
  
  // Run on page load
  init();
  
  // Handle window resize events efficiently using debounce
  let resizeTimer;
  $(window).on('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
      fixNavbarResponsiveness();
      adjustDashboardButton();
    }, 250);
  });
});