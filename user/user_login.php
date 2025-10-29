<?php
include '../config.php';

// Function to display attractive Bootstrap alerts
function displayAlert($message, $type = 'danger') {
    $icon = 'exclamation-circle';
    
    if ($type == 'success') {
        $icon = 'check-circle';
    } elseif ($type == 'warning') { 
        $icon = 'exclamation-triangle';
    } elseif ($type == 'info') {
        $icon = 'info-circle';
    }
    
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show animate__animated animate__slideInDown" role="alert"
                style="animation-duration: 0.4s;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-' . $icon . ' mr-2"></i>
                    <strong>' . $message . '</strong>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
}

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
			$error = "Invalid credentials. Please check your email and password.";
		}

	} else {
		$error = "Invalid credentials. Please check your email and password.";
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>User Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="../assets/css/user_login.css">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4 mx-auto">
            <div class="text-center mb-4">
                <i class="fas fa-user-circle text-white brand-icon"></i>
            </div>
            
            <div class="login-card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="../index.php" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h3 class="header-title">Welcome Back!</h3>
                        <div class="spacer"></div>
                    </div>
                </div>

                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <?php echo displayAlert($error); ?>
                    <?php endif; ?>

                    <form method="POST" action="" autocomplete="off">
                        <div class="form-group">
                            <div class="custom-input-group">
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <input type="email" name="email" required autocomplete="off"
                                    class="custom-input" 
                                    placeholder="Email address">
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

                        <button type="submit" name="login" class="login-button">
                            Sign In
                        </button>

                        <div class="register-link">
                            <a href="register_user.php">
                                <i class="fas fa-user-plus"></i>
                                Don't have an account? Register
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