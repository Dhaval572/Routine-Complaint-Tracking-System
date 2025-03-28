<?php
include('../config.php');

if (isset($_POST['login'])) {
  $email = $conn->real_escape_string($_POST['email']);
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'citizen'";
  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      header("Location: user_dashboard.php");
      exit;
    } else {
      $error = "Invalid credentials.";
    }
  } else {
    $error = "Invalid credentials.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background: linear-gradient(135deg, #0396FF 0%, #0D47A1 100%);">
  <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-4">
      <!-- Logo or Brand Image -->
      <div class="text-center mb-3">
        <i class="fas fa-user-circle text-white" style="font-size: 3.5rem;"></i>
      </div>
      
      <div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
        <div class="card-header border-0 bg-white text-center py-3" style="border-radius: 1.5rem 1.5rem 0 0;">
          <h3 class="font-weight-bold text-primary mb-2">Welcome Back!</h3>
          <p class="text-muted small mb-0">Sign in to your account</p>
        </div>
        <div class="card-body px-4 py-4">
          <?php if (isset($error)): ?>
            <div class='alert alert-danger py-2 d-flex align-items-center rounded-pill small'>
              <i class='fas fa-exclamation-circle mr-2'></i><?php echo $error; ?>
            </div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="form-group mb-3">
              <div class="input-group shadow-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-light border-right-0 rounded-pill px-3">
                    <i class="fas fa-envelope text-primary"></i>
                  </span>
                </div>
                <input type="email" name="email" required 
                  class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small" 
                  placeholder="Email address" autocomplete="off">
            </div>
            </div>
            <div class="form-group mb-3">
              <div class="input-group shadow-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-light border-right-0 rounded-pill px-3">
                    <i class="fas fa-lock text-primary"></i>
                  </span>
                </div>
                <input type="password" name="password" required 
                  class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small" 
                  placeholder="Password" autocomplete="off">
            </div>
            </div>
            <button type="submit" name="login" 
              class="btn btn-primary btn-block mb-3 shadow rounded-pill py-2 font-weight-bold">
              Sign In
            </button>
            <div class="text-center">
              <a href="register_complaint.php" class="text-primary small font-weight-bold">
                <i class="fas fa-user-plus mr-2"></i>Don't have an account? Register
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>