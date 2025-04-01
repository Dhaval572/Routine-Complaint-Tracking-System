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

// Update profile
if (isset($_POST['update_profile'])) {
  $name = trim($conn->real_escape_string($_POST['name']));
  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  
  // Validate inputs
  if (empty($name) || empty($email)) {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Please enter a valid email address.";
  } else {
    // Check if email already exists (but not for current user)
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $user_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
      $error = "Email already registered by another user.";
    } else {
      // Update user profile
      $update = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
      $update->bind_param("ssi", $name, $email, $user_id);
      
      if ($update->execute()) {
        $_SESSION['user_name'] = $name; // Update session with new name
        $success = "Profile updated successfully.";
        // Refresh user data
        $user['name'] = $name;
        $user['email'] = $email;
      } else {
        $error = "Error updating profile. Please try again.";
      }
      $update->close();
    }
    $check->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
              <div class="alert alert-success alert-dismissible fade show rounded-pill" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
              <div class="alert alert-danger alert-dismissible fade show rounded-pill" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i> <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php endif; ?>

            <div class="text-center mb-4">
              <div class="text-white rounded-circle p-3 d-inline-block mb-3" style="background-color: #1565c0; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                <i class="fas fa-user-circle" style="font-size: 3rem;"></i>
              </div>
              <h5 class="font-weight-bold"><?= htmlspecialchars($user['name']) ?></h5>
              <p class="text-muted mb-0"><i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($user['email']) ?></p>
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
                    class="form-control rounded-pill" style="border-top-left-radius: 0; border-bottom-left-radius: 0; margin-left: -1px; background-color: #e6f2ff;"
                    placeholder="Your full name"
                    value="<?= htmlspecialchars($user['name']) ?>">
                </div>
              </div>
              
              <div class="form-group">
                <label for="email" class="font-weight-bold">Email Address</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-primary text-white rounded-pill" style="border-top-right-radius: 0; border-bottom-right-radius: 0; z-index: 1;">
                      <i class="fas fa-envelope"></i>
                    </span>
                  </div>
                  <input type="email" id="email" name="email" required 
                    class="form-control rounded-pill" style="border-top-left-radius: 0; border-bottom-left-radius: 0; margin-left: -1px; background-color: #e6f2ff;"
                    placeholder="Your email address"
                    value="<?= htmlspecialchars($user['email']) ?>">
                </div>
              </div>
              
              <div class="row mt-4">
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