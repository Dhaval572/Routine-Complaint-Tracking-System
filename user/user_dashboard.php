<?php
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Query metrics for the logged-in citizen
$sqlTotal = "SELECT COUNT(*) AS total FROM complaints WHERE citizen_id = '$user_id'";
$resultTotal = $conn->query($sqlTotal);
$total = ($resultTotal && $row = $resultTotal->fetch_assoc()) ? $row['total'] : 0;

$sqlSolved = "SELECT COUNT(*) AS solved FROM complaints WHERE citizen_id = '$user_id' AND LOWER(status) = 'solved'";
$resultSolved = $conn->query($sqlSolved);
$solved = ($resultSolved && $row = $resultSolved->fetch_assoc()) ? $row['solved'] : 0;

$sqlPending = "SELECT COUNT(*) AS pending FROM complaints WHERE citizen_id = '$user_id' AND LOWER(status) IN ('registered','in_progress')";
$resultPending = $conn->query($sqlPending);
$pending = ($resultPending && $row = $resultPending->fetch_assoc()) ? $row['pending'] : 0;

$sqlReferred = "SELECT COUNT(*) AS referred FROM complaints WHERE citizen_id = '$user_id' AND LOWER(status) = 'referred'";
$resultReferred = $conn->query($sqlReferred);
$referred = ($resultReferred && $row = $resultReferred->fetch_assoc()) ? $row['referred'] : 0;

// Query for complaints registered by date for the chart
$sqlDates = "SELECT DATE(created_at) AS reg_date, COUNT(*) AS count 
             FROM complaints 
             WHERE citizen_id = '$user_id' 
             GROUP BY DATE(created_at)
             ORDER BY reg_date ASC";
$resultDates = $conn->query($sqlDates);
$chartLabels = [];
$chartData = [];
if ($resultDates) {
    while ($row = $resultDates->fetch_assoc()) {
        $chartLabels[] = $row['reg_date'];
        $chartData[] = $row['count'];
    }
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
                    <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 rounded-lg" style="min-width: 16rem; transform: translateY(10px); background: linear-gradient(to bottom, #003975, #0a4d8c);">
                        <div class="p-3 border-bottom text-center" style="border-color: rgba(255,255,255,0.1) !important;">
                            <div class="d-inline-block mb-2">
                                <div class="bg-info text-white rounded-circle p-2 d-inline-block" style="width: 60px; height: 60px; text-align: center; box-shadow: 0 0 10px rgba(23, 162, 184, 0.4);">
                                    <i class="fas fa-user-circle" style="font-size: 2.2rem; line-height: 1.4;"></i>
                                </div>
                            </div>
                            <h6 class="mb-0 font-weight-bold text-white"><?php echo $_SESSION['user_name']; ?></h6>
                            <small class="text-light">Citizen Account</small>
                        </div>
                        <div class="p-2">
                            <a class="dropdown-item px-4 py-3 rounded-lg mb-1 hover-bg text-white" href="profile.php" style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-center">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(45deg, #00b09b, #96c93d); box-shadow: 0 0 8px rgba(0, 176, 155, 0.3); display: flex; align-items: center; justify-content: center;" class="mr-3">
                                        <i class="fas fa-user-circle text-white" style="font-size: 1.1rem;"></i>
                                    </div>
                                    <span>My Profile</span>
                                </div>
                            </a>
                            <a class="dropdown-item px-4 py-3 rounded-lg mb-1 hover-bg text-white" href="change_password.php" style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning text-white rounded-circle p-2 mr-3" style="width: 36px; height: 36px; text-align: center;">
                                        <i class="fas fa-key" style="font-size: 1.1rem;"></i>
                                    </div>
                                    <span>Change Password</span>
                                </div>
                            </a>
                            <a class="dropdown-item px-4 py-3 rounded-lg mb-1 hover-bg text-white" href="delete_account.php" style="transition: all 0.2s ease;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger text-white rounded-circle p-2 mr-3" style="width: 36px; height: 36px; text-align: center;">
                                        <i class="fas fa-user-times" style="font-size: 1.1rem;"></i>
                                    </div>
                                    <span>Delete Account</span>
                                </div>
                            </a>
                        </div>
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

        // Add hover effect for dropdown items
        document.querySelectorAll('.hover-bg').forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
            });
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = 'transparent';
            });
        });
    </script>
</body>
</html>