<?php
include('../config.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success = null;
$error = null;

// Fetch user data
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Update profile logic
if (isset($_POST['update_profile'])) {
    $name = trim($conn->real_escape_string($_POST['name']));
    $current_name = trim($user['name']); // Get current name for comparison
    
    // Validate name
    if (empty($name)) {
        $error = [
            'type' => 'danger',
            'icon' => 'exclamation-triangle',
            'title' => 'Validation Error',
            'message' => 'Please enter your full name'
        ];
    } elseif (strlen($name) < 3) {
        $error = [
            'type' => 'warning',
            'icon' => 'exclamation-circle',
            'title' => 'Invalid Name',
            'message' => 'Name must be at least 3 characters long'
        ];
    } elseif ($name === $current_name) {
        // Only show no changes alert when update button is clicked and names match
        $error = [
            'type' => 'info',
            'icon' => 'info-circle',
            'title' => 'No Changes',
            'message' => 'The name is the same as current name'
        ];
    } else {
        // Update user profile
        $update = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $update->bind_param("si", $name, $user_id);
        
        if ($update->execute()) {
            $_SESSION['user_name'] = $name;
            $_SESSION['success'] = [
                'icon' => 'check-circle',
                'title' => 'Success!',
                'message' => 'Your profile has been updated successfully'
            ];
            $user['name'] = $name;
            header("Location: user_dashboard.php");
            exit;
        } else {
            $error = [
                'type' => 'danger',
                'icon' => 'times-circle',
                'title' => 'Update Failed',
                'message' => 'Unable to update profile. Please try again.'
            ];
        }
        $update->close();
    }
}

// In the form input section, add autocomplete
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
      @keyframes slideInRight {
          from {
              transform: translateX(100%);
              opacity: 0;
          }
          to {
              transform: translateX(0);
              opacity: 1;
          }
      }
  
      @keyframes shake {
          0%, 100% { transform: translateX(0); }
          10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
          20%, 40%, 60%, 80% { transform: translateX(5px); }
      }
  
      @keyframes pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.2); }
          100% { transform: scale(1); }
      }
  
      .animate-alert {
          animation: slideInRight 0.5s ease-out forwards;
          border-left: 5px solid;
      }
  
      .alert-success {
          border-left-color: #28a745;
      }
  
      .alert-danger {
          border-left-color: #dc3545;
      }
  
      .alert-warning {
          border-left-color: #ffc107;
      }
  
      .alert-info {
          border-left-color: #17a2b8;
      }
  
      .shake {
          animation: shake 0.8s ease-in-out;
      }
  
      .pulse {
          animation: pulse 1s infinite;
      }
  
      .alert {
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          border-radius: 10px;
          margin-bottom: 1rem;
      }
  
      .alert-icon {
          background: rgba(255,255,255,0.2);
          padding: 10px;
          border-radius: 50%;
      }
      
      /* Enhanced styling */
      .card {
          transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .card:hover {
          transform: translateY(-8px) !important;
          box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important;
      }
      
      .input-group-text {
          transition: background-color 0.3s ease;
      }
      
      .input-group-text:hover {
          background-color: #0d47a1 !important;
      }
      
      .form-control:focus {
          box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
          border-color: #1565c0;
          background-color: #fff !important;
          transition: all 0.3s ease;
      }
      
      .btn {
          transition: all 0.3s ease;
          position: relative;
          overflow: hidden;
      }
      
      .btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      }
      
      .btn:active {
          transform: translateY(0);
      }
      
      .btn i {
          transition: transform 0.3s ease;
      }
      
      .btn:hover i {
          transform: scale(1.1);
      }
      
      .text-white.rounded-circle {
          transition: all 0.3s ease;
      }
      
      .text-white.rounded-circle:hover {
          transform: scale(1.05);
          box-shadow: 0 6px 12px rgba(0,0,0,0.3);
      }
      
      @media (max-width: 768px) {
          .container {
              padding: 10px;
          }
          
          .card {
              margin: 10px;
          }
          
          .card-header h4 {
              font-size: 1.25rem;
          }
          
          .btn {
              padding: 0.5rem 1rem;
          }
          
          .alert {
              padding: 0.75rem;
          }
          
          .alert-icon {
              padding: 8px;
          }
          
          .fa-2x {
              font-size: 1.5em;
          }
      }
      
      @media (max-width: 576px) {
          .card-body {
              padding: 1rem;
          }
          
          .row.mt-4 {
              margin-top: 1rem !important;
          }
          
          .mb-3 {
              margin-bottom: 0.5rem !important;
          }
      }
      
      /* Smooth scrolling */
      html {
          scroll-behavior: smooth;
      }
      
      /* Better form responsiveness */
      .form-group {
          margin-bottom: 1.5rem;
      }
      
      /* Enhanced alert animations */
      .animate-alert {
          animation-duration: 0.6s;
          animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
      }
      
      /* Adjusted card and alert sizing */
      @media (min-width: 992px) {
          .col-md-8 {
              max-width: 650px;
          }
      }
      
      @media (min-width: 768px) and (max-width: 991px) {
          .col-md-8 {
              max-width: 550px;
          }
      }
      
      .alert {
          font-size: 0.95rem;
          padding: 0.75rem;
          margin-bottom: 1rem;
      }
      
      .alert h5 {
          font-size: 1.1rem;
      }
      
      .alert .fa-2x {
          font-size: 1.5em;
      }
      
      .alert-icon {
          padding: 8px;
      }
      
      .card-body {
          padding: 1.25rem;
      }
      
      .card-header {
          padding: 0.75rem 1.25rem;
      }
      
      .card-header h4 {
          font-size: 1.35rem;
      }
      
      @media (max-width: 576px) {
          .container {
              padding: 8px;
          }
          
          .py-4 {
              padding-top: 1rem !important;
              padding-bottom: 1rem !important;
          }
          
          .card-body {
              padding: 1rem;
          }
          
          .alert {
              font-size: 0.9rem;
              padding: 0.6rem;
          }
          
          .alert h5 {
              font-size: 1rem;
          }
          
          .alert .fa-2x {
              font-size: 1.25em;
          }
          
          .card-header h4 {
              font-size: 1.25rem;
          }
          
          .fa-user-circle {
              font-size: 2.5rem !important;
          }
      }
  </style>
