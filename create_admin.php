<?php
include('config.php');

if (isset($_POST['register'])) {
  // Check if form fields are set and not empty
  $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
  $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
  $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
  
  // Removed role selection
  $role = 'admin'; // Set role to 'admin' by default

  // Check if the email already exists
  if (!empty($email)) {
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
      $error = "Email already registered.";
    } else {
      $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $name, $email, $password, $role);
      if ($stmt->execute()) {
        $success = "Registration successful. You can now <a href='user_login.php'>login</a>.";
      } else {
        $error = "Error during registration. Please try again.";
      }
      $stmt->close();
    }
  } else {
    $error = "Email field cannot be empty.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create System Administrator</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background: linear-gradient(135deg, #1C1E21 0%, #2D3436 100%);">
  <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-5 col-lg-4 col-xl-4">
      <div class="text-center mb-3">
        <i class="fas fa-user-cog text-white" style="font-size: 3.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"></i>
      </div>
      <div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
        <div class="card-header border-0 bg-white text-center py-3" style="border-radius: 1.5rem 1.5rem 0 0;">
          <h3 class="font-weight-bold text-dark mb-2">Create Administrator</h3>
          <p class="text-muted small mb-0">Set up system administrator account</p>
        </div>
        <div class="card-body px-4 py-3">
          <?php if (isset($error)): ?>
            <div class='alert alert-danger py-2 d-flex align-items-center rounded-pill small'>
              <i class='fas fa-exclamation-circle mr-2'></i><?php echo $error; ?>
            </div>
          <?php endif; ?>
          <?php if (isset($success)): ?>
            <div class='alert alert-success py-2 d-flex align-items-center rounded-pill small'>
              <i class='fas fa-check-circle mr-2'></i><?php echo $success; ?>
            </div>
          <?php endif; ?>
          <form method="POST" action="" autocomplete="off">
            <div class="form-group mb-3">
              <div class="input-group shadow-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0 rounded-pill px-3">
                    <i class="fas fa-user text-dark"></i>
                  </span>
                </div>
                <input type="text" name="name" required
                  class="form-control bg-white border-left-0 rounded-pill py-2 pl-2" placeholder="Full name"
                  autocomplete="off">
              </div>
            </div>
            <div class="form-group mb-4">
              <div class="input-group shadow-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0 rounded-pill px-3">
                    <i class="fas fa-envelope text-dark"></i>
                  </span>
                </div>
                <input type="email" name="email" required
                  class="form-control bg-white border-left-0 rounded-pill py-2 pl-2" placeholder="Email address"
                  autocomplete="off">
              </div>
            </div>
            <div class="form-group mb-4">
              <div class="input-group shadow-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0 rounded-pill px-3">
                    <i class="fas fa-lock text-dark"></i>
                  </span>
                </div>
                <input type="password" name="password" required
                  class="form-control bg-white border-left-0 rounded-pill py-2 pl-2"
                  placeholder="Create strong password" minlength="6" maxlength="10" autocomplete="new-password">
              </div>
            </div>
            <!-- Removed role selection -->
            <button type="submit" name="register"
              class="btn btn-dark btn-block mb-3 shadow-lg rounded-pill py-2 font-weight-bold">
              <i class="fas fa-plus-circle mr-2"></i>Create Account
            </button>
            <div class="text-center">
              <a href="admin/admin_login.php" class="text-dark small">
                <i class="fas fa-arrow-left mr-2"></i>Return to Login
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