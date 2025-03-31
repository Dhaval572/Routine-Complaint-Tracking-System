<?php
include '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
	header("Location: user_login.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>User Dashboard</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background:rgb(75, 184, 199);">
    <nav class="navbar navbar-expand-lg navbar-dark shadow" style="background: rgba(0, 57, 117, 0.95);">
        <div class="container">
            <span class="navbar-brand">
                <i class="fas fa-columns mr-2"></i>Citizen Dashboard
            </span>
            <!-- User dropdown menu -->
            <div class="ml-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn dropdown-toggle rounded-pill px-4 mr-2" type="button" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);">
                        <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 rounded-lg" style="min-width: 14rem;">
                        <span class="dropdown-item-text text-muted small px-4 py-2">Logged in as <strong><?php echo $_SESSION['user_name']; ?></strong></span>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item px-4 py-2" href="profile.php">
                            <i class="fas fa-user-circle mr-2 text-primary"></i> My Profile
                        </a>
                        <a class="dropdown-item px-4 py-2" href="change_password.php">
                            <i class="fas fa-key mr-2 text-primary"></i> Change Password
                        </a>
                        <a class="dropdown-item px-4 py-2 text-danger" href="delete_account.php">
                            <i class="fas fa-user-times mr-2"></i> Delete Account
                        </a>
                    </div>
                </div>
                <a href="../logout.php" class="btn rounded-pill px-4" style="background: linear-gradient(45deg, #e74c3c, #c0392b); color: white; box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card shadow-lg mb-4 border-0 rounded-lg" 
            style="background: linear-gradient(120deg, rgba(16, 40, 90, 0.98), rgba(26, 55, 126, 0.98));">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-3 text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                            <i class="fas fa-user-circle text-white mr-2"></i>
                            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </h2>
                        <p class="text-white mb-0">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Manage your complaints and track their progress through our system
                        </p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="bg-white rounded-pill px-4 py-2 d-inline-block shadow-sm">
                            <i class="fas fa-clock text-info mr-2"></i>
                            <small class="text-muted font-weight-bold">Last Login:
                                <?php echo date('d M Y, h:i A'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-lg rounded-lg bg-gradient hover-lift" 
                    onclick="location.href='register_complaint.php';"
                    style="cursor: pointer; background: linear-gradient(45deg, rgba(12, 52, 61, 0.98), rgba(34, 111, 57, 0.98)); transition: transform 0.2s;">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-file-alt text-white mb-3" style="font-size: 3rem;"></i>
                        <h4 class="card-title text-white">Register Complaint</h4>
                        <p class="card-text text-white-50">Submit a new complaint or grievance</p>
                        <div class="mt-4">
                            <span class="btn rounded-pill px-4" style="background: linear-gradient(45deg, #00b09b, #96c93d); color: white; box-shadow: 0 4px 15px rgba(0, 176, 155, 0.3);">
                                <i class="fas fa-plus mr-2"></i>New Complaint
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-lg rounded-lg bg-gradient hover-lift"
                    onclick="location.href='view_complaints.php';"
                    style="cursor: pointer; background: linear-gradient(45deg, rgba(44, 62, 80, 0.95), rgba(52, 152, 219, 0.95)); transition: transform 0.2s;">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-list-alt text-white mb-3" style="font-size: 3rem;"></i>
                        <h4 class="card-title text-white">View Complaints</h4>
                        <p class="card-text text-white-50">Access all your submitted complaints</p>
                        <div class="mt-4">
                            <span class="btn rounded-pill px-4" style="background: linear-gradient(45deg, #4facfe, #00f2fe); color: white; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);">
                                <i class="fas fa-eye mr-2"></i>View All
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-lg rounded-lg bg-gradient hover-lift"
                    onclick="location.href='track_complaint.php';"
                    style="cursor: pointer; background: linear-gradient(45deg, rgba(74, 20, 140, 0.95), rgba(124, 77, 255, 0.95)); transition: transform 0.2s;">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-search-location text-white mb-3" style="font-size: 3rem;"></i>
                        <h4 class="card-title text-white">Track Complaint</h4>
                        <p class="card-text text-white-50">Monitor the status of your complaints</p>
                        <div class="mt-4">
                            <span class="btn rounded-pill px-4" style="background: linear-gradient(45deg, #667eea, #764ba2); color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                                <i class="fas fa-map-marker-alt mr-2"></i>Track Now
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove the standalone dropdown menu that was at the bottom -->
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Add hover effect for cards
        document.querySelectorAll('.hover-lift').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px)';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>