</head>

<body class="bg-light min-vh-100" style="background-color:#e8f5e9 !important; background-image: linear-gradient(to bottom right,rgb(122, 235, 132),rgb(165, 231, 167));">
  <nav class="navbar navbar-dark mb-4" style="background-color: #1b5e20; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
    <div class="container">
      <span class="navbar-brand">
        <i class="fas fa-user-circle mr-2"></i>My Profile
      </span>
      <a href="user_dashboard.php" class="btn btn-outline-light rounded-pill">
        <i class="fas fa-home mr-2"></i>Dashboard
      </a>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card border-0 shadow-lg rounded-lg" style="border-radius: 20px !important; box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important; transform: translateY(-5px);">
          <div class="card-header text-white py-3" style="background-color: #1565c0; border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <div class="d-flex align-items-center">
              <i class="fas fa-user-cog fa-2x mr-3"></i>
              <h4 class="mb-0">Manage Your Profile</h4>
            </div>
          </div>

          <div class="card-body" style="background-color:rgb(197, 226, 252); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
            <?php if ($success): ?>
              <div class="alert alert-success alert-dismissible fade show animate-alert" role="alert">
                <div class="d-flex align-items-center">
                    <div class="alert-icon mr-3">
                        <i class="fas fa-<?= $success['icon'] ?> fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-1"><?= $success['title'] ?></h5>
                        <p class="mb-0"><?= $success['message'] ?></p>
                    </div>
                    <button type="button" class="close ml-auto" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
              <div class="alert alert-<?= $error['type'] ?> alert-dismissible fade show animate-alert" role="alert">
                <div class="d-flex align-items-center">
                    <div class="alert-icon mr-3">
                        <i class="fas fa-<?= $error['icon'] ?> fa-2x pulse"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 shake"><?= $error['title'] ?></h5>
                        <p class="mb-0"><?= $error['message'] ?></p>
                    </div>
                    <button type="button" class="close ml-auto" data-dismiss="alert">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              </div>
            <?php endif; ?>

            <div class="text-center mb-4">
              <div class="text-white rounded-circle p-3 d-inline-block mb-3" style="background-color: #1565c0; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <i class="fas fa-user-circle" style="font-size: 3rem;"></i>
              </div>
              <h5 class="font-weight-bold"><?= htmlspecialchars($user['name']) ?></h5>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name" class="font-weight-bold">Full Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-primary text-white rounded-pill" style="border-top-right-radius: 0; border-bottom-right-radius: 0; z-index: 1;">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>
                        <input type="text" id="name" name="name" required 
                            class="form-control rounded-pill" 
                            style="border-top-left-radius: 0; border-bottom-left-radius: 0; margin-left: -1px; background-color: #e6f2ff;"
                            placeholder="Your full name"
                            value="<?= htmlspecialchars($user['name']) ?>"
                            autocomplete="off"
                            pattern=".{3,}"
                            title="Name must be at least 3 characters long"
                            oninput="this.setCustomValidity('')"
                            oninvalid="this.setCustomValidity('Please enter a valid name')">
                    </div>
                </div>
                
                <!-- Email form group removed -->
              
                <div class="row mt-4">
                    <!-- Rest of the form buttons -->
                <div class="col-md-6 mb-3">
                  <a href="change_password.php" class="btn btn-outline-primary btn-block rounded-pill">
                    <i class="fas fa-key mr-2"></i> Change Password
                  </a>
                </div>
                <div class="col-md-6 mb-3">
                  <button type="submit" name="update_profile" class="btn btn-primary btn-block rounded-pill">
                    <i class="fas fa-save mr-2"></i> Update Profile
                  </button>
                </div>
              </div>
              
              <div class="mt-3">
                <a href="delete_account.php" class="btn btn-outline-danger btn-block rounded-pill">
                  <i class="fas fa-user-times mr-2"></i> Delete Account
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>