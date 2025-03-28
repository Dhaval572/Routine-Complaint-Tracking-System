<?php
include('../config.php');

if (isset($_POST['register'])) {
  $name = $conn->real_escape_string($_POST['name']);
  $email = $conn->real_escape_string($_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  // If you need to store phone or any extra fields, ensure your users table is updated accordingly.
  // For now, we only insert name, email, and password for a citizen.
  $role = 'citizen';

  // Check if the email already exists
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
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Citizen Registration</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2 class="mt-5 text-center">Citizen Registration</h2>
        <?php
        if (isset($error)) {
          echo "<div class='alert alert-danger'>$error</div>";
        }
        if (isset($success)) {
          echo "<div class='alert alert-success'>$success</div>";
        }
        ?>
        <form method="POST" action="">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required class="form-control" placeholder="Enter your name">
          </div>
          <div class="form-group">
            <label>Email address</label>
            <input type="email" name="email" required class="form-control" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required class="form-control" placeholder="Enter password">
          </div>
          <!-- If you wish to add phone number or other fields, add them here -->
          <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="text-center mt-3"><a href="user_login.php">Already have an account? Login here</a></p>
      </div>
    </div>
  </div>
</body>

</html>