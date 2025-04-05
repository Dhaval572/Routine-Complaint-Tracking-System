<?php
include('../config.php');

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// Fetch departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

if (isset($_POST['create_dept_head'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $department_id = $_POST['department_id'];
  $role = 'dept_head';

  // Check if email already exists
  $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $checkEmail->bind_param("s", $email);
  $checkEmail->execute();
  $result = $checkEmail->get_result();
  
  if ($result->num_rows > 0) {
    $_SESSION['error'] = "This email address is already registered. Please use a different email.";
  } else {
    // Insert department head details into the users table
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $email, $password, $role, $department_id);
    if ($stmt->execute()) {
      $user_id = $conn->insert_id;
      $_SESSION['success'] = "Department Head created successfully";
      $_SESSION['show_success_modal'] = true;
      
      // Process signature file upload and insert into the signatures table
      if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $uploadDir = '../signatures/'; // Ensure this folder exists and is writable
        $filename = time() . "_" . basename($_FILES['signature']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['signature']['tmp_name'], $targetFile)) {
            $stmtSig = $conn->prepare("INSERT INTO signatures (user_id, signature_filename) VALUES (?, ?)");
            $stmtSig->bind_param("is", $user_id, $filename);
            $stmtSig->execute();
            $stmtSig->close();
        }
      }
    } else {
      $_SESSION['error'] = "Error creating Department Head: " . $conn->error;
    }
    $stmt->close();
  }
  $checkEmail->close();
}

// Clear session error after displaying
if (isset($_SESSION['error'])) {
  $error = $_SESSION['error'];
  unset($_SESSION['error']);
}

// Clear session success after displaying
if (isset($_SESSION['success'])) {
  $success = $_SESSION['success'];
  unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Department Head | Admin Portal</title>
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
      margin-bottom: 2rem;
      max-width: 100%;
      width: 95%;
    }
    
    .card-header {
      background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
      color: white;
      text-align: center;
      padding: 1.5rem 1rem;
      border-bottom: none;
    }
    
    .header-icon {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }
    
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
    
    .btn-primary {
      background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
      border: none;
      transition: all 0.3s ease;
      padding: 0.5rem 1rem;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
    }
    
    .custom-file-label {
      overflow: hidden;
    }
    
    .custom-alert {
      position: relative;
      border: none !important;
      border-radius: 15px !important;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      overflow: hidden;
      margin-bottom: 1.5rem;
    }
    
    .alert-icon {
      width: 40px;
      height: 40px;
      min-width: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0.5rem;
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
    
    .back-link {
      position: absolute;
      left: 1rem;
      top: 1rem;
      color: white;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .back-link:hover {
      color: rgba(255, 255, 255, 0.8);
      transform: translateX(-3px);
    }
    
    .animate__animated {
      animation-duration: 0.5s;
      animation-fill-mode: both;
    }
    
    .invalid-feedback {
      color: #dc3545;
      font-size: 0.75rem;
      margin-top: 0.25rem;
    }
    
    .form-control.is-invalid {
      border-color: #dc3545;
    }
    
    .form-control.is-valid {
      border-color: #28a745;
    }
    
    @media (max-width: 768px) {
      .card-body {
        padding: 1rem;
      }
      
      .modal-body {
        padding: 2rem;
      }
      
      .btn {
        padding: 0.5rem 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <div class="card shadow-lg">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-center position-relative mb-3">
              <a href="admin_dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
              </a>
              <div class="text-center header-icon-container">
                <i class="fas fa-user-shield header-icon"></i>
              </div>
            </div>
            <h3 class="font-weight-bold mb-2">Create Department Head</h3>
            <p class="mb-0 opacity-75">Add a new department head to the system</p>
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
            
            <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate autocomplete="off">
              <div class="form-group">
                <label><i class="fas fa-user-tie mr-2"></i>Full Name</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                  </div>
                  <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                </div>
              </div>

              <div class="form-group">
                <label><i class="fas fa-envelope mr-2"></i>Email Address</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-at"></i></span>
                  </div>
                  <input type="email" name="email" class="form-control" required placeholder="Enter official email">
                </div>
              </div>

              <div class="form-group">
                <label><i class="fas fa-lock mr-2"></i>Password</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                  </div>
                  <input type="password" name="password" class="form-control" required placeholder="Create a strong password">
                </div>
                <small class="form-text text-muted mt-2">
                  <i class="fas fa-shield-alt mr-2"></i>Password should be 6-10 characters with letters and numbers
                </small>
              </div>

              <div class="form-group">
                <label><i class="fas fa-building mr-2"></i>Department</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-sitemap"></i></span>
                  </div>
                  <select name="department_id" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php while ($dept = $departments->fetch_assoc()) { ?>
                      <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label><i class="fas fa-signature mr-2"></i>Digital Signature</label>
                <div class="custom-file mb-2">
                  <input type="file" name="signature" class="custom-file-input" id="signatureFile" required 
                         accept="image/*" onchange="previewSignature(this)">
                  <label class="custom-file-label" for="signatureFile">Choose signature file</label>
                </div>
                <div id="signaturePreviewContainer" class="text-center mt-3 p-3 rounded border" style="display: none; background-color: #f8f9fc;">
                  <img id="signaturePreview" class="img-fluid" style="max-height: 100px;" />
                  <p class="small text-muted mt-2 mb-0" id="fileName"></p>
                </div>
              </div>

              <div class="text-center mt-4">
                <button type="submit" name="create_dept_head" class="btn btn-primary btn-lg px-5 rounded-pill">
                  <i class="fas fa-plus mr-2"></i>Create Department Head
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Signature Preview Function
    function previewSignature(input) {
      const preview = document.getElementById('signaturePreview');
      const container = document.getElementById('signaturePreviewContainer');
      const fileName = document.getElementById('fileName');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          container.style.display = 'block';
          fileName.textContent = input.files[0].name;
        }
        
        reader.readAsDataURL(input.files[0]);
      }
    }

    // Bootstrap Validation
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();

    // Dynamic File Name Display
    $('.custom-file-input').on('change', function() {
      let fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').addClass('selected').html(fileName);
    });
    
    // Enhanced alert handling
    document.addEventListener('DOMContentLoaded', function() {
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
  </script>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
        <div class="modal-header bg-success text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
          <h5 class="modal-title"><i class="fas fa-check-circle mr-2"></i>Success!</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center p-5">
          <div class="mb-4">
            <i class="fas fa-user-plus text-success" style="font-size: 4rem;"></i>
          </div>
          <h3 class="font-weight-bold mb-3">Department Head Created!</h3>
          <p class="text-muted mb-4">What would you like to do next?</p>
          <div class="d-flex justify-content-center gap-3">
            <a href="create_dept_head.php" class="btn btn-outline-primary rounded-pill px-4 mr-3">
              <i class="fas fa-plus mr-2"></i>Create Another
            </a>
            <a href="admin_dashboard.php" class="btn btn-secondary rounded-pill px-4">
              <i class="fas fa-home mr-2"></i>Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Add this to your existing DOMContentLoaded event or create a new one
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (isset($_SESSION['show_success_modal'])): ?>
        $('#successModal').modal('show');
        <?php unset($_SESSION['show_success_modal']); ?>
      <?php endif; ?>
    });
  </script>
</body>
</html>