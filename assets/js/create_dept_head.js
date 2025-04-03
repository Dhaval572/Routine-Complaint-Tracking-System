$(document).ready(function () {
  // Add loading animation
  const form = $('form');
  const submitBtn = form.find('button[type="submit"]');
  // Store the original button text for later restoration
  const originalButtonText = submitBtn.html();
  
  // Auto-hide alerts after 5 seconds
  setTimeout(function () {
    $('.alert:not(#signatureHelp)').fadeOut('slow');
  }, 5000);

  // Update file input label with filename
  $('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
  });

  // Add animation to modal
  $('#successModal').on('show.bs.modal', function () {
    $(this).find('.modal-content')
      .addClass('animate__animated animate__zoomIn');
  });
  
  // Add input field animations
  $('.form-control').on('focus', function() {
    $(this).closest('.form-group').find('label').addClass('text-primary');
    $(this).closest('.input-group').find('.input-group-text').addClass('bg-primary');
  }).on('blur', function() {
    if (!$(this).val()) {
      $(this).closest('.form-group').find('label').removeClass('text-primary');
      $(this).closest('.input-group').find('.input-group-text').removeClass('bg-primary');
    }
  });
  
  // Add form submission animation
  form.on('submit', function() {
    submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
    submitBtn.attr('disabled', true);
    
    // We'll add a small delay to show the animation (in a real app, this would be removed)
    setTimeout(function() {
      submitBtn.html(originalBtnText);
      submitBtn.attr('disabled', false);
    }, 3000);
  });
  
  // Add password validation
  const passwordInput = $('input[name="password"]');
  const passwordHelp = passwordInput.closest('.form-group').find('.form-text');
  
  passwordInput.on('keyup', function() {
    const length = $(this).val().length;
    
    if (length === 6 || length === 10) {
      $(this).removeClass('is-invalid').addClass('is-valid');
      passwordHelp.removeClass('text-danger').addClass('text-success');
      passwordHelp.html('<i class="fas fa-check-circle mr-1"></i>Password length is valid.');
    } else {
      $(this).removeClass('is-valid').addClass('is-invalid');
      passwordHelp.removeClass('text-success').addClass('text-danger');
      passwordHelp.html('<i class="fas fa-exclamation-circle mr-1"></i>Password must be exactly 6 or 10 characters long.');
    }
  });
  
  // Add department selection animation
  $('select[name="department_id"]').on('change', function() {
    if ($(this).val()) {
      $(this).addClass('is-valid');
      $(this).closest('.input-group').find('.input-group-text').addClass('bg-success');
    } else {
      $(this).removeClass('is-valid');
      $(this).closest('.input-group').find('.input-group-text').removeClass('bg-success');
    }
  });
});

// Function to preview signature image
function previewSignature(input) {
  var previewContainer = $('#signaturePreviewContainer');
  var preview = $('#signaturePreview');
  var fileNameSpan = $('#signatureFileName');
  var signatureHelp = $('#signatureHelp');
  
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      preview.attr('src', e.target.result);
      fileNameSpan.text(input.files[0].name + ' uploaded successfully');
      previewContainer.show();
      
      // Remove the signature help element completely from the DOM
      signatureHelp.remove();
      
      // Add animation
      previewContainer.addClass('animate__animated animate__fadeIn');
      
      // Add success indicator to file input
      $(input).addClass('is-valid');
      $(input).closest('.custom-file').find('.custom-file-label').addClass('border-success');
    }
    
    reader.readAsDataURL(input.files[0]);
  } else {
    previewContainer.hide();
    
    // If the signature help element doesn't exist anymore, recreate it
    if ($('#signatureHelp').length === 0) {
      var newSignatureHelp = $('<div id="signatureHelp" class="alert alert-info mt-2 d-flex align-items-center" role="alert">' +
        '<div class="mr-3 text-primary"><i class="fas fa-info-circle fa-2x"></i></div>' +
        '<div><h6 class="font-weight-bold mb-1">Signature Requirements</h6>' +
        '<p class="mb-0">Upload a clear image of the signature. Recommended formats: ' +
        '<span class="badge badge-primary">JPG</span> ' +
        '<span class="badge badge-primary">PNG</span> ' +
        '<span class="badge badge-primary">GIF</span></p></div></div>');
      
      $(input).closest('.form-group').append(newSignatureHelp);
    } else {
      $('#signatureHelp').show();
    }
    
    // Remove success indicator
    $(input).removeClass('is-valid');
    $(input).closest('.custom-file').find('.custom-file-label').removeClass('border-success');
  }
}

// Add this to document ready to ensure the alert is properly handled on page load
$(document).ready(function() {
  // Existing code...
  
  // Check if a file is already selected on page load
  $('#signatureFile').each(function() {
    if (this.files && this.files[0]) {
      $('#signatureHelp').hide();
    }
  });
  
  // Ensure the alert stays hidden after file selection
  $('.custom-file-input').on('change', function() {
    if (this.files && this.files[0]) {
      $('#signatureHelp').hide();
    } else {
      $('#signatureHelp').show();
    }
  });
});