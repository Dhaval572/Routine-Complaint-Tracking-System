<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>About - Complaint Tracking System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/about.css">
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary navbar-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-comments mr-2"></i>
                <span class="font-weight-bold">Complaint Tracking System</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-btn" href="index.php">
                            <i class="fas fa-home mr-2"></i>
                            <span class="font-weight-bold">Home</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title animate__animated animate__fadeIn">Complaint Management System</h1>
                    <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s">A comprehensive platform connecting citizens, officers, department heads, and administrators</p>
                    <!-- Stats section -->
                    <div class="row justify-content-center mt-4">
                        <div class="col-6 col-md-4 mb-3">
                            <div class="border border-white rounded p-3 hero-stats animate__animated animate__fadeIn animate__delay-2s">
                                <h2 class="h1 mb-0">4</h2>
                                <p class="mb-0">User Roles</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="border border-white rounded p-3 hero-stats animate__animated animate__fadeIn animate__delay-2s">
                                <h2 class="h1 mb-0">24/7</h2>
                                <p class="mb-0">Access</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- System Features -->
        <div class="mb-5">
            <h2 class="text-center mb-4">Available Features</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body">
                            <h4><i class="fas fa-users feature-icon"></i>User Management</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle check-icon"></i>Citizen Registration & Login</li>
                                <li class="mb-2"><i class="fas fa-check-circle check-icon"></i>Officer Access Portal</li>
                                <li class="mb-2"><i class="fas fa-check-circle check-icon"></i>Department Head Dashboard</li>
                                <li><i class="fas fa-check-circle check-icon"></i>Admin Control Panel</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm feature-card">
                        <div class="card-body">
                            <h4><i class="fas fa-tasks feature-icon"></i>Complaint Management</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle check-icon"></i>Submit New Complaints</li>
                                <li class="mb-2"><i class="fas fa-check-circle check-icon"></i>Track Complaint Status</li>
                                <li class="mb-2"><i class="fas fa-check-circle check-icon"></i>Department Assignment</li>
                                <li><i class="fas fa-check-circle check-icon"></i>Resolution Updates</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Roles -->
        <div class="card border-0 shadow-lg mb-5">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">System Roles</h3>
                <div class="row">
                    <div class="col-6 col-md-3 mb-4">
                        <div class="role-card text-center">
                            <div class="bg-success text-white role-icon">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5>Citizens</h5>
                            <p class="small text-muted mb-0">Submit and track complaints</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="role-card text-center">
                            <div class="bg-primary text-white role-icon">
                                <i class="fas fa-user-tie fa-2x"></i>
                            </div>
                            <h5>Officers</h5>
                            <p class="small text-muted mb-0">Handle assigned complaints</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="role-card text-center">
                            <div class="bg-info text-white role-icon">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                            <h5>Department Heads</h5>
                            <p class="small text-muted mb-0">Oversee department operations</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-4">
                        <div class="role-card text-center">
                            <div class="bg-danger text-white role-icon">
                                <i class="fas fa-user-shield fa-2x"></i>
                            </div>
                            <h5>Administrators</h5>
                            <p class="small text-muted mb-0">System management</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center py-4">
            <h3 class="mb-4">Ready to Get Started?</h3>
            <a href="user/register_user.php" class="btn btn-primary btn-lg rounded-pill cta-button">
                <i class="fas fa-user-plus mr-2"></i>Register as User
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-2">Complaint Tracking System</h5>
                    <p class="small mb-0">©2025 All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <div class="mb-2">
                        <a href="#" class="text-white mr-3 social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white mr-3 social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white social-icon"><i class="fab fa-linkedin"></i></a>
                    </div>
                    <p class="small mb-0">Contact: support@example.com</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>