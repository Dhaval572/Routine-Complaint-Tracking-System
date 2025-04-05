<?php
include('../config.php');
if (!isset($_SESSION['dept_head_id'])) {
  header("Location: dept_head_login.php");
  exit;
}

// Get department name
$dept_id = $_SESSION['dept_head_dept_id'] ?? 0;
$dept_name = "Department";
if ($dept_id) {
  $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
  $stmt->bind_param("i", $dept_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $dept_name = $row['name'];
  }
}

// Count pending complaints
$pending_count = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM complaints WHERE department_id = ? AND status = 'pending'");
$stmt->bind_param("i", $dept_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
  $pending_count = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Department Head Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    :root {
      --primary-color: #3a86ff;
      --secondary-color: #8338ec;
      --success-color: #06d6a0;
      --warning-color: #ffbe0b;
      --danger-color: #ef476f;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
    }
    
    .navbar {
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .dashboard-header {
      background: rgba(255,255,255,0.9);
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .stat-card {
      border-radius: 15px;
      transition: all 0.3s ease;
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      overflow: hidden;
    }
    
    .stat-card .icon-bg {
      position: absolute;
      top: -20px;
      right: -20px;
      font-size: 8rem;
      opacity: 0.1;
      transform: rotate(15deg);
      transition: all 0.3s ease;
    }
    
    .stat-card:hover .icon-bg {
      transform: rotate(0deg) scale(1.1);
      opacity: 0.15;
    }
    
    .card {
      border-radius: 15px;
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      height: 100%;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .card-assign {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }
    
    .card-view {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
    }
    
    .card-icon {
      font-size: 3rem;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }
    
    .card:hover .card-icon {
      transform: scale(1.2);
    }
    
    .card-body {
      z-index: 10;
      padding: 30px;
    }
    
    .card-pattern {
      position: absolute;
      bottom: 0;
      right: 0;
      width: 150px;
      height: 150px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
      transform: translate(50%, 50%);
    }
    
    .btn-logout {
      border-radius: 50px;
      padding: 8px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
      background: rgba(255,255,255,0.2);
      border: none;
    }
    
    .btn-logout:hover {
      background: rgba(255,255,255,0.3);
      transform: translateY(-2px);
    }
    
    .welcome-text {
      font-weight: 700;
      color: var(--dark-color);
      margin-bottom: 0;
    }
    
    .dept-name {
      color: var(--primary-color);
      font-weight: 300;
    }
    
    @media (max-width: 768px) {
      .container {
        padding: 15px;
      }
      
      .dashboard-header {
        padding: 15px;
        margin-bottom: 20px;
      }
      
      .card-body {
        padding: 20px;
      }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <span class="navbar-brand">
        <i class="fas fa-user-shield mr-2"></i>
        Department Head Portal
      </span>
      <div class="ml-auto">
        <a href="../logout.php" class="btn btn-logout">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </div>
    </div>
  </nav>
  
  <div class="container py-5">
    <div class="dashboard-header">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h3 class="welcome-text display-4 font-weight-bold text-primary mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['dept_head_name']); ?></h3>
          <p class="dept-name lead text-secondary mb-0"><span class="badge badge-pill badge-primary mr-2">HOD</span><?php echo htmlspecialchars($dept_name); ?> Department</p>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
          <div class="stat-card bg-warning text-white p-3">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0">Pending Complaints</h6>
                <h2 class="mb-0"><?php echo $pending_count; ?></h2>
              </div>
              <i class="fas fa-clipboard-list fa-2x"></i>
            </div>
            <i class="fas fa-clipboard-list icon-bg"></i>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <!-- Card: Assign Complaint -->
      <div class="col-md-6 mb-4">
        <div class="card card-assign" onclick="location.href='assign_complaint.php';">
          <div class="card-body text-center">
            <i class="fas fa-tasks card-icon"></i>
            <h4 class="card-title">Assign Complaint</h4>
            <p class="card-text">Delegate complaints to officers for investigation and resolution</p>
            <div class="mt-4">
              <span class="btn btn-light rounded-pill px-4">
                <i class="fas fa-arrow-right"></i> Assign Now
              </span>
            </div>
          </div>
          <div class="card-pattern"></div>
        </div>
      </div>
      
      <!-- Card: View Assigned Complaints -->
      <div class="col-md-6 mb-4">
        <div class="card card-view" onclick="location.href='view_assigned_complaints.php';">
          <div class="card-body text-center">
            <i class="fas fa-chart-line card-icon"></i>
            <h4 class="card-title">View Assigned Complaints</h4>
            <p class="card-text">Monitor progress and status of all complaints in your department</p>
            <div class="mt-4">
              <span class="btn btn-light rounded-pill px-4">
                <i class="fas fa-arrow-right"></i> View Status
              </span>
            </div>
          </div>
          <div class="card-pattern"></div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>