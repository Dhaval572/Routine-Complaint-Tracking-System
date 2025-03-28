<?php
include('../config.php');

if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Only allow admin type login (admin, cmo, dev)
  $sql = "SELECT * FROM users WHERE email = '$email' AND role IN ('admin', 'cmo', 'dev')";
  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['admin_id'] = $user['id'];
      $_SESSION['admin_role'] = $user['role'];
      header("Location: admin_dashboard.php");
      exit;
    } else {
      $error = "Invalid credentials";
    }
  } else {
    $error = "Invalid credentials";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background: linear-gradient(135deg, #000046 0%, #1CB5E0 100%);">
  <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="col-md-4">
      <div class="text-center mb-4">
        <i class="fas fa-user-shield text-white"
          style="font-size: 4.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"></i>
      </div>

      <div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
        <div class="card-header border-0 bg-white text-center py-4" style="border-radius: 1.5rem 1.5rem 0 0;">
          <h3 class="font-weight-bold text-dark mb-2">Administration Portal</h3>
          <p class="text-muted small mb-0">Welcome to the control panel</p>
        </div>
        <div class="card-body px-5 py-4">
          <?php if (isset($error)): ?>
            <div class='alert alert-danger py-2 d-flex align-items-center rounded-pill small'>
              <i class='fas fa-exclamation-circle mr-2'></i><?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group mb-4">
              <div class="input-group shadow-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-white border-right-0 rounded-pill px-3">
                    <i class="fas fa-envelope text-dark"></i>
                  </span>
                </div>
                <input type="email" name="email" required
                  class="form-control bg-white border-left-0 rounded-pill py-3 pl-2" placeholder="Email address"
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
                  class="form-control bg-white border-left-0 rounded-pill py-3 pl-2" placeholder="Password"
                  autocomplete="off">
              </div>
            </div>

            <button type="submit" name="login"
              class="btn btn-dark btn-block mb-4 shadow-lg rounded-pill py-3 font-weight-bold">
              <i class="fas fa-sign-in-alt mr-2"></i>Sign In
            </button>

            <div class="text-center">
              <span class="badge badge-light shadow-sm px-3 py-2 rounded-pill">
                <i class="fas fa-shield-alt text-dark mr-2"></i>
                <span class="text-muted">Administrative Access</span>
              </span>
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