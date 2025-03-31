<?php
include('../config.php');
if (!isset($_SESSION['user_id'])) {
  header("Location: user_login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$success = null;
$error = null;

if (isset($_POST['change_password'])) {
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  
  // Validate password length
  if (strlen($new_password) < 6 || strlen($new_password) > 12) {
    $error = "New password must be between 6 and 12 characters.";
  } 
  // Check if new passwords match
  elseif ($new_password !== $confirm_password) {
    $error = "New passwords do not match.";
  } 
  else {
    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($current_password, $user['password'])) {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
          $success = "Password changed successfully.";
        } else {
          $error = "Error updating password. Please try again.";
        }
        $update_stmt->close();
      } else {
        $error = "Current password is incorrect.";
      }
    } else {
      $error = "User not found.";
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="min-vh-100" style="background: linear-gradient(135deg, #1a3a8f 0%, #0d2b6b 100%);">
  <!-- Animated floating blobs using Bootstrap utilities -->
  <div class="position-fixed w-100 h-100" style="z-index: -1;">
    <div class="position-absolute rounded-circle" style="width: 400px; height: 400px; background: rgba(100, 181, 246, 0.15); filter: blur(60px); top: 20%; left: 10%; animation: float 12s infinite;"></div>
    <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(66, 165, 245, 0.12); filter: blur(60px); top: 50%; right: 15%; animation: float 12s infinite 4s;"></div>
    <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(144, 202, 249, 0.15); filter: blur(60px); bottom: 10%; left: 30%; animation: float 12s infinite 8s;"></div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark shadow-lg mx-md-4 mx-2" style="
      background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
      border-radius: 20px;
      margin-top: 15px;
      padding: 12px 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 5px 10px rgba(0, 0, 0, 0.05);
    ">
    <div class="container">
      <span class="navbar-brand d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-primary mr-2" style="width: 38px; height: 38px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
          <i class="fas fa-key"></i>
        </div>
        <span class="font-weight-bold ml-1" style="letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">Change Password</span>
      </span>
      <div class="ml-auto">
        <a href="user_dashboard.php" class="btn rounded-pill px-4 shadow-sm" style="
          background: rgba(255, 255, 255, 0.15); 
          color: white; 
          border: 1px solid rgba(255, 255, 255, 0.3);
          transition: all 0.3s ease;
          backdrop-filter: blur(5px);
        " onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden" style="
          border-radius: 30px !important; 
          backdrop-filter: blur(20px); 
          background: rgba(255, 255, 255, 0.9);
          box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 8px 24px -4px rgba(41, 98, 255, 0.2);
          border: 1px solid rgba(255, 255, 255, 0.5);
        ">
          <div class="card-header border-0 py-4" style="
            background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
          ">
            <div class="d-flex align-items-center">
              <div class="d-flex align-items-center justify-content-center rounded-lg text-white mr-3" style="
                width: 50px; 
                height: 50px; 
                background: rgba(255, 255, 255, 0.2); 
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.2);
                border-radius: 15px;
              ">
                <i class="fas fa-lock"></i>
              </div>
              <h4 class="mb-0 text-white font-weight-bold" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">Update Your Password</h4>
            </div>
          </div>

          <div class="card-body p-md-4 p-3" style="background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%);">
            <?php if ($success): ?>
              <div class="alert alert-success alert-dismissible fade show rounded-lg" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show rounded-lg" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="form-group">
                <label for="current_password" class="font-weight-bold text-primary">Current Password</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0 rounded-pill-left">
                      <i class="fas fa-lock text-primary"></i>
                    </span>
                  </div>
                  <input type="password" id="current_password" name="current_password" required 
                    class="form-control bg-light border-left-0 rounded-pill-right" 
                    placeholder="Enter your current password">
                </div>
              </div>
              
              <div class="form-group">
                <label for="new_password" class="font-weight-bold text-primary">New Password</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0 rounded-pill-left">
                      <i class="fas fa-key text-primary"></i>
                    </span>
                  </div>
                  <input type="password" id="new_password" name="new_password" required 
                    class="form-control bg-light border-left-0 rounded-pill-right" 
                    placeholder="Enter new password (6-12 characters)" 
                    minlength="6" maxlength="12">
                </div>
                <small class="form-text text-muted ml-2">Password must be 6-12 characters long</small>
              </div>
              
              <div class="form-group">
                <label for="confirm_password" class="font-weight-bold text-primary">Confirm New Password</label>
                <div class="input-group shadow-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-right-0 rounded-pill-left">
                      <i class="fas fa-check-circle text-primary"></i>
                    </span>
                  </div>
                  <input type="password" id="confirm_password" name="confirm_password" required 
                    class="form-control bg-light border-left-0 rounded-pill-right" 
                    placeholder="Confirm your new password" 
                    minlength="6" maxlength="12">
                </div>
              </div>
              
              <div class="mt-4">
                <button type="submit" name="change_password" class="btn btn-primary btn-block rounded-pill py-2 shadow-sm">
                  <i class="fas fa-save mr-2"></i> Update Password
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }
    
    .rounded-pill-left {
      border-top-left-radius: 50rem !important;
      border-bottom-left-radius: 50rem !important;
    }
    
    .rounded-pill-right {
      border-top-right-radius: 50rem !important;
      border-bottom-right-radius: 50rem !important;
    }
  </style>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>