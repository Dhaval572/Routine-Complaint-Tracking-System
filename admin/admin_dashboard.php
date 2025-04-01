<?php
include('../config.php');
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .card {
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: translateY(-10px);
    }
  </style>
</head>

<body class="bg-light" style="background-color:rgb(100, 247, 112) !important;">
  <nav class="navbar navbar-expand-lg navbar-dark shadow" style="background-color: #1b5e20;">
    <div class="container">
      <span class="navbar-brand">
        <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
      </span>
      <div class="ml-auto">
        <a href="../logout.php" class="btn btn-danger rounded-pill px-4">
          <i class="fas fa-sign-out-alt mr-2"></i>Logout
        </a>
      </div>
    </div>
  </nav>
  
  <div class="container py-5">
    <div class="card mb-4 border-0 rounded bg-primary text-white shadow">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-3">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Admin Control Panel
                    </h2>
                    <p class="mb-0">
                        <i class="fas fa-cogs mr-2"></i>
                        Manage departments, officers, and system operations
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="bg-white rounded-pill px-4 py-2 d-inline-block shadow-sm">
                        <i class="fas fa-clock text-info mr-2"></i>
                        <small class="text-muted font-weight-bold">
                            <?php echo date('d M Y, h:i A'); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
      <!-- Card: Create Department -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow rounded bg-primary text-white" 
            onclick="location.href='create_department.php';"
            style="cursor: pointer;">
            <div class="card-body text-center p-4">
                <i class="fas fa-building mb-3" style="font-size: 3rem;"></i>
                <h4 class="card-title">Create Department</h4>
                <p class="card-text text-white-50">Add a new department to the system</p>
                <div class="mt-4">
                    <span class="btn btn-light rounded-pill px-4 font-weight-bold text-primary">
                        <i class="fas fa-plus mr-2"></i>New Department
                    </span>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Card: View Departments -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow rounded bg-success text-white" 
            onclick="location.href='view_departments.php';"
            style="cursor: pointer;">
            <div class="card-body text-center p-4">
                <i class="fas fa-eye mb-3" style="font-size: 3rem;"></i>
                <h4 class="card-title">View Departments</h4>
                <p class="card-text text-white-50">See all registered departments</p>
                <div class="mt-4">
                    <span class="btn btn-light rounded-pill px-4 font-weight-bold text-success">
                        <i class="fas fa-list mr-2"></i>View All
                    </span>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Card: Create Department Head -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow rounded bg-warning text-white" 
            onclick="location.href='create_dept_head.php';"
            style="cursor: pointer;">
            <div class="card-body text-center p-4">
                <i class="fas fa-user-tie mb-3" style="font-size: 3rem;"></i>
                <h4 class="card-title">Create Dept Head</h4>
                <p class="card-text text-white-50">Register a new department head</p>
                <div class="mt-4">
                    <span class="btn btn-light rounded-pill px-4 font-weight-bold text-warning">
                        <i class="fas fa-user-plus mr-2"></i>Add Head
                    </span>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Card: View Department Heads -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow rounded bg-danger text-white" 
            onclick="location.href='view_dept_heads.php';"
            style="cursor: pointer;">
            <div class="card-body text-center p-4">
                <i class="fas fa-users mb-3" style="font-size: 3rem;"></i>
                <h4 class="card-title">View Dept Heads</h4>
                <p class="card-text text-white-50">See all department heads</p>
                <div class="mt-4">
                    <span class="btn btn-light rounded-pill px-4 font-weight-bold text-danger">
                        <i class="fas fa-list-ul mr-2"></i>View List
                    </span>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Card: Create Officer -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow rounded bg-info text-white" 
            onclick="location.href='create_officer.php';"
            style="cursor: pointer;">
            <div class="card-body text-center p-4">
                <i class="fas fa-user-shield mb-3" style="font-size: 3rem;"></i>
                <h4 class="card-title">Create Officer</h4>
                <p class="card-text text-white-50">Add a new officer to a department</p>
                <div class="mt-4">
                    <span class="btn btn-light rounded-pill px-4 font-weight-bold text-info">
                        <i class="fas fa-user-plus mr-2"></i>Add Officer
                    </span>
                </div>
            </div>
        </div>
      </div>
      
      <!-- Card: View Officers -->
      <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow rounded bg-secondary text-white" 
            onclick="location.href='view_officers.php';"
            style="cursor: pointer;">
            <div class="card-body text-center p-4">
                <i class="fas fa-user-secret mb-3" style="font-size: 3rem;"></i>
                <h4 class="card-title">View Officers</h4>
                <p class="card-text text-white-50">See all registered officers</p>
                <div class="mt-4">
                    <span class="btn btn-light rounded-pill px-4 font-weight-bold text-secondary">
                        <i class="fas fa-list-alt mr-2"></i>View All
                    </span>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>