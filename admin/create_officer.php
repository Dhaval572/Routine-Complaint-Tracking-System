<?php
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

if (isset($_POST['create_officer'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department_id = $_POST['department_id'];
    $role = 'officer';
  
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        $error = "This email address is already registered. Please use a different email.";
    } else {
        // Insert officer details
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssi", $name, $email, $password, $role, $department_id);
            
            if ($stmt->execute()) {
                $user_id = $conn->insert_id;
                
                // Process signature file upload
                if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
                    $uploadDir = '../signatures/';
                    $filename = time() . "_" . basename($_FILES['signature']['name']);
                    $targetFile = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES['signature']['tmp_name'], $targetFile)) {
                        $stmtSig = $conn->prepare("INSERT INTO signatures (user_id, signature_filename) VALUES (?, ?)");
                        if ($stmtSig) {
                            $stmtSig->bind_param("is", $user_id, $filename);
                            if ($stmtSig->execute()) {
                                $_SESSION['officer_created'] = true;
                                $success = "Officer account created successfully!";
                                echo "<script>$(document).ready(function() { $('#successModal').modal('show'); });</script>";
                            } else {
                                $error = "Error saving signature information. Please try again.";
                            }
                            $stmtSig->close();
                        } else {
                            $error = "Error preparing signature statement. Please try again.";
                        }
                    } else {
                        $error = "Failed to upload signature file. Please try again.";
                    }
                } else {
                    $_SESSION['officer_created'] = true;
                    $success = "Officer account created successfully!";
                    echo "<script>$(document).ready(function() { $('#successModal').modal('show'); });</script>";
                }
            } else {
                $error = "Error creating officer account. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "Error preparing statement. Please try again.";
        }
    }
    $checkEmail->close();
}
?>

<!-- Add this JavaScript at the end of your file, before </body> -->
<script>
// Enhanced alert handling
document.addEventListener('DOMContentLoaded', function() {
  // Enhanced alert handling
  const alerts = document.querySelectorAll('.custom-alert');
  
  alerts.forEach(alert => {
    // Add hover effect
    alert.addEventListener('mouseenter', () => {
      alert.style.transform = 'translateY(-5px) scale(1.01)';
    });
    
    alert.addEventListener('mouseleave', () => {
      alert.style.transform = 'translateY(0) scale(1)';
    });
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
      dismissAlert(alert);
    }, 5000);
    
    // Handle dismiss button
    const dismissBtn = alert.querySelector('.alert-dismiss');
    if (dismissBtn) {
      dismissBtn.addEventListener('click', () => {
        dismissAlert(alert);
      });
    }
  });
  
  function dismissAlert(alert) {
    alert.classList.add('animate__fadeOutUp');
    setTimeout(() => {
      alert.style.height = alert.offsetHeight + 'px';
      alert.style.marginTop = '0';
      alert.style.marginBottom = '0';
      alert.style.padding = '0';
      alert.style.overflow = 'hidden';
      
      setTimeout(() => {
        alert.style.height = '0';
        setTimeout(() => {
          alert.remove();
        }, 300);
      }, 10);
    }, 300);
  }
});

// Real-time email validation
document.querySelector('input[type="email"]').addEventListener('blur', function() {
    const email = this.value;
    if (email) {
        fetch('check_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            const emailInput = document.querySelector('input[type="email"]');
            const existingFeedback = emailInput.parentElement.querySelector('.invalid-feedback');
            
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            if (data.exists) {
                emailInput.classList.add('is-invalid');
                emailInput.classList.remove('is-valid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback animate__animated animate__fadeIn';
                feedback.style.display = 'block';
                feedback.innerHTML = 'This email is already registered. Please use a different email.';
                emailInput.parentElement.appendChild(feedback);
            } else {
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
            }
        });
    }
});
</script>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Officer</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 1.5rem;
      border: none;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .card-header {
      background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
      color: white;
      text-align: center;
      padding: 2rem 1.5rem;
      border-bottom: none;
    }
    .header-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }
    /* Main Styles */
        /* Form Styles */
        .form-group label {
          font-weight: 500;
          color: #4e73df;
        }
        
        .input-group-text {
          background-color: #4e73df;
          color: white;
          border: none;
        }
        
        .form-control:focus, 
        .custom-file-input:focus {
          border-color: #4e73df;
          box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Button Styles */
        .btn-primary {
          background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
          border: none;
          transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
          transform: translateY(-2px);
          box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }
        
        /* File Upload Styles */
        .custom-file-label {
          overflow: hidden;
        }
        
        #signaturePreviewContainer {
          transition: all 0.3s ease;
        }
        
        /* Alert Styles */
        .alert-icon {
          width: 48px;
          height: 48px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 50%;
          margin-right: 1rem;
        }
        
        /* Success Alert */
        .alert.alert-success {
          background-color: rgba(40, 167, 69, 0.1);
          border-left: 4px solid #28a745;
          position: relative;
        }
        
        .alert.alert-success .alert-icon {
          background: linear-gradient(145deg, #28a745, #20c997);
          box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }
        
        .alert.alert-success .close {
          opacity: 1;
          text-shadow: none;
          transition: transform 0.3s ease;
          color: #28a745;
        }
        
        .alert.alert-success .close:hover {
          transform: rotate(90deg);
        }
        
        /* Danger Alert */
        /* Update Alert Styles */
        .custom-alert {
            position: relative;
            border: none !important;
            border-radius: 15px !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .alert-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert.alert-success {
            background: rgba(40, 167, 69, 0.1);
            border-left: 5px solid #28a745 !important;
        }
        
        .alert.alert-danger {
            background: rgba(220, 53, 69, 0.1);
            border-left: 5px solid #dc3545 !important;
        }
        
        .alert .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            opacity: 0.8;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.2);
        }
        
        .alert .close:hover {
            opacity: 1;
            transform: rotate(90deg);
            background: rgba(255,255,255,0.3);
        }
        
        /* Animation Fixes */
        .animate__animated {
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }
        
        .animate__fadeInDown {
            animation-name: fadeInDown;
        }
        
        .animate__fadeOutUp {
            animation-name: fadeOutUp;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        @keyframes fadeOutUp {
            from {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
            to {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .form-control.is-valid {
            border-color: #28a745;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-lg">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-center position-relative mb-3">
              <a href="admin_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
              </a>
              <div class="text-center header-icon-container">
                <i class="fas fa-user-plus header-icon"></i>
              </div>
            </div>
            <h3 class="font-weight-bold mb-2">Create Officer Account</h3>
            <p class="mb-0 opacity-75">Add a new department officer to the system</p>
          </div>
          
          <div class="card-body bg-light p-4">
            <div id="alertContainer">
              <?php if (isset($success)): ?>
              <!-- Success Alert -->
              <div class="alert custom-alert alert-success animate__animated animate__fadeInDown d-flex align-items-center p-4">
                  <div class="alert-icon bg-success">
                      <i class="fas fa-check-circle fa-lg text-white"></i>
                  </div>
                  <div class="alert-content">
                      <h5 class="mb-1 font-weight-bold">Success!</h5>
                      <p class="mb-0"><?php echo $success; ?></p>
                  </div>
                  <button type="button" class="close alert-dismiss">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <?php endif; ?>
              
              <?php if (isset($error)): ?>
              <!-- Error Alert -->
              <div class="alert custom-alert alert-danger animate__animated animate__fadeInDown d-flex align-items-center p-4">
                  <div class="alert-icon bg-danger">
                      <i class="fas fa-exclamation-circle fa-lg text-white"></i>
                  </div>
                  <div class="alert-content">
                      <h5 class="mb-1 font-weight-bold">Error!</h5>
                      <p class="mb-0"><?php echo $error; ?></p>
                  </div>
                  <button type="button" class="close alert-dismiss">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <?php endif; ?>
            </div>
            <form method="POST" action="" enctype="multipart/form-data" autocomplete="off">
              <div class="form-group">
                <label><i class="fas fa-user mr-2"></i>Officer Name</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                  </div>
                  <input type="text" name="name" required class="form-control" placeholder="Enter full name" autocomplete="off">
                </div>
              </div>
              
              <div class="form-group">
                <label><i class="fas fa-envelope mr-2"></i>Email Address</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                  </div>
                  <input type="email" name="email" required class="form-control" placeholder="Enter official email" autocomplete="off">
                </div>
              </div>
              
              <div class="form-group">
                <label><i class="fas fa-lock mr-2"></i>Password</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                  </div>
                  <input type="password" name="password" required class="form-control" placeholder="Create a strong password" autocomplete="off">
                </div>
              </div>
              
              <div class="form-group">
                <label><i class="fas fa-building mr-2"></i>Department</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                  </div>
                  <select name="department_id" class="form-control" required autocomplete="off">
                    <option value="">Select Department</option>
                    <?php while ($dept = $departments->fetch_assoc()) { ?>
                      <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              
              <div class="form-group">
                <label><i class="fas fa-signature mr-2"></i>Officer Signature</label>
                <div class="custom-file mb-2">
                  <input type="file" name="signature" required class="custom-file-input" id="signatureFile" accept="image/*" onchange="previewSignature(this)" autocomplete="off">
                  <label class="custom-file-label" for="signatureFile">Choose file</label>
                </div>
                <div id="signaturePreviewContainer" class="text-center mt-3 p-3 rounded border" style="display: none; background-color: #f8f9fc;">
                  <img id="signaturePreview" src="#" alt="Signature Preview" class="img-fluid mb-2" style="max-height: 100px; border: 1px dashed #4e73df; padding: 5px; border-radius: 5px;">
                  <div class="text-success mt-2">
                    <i class="fas fa-check-circle mr-1"></i> <span id="signatureFileName">Signature uploaded successfully</span>
                  </div>
                </div>
                
                <!-- Signature Guidance Alert -->
                <div id="signatureAlert" class="alert alert-info mt-3 d-flex align-items-center animate__animated animate__fadeIn" role="alert">
                  <div class="mr-3 text-primary">
                    <i class="fas fa-info-circle fa-2x"></i>
                  </div>
                  <div>
                    <h6 class="font-weight-bold mb-1">Signature Requirements</h6>
                    <p class="mb-0">Upload a clear image of the officer's signature. Recommended formats: 
                      <span class="badge badge-primary">JPG</span> 
                      <span class="badge badge-primary">PNG</span> 
                      <span class="badge badge-primary">GIF</span>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="text-center mt-4">
                <button type="submit" name="create_officer" class="btn btn-primary btn-lg btn-block rounded-pill shadow">
                  <i class="fas fa-user-plus mr-2"></i>Create Officer Account
                </button>
              </div>
            </form>
            
            <!-- Remove the following div completely -->
            <!-- <div class="text-center mt-3">
              <a href="admin_dashboard.php" class="btn btn-secondary rounded-pill">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
              </a>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Update the script order and add modal initialization -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script>
    // Initialize Bootstrap modal
    $(document).ready(function() {
      <?php if (isset($_SESSION['officer_created']) && $_SESSION['officer_created']): ?>
        $('#successModal').modal('show');
        <?php unset($_SESSION['officer_created']); ?>
      <?php endif; ?>
    });

    // Add signature preview functionality
    function previewSignature(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
          $('#signaturePreview').attr('src', e.target.result);
          $('#signatureFileName').text(input.files[0].name);
          $('#signaturePreviewContainer').show();
          
          // Hide the signature alert with animation and remove from DOM flow
          $('#signatureAlert').removeClass('animate__fadeIn').addClass('animate__fadeOut');
          setTimeout(function() {
            $('#signatureAlert').hide().css('margin', '0').css('height', '0').css('padding', '0');
          }, 500);
        }
        
        reader.readAsDataURL(input.files[0]);
      }
    }
    
    // Update custom file input label with filename
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
  </script>

  <!-- Success Modal -->
    <style>
      /* Update existing styles */
      .back-link {
        font-size: 1.3rem;
        transition: all 0.3s ease;
        position: absolute;
        left: 1.2rem;
        top: 2.2rem;
        z-index: 10;
        padding: 12px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }
      
      .back-link:hover {
        transform: translateX(-3px);
        color: #fff !important;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
      
      .back-link:active {
        transform: translateX(-1px);
        background: rgba(255, 255, 255, 0.2);
      }

      .header-icon-container {
        width: 100%;
        padding: 1rem 0;
        position: relative;
        z-index: 1;
      }
      
      .header-icon {
        font-size: 2.5rem;
        color: #fff;
        margin: 0;
      }
      
      .card-header {
        position: relative;
        padding: 2.5rem 1.5rem 1.5rem;
      }
    </style>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
          <div class="modal-header border-0 py-3">
            <a href="admin_dashboard.php" class="back-link">
              <i class="fas fa-arrow-left"></i>
            </a>
            <h6 class="modal-title font-weight-bold mb-0 w-100 text-center" id="successModalLabel">
              Success!
            </h6>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body text-center py-4">
            <div class="animate__animated animate__bounceIn">
              <i class="fas fa-user-plus fa-3x text-success mb-3"></i>
              <h5 class="font-weight-bold mb-3">Officer Created!</h5>
            </div>
            <div class="d-flex justify-content-center">
              <a href="create_officer.php" class="btn btn-primary btn-sm rounded-pill shadow-sm px-4 py-2 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                <i class="fas fa-plus-circle mr-2"></i>Create Another
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
