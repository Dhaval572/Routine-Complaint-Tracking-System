<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Routine Complaint Tracking System</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-btn {
            margin-bottom: 15px;
            width: 100%;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 150px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card login-card">
                    <div class="container mt-3">
                        <h2 class="text-center">Complaint Tracking System</h2>
                    </div>
                    
                    <div class="login-options">
                        <a href="user_login.php" class="btn btn-success login-btn">
                            <i class="fas fa-user"></i> User Login
                        </a>
                        
                        <a href="officer_login.php" class="btn btn-primary login-btn">
                            <i class="fas fa-briefcase"></i> Officer Login
                        </a>
                        
                        <a href="dept_head_login.php" class="btn btn-warning login-btn">
                            <i class="fas fa-building"></i> Department Head Login
                        </a>
                        
                        <a href="admin_login.php" class="btn btn-danger login-btn">
                            <i class="fas fa-lock"></i> Admin Login
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <p class="small">
                            New User? <a href="register_user.php" class="text-primary">Register Here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 4 JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</body>
</html>