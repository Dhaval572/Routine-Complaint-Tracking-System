<?php
require_once '../config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Validation
    $errors = [];
    
    // Name validation
    if (strlen($name) < 3) {
        $errors[] = ['type' => 'error', 'message' => 'Name must be at least 3 characters long'];
    }
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = ['type' => 'error', 'message' => 'Please enter a valid email address'];
    }
    
    // Check if email already exists
    $check_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $errors[] = ['type' => 'error', 'message' => 'Email already registered'];
    }
    
    // Password validation
    if (strlen($password) < 6) {
        $errors[] = ['type' => 'error', 'message' => 'Password must be at least 6 characters long'];
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Registration successful! Please login.'
            ];
            header('Location: user_login.php');
            exit();
        } else {
            $errors[] = ['type' => 'error', 'message' => 'Registration failed. Please try again.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/register_user.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto">
            <div class="text-center mb-4">
                <i class="fas fa-user-plus text-white brand-icon"></i>
            </div>
            
            <div class="login-card register-card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="../index.php" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h3 class="header-title">Create Account</h3>
                        <div class="spacer"></div>
                    </div>
                    <p class="header-subtitle">Join our community today</p>
                </div>

                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <?php foreach ($errors as $error): ?>
                            <div class="alert alert-danger alert-dismissible fade show animated fadeInDown" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <strong><?php echo $error['message']; ?></strong>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Rest of the form remains the same -->
                    <form method="POST" action="" autocomplete="off">
                        <div class="form-row">
                            <div class="form-group flex-grow-1">
                                <div class="custom-input-group">
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <input type="text" name="name" required autocomplete="off"
                                        class="custom-input" 
                                        placeholder="Full Name">
                                </div>
                            </div>
                            <div class="form-group flex-grow-1">
                                <div class="custom-input-group">
                                    <div class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input type="email" name="email" required autocomplete="off"
                                        class="custom-input" 
                                        placeholder="Email Address">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-input-group">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" name="password" required autocomplete="off"
                                    class="custom-input" 
                                    placeholder="Password">
                            </div>
                        </div>

                        <div class="terms-check">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">
                                I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            </label>
                        </div>

                        <button type="submit" name="register" class="login-button register-button">
                            Create Account
                        </button>

                        <div class="register-link login-link">
                            <a href="user_login.php">
                                <i class="fas fa-sign-in-alt"></i>
                                Already have an account? Login
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
