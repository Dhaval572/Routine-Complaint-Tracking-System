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
  <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark shadow">
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
    <div class="card dashboard-header bg-primary text-white shadow">
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
            <div class="time-pill shadow-sm">
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
        <div class="card bg-primary text-white clickable-card" onclick="location.href='create_department.php';">
          <div class="card-body text-center">
            <i class="fas fa-building dashboard-icon"></i>
            <h4 class="card-title">Create Department</h4>
            <p class="card-text text-white-50">Add a new department to the system</p>
            <div class="mt-4">
              <span class="btn btn-light btn-card text-primary">
                <i class="fas fa-plus mr-2"></i>New Department
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: View Departments -->
      <div class="col-md-4 mb-4">
        <div class="card bg-success text-white clickable-card" onclick="location.href='view_departments.php';">
          <div class="card-body text-center">
            <i class="fas fa-eye dashboard-icon"></i>
            <h4 class="card-title">View Departments</h4>
            <p class="card-text text-white-50">See all registered departments</p>
            <div class="mt-4">
              <span class="btn btn-light btn-card text-success">
                <i class="fas fa-list mr-2"></i>View All
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Create Department Head -->
      <div class="col-md-4 mb-4">
        <div class="card bg-warning text-white clickable-card" onclick="location.href='create_dept_head.php';">
          <div class="card-body text-center">
            <i class="fas fa-user-tie dashboard-icon"></i>
            <h4 class="card-title">Create Dept Head</h4>
            <p class="card-text text-white-50">Register a new department head</p>
            <div class="mt-4">
              <span class="btn btn-light btn-card text-warning">
                <i class="fas fa-user-plus mr-2"></i>Add Head
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: View Department Heads -->
      <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white clickable-card" onclick="location.href='view_dept_heads.php';">
          <div class="card-body text-center">
            <i class="fas fa-users dashboard-icon"></i>
            <h4 class="card-title">View Dept Heads</h4>
            <p class="card-text text-white-50">See all department heads</p>
            <div class="mt-4">
              <span class="btn btn-light btn-card text-danger">
                <i class="fas fa-list-ul mr-2"></i>View List
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: Create Officer -->
      <div class="col-md-4 mb-4">
        <div class="card bg-info text-white clickable-card" onclick="location.href='create_officer.php';">
          <div class="card-body text-center">
            <i class="fas fa-user-shield dashboard-icon"></i>
            <h4 class="card-title">Create Officer</h4>
            <p class="card-text text-white-50">Add a new officer to a department</p>
            <div class="mt-4">
              <span class="btn btn-light btn-card text-info">
                <i class="fas fa-user-plus mr-2"></i>Add Officer
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card: View Officers -->
      <div class="col-md-4 mb-4">
        <div class="card bg-secondary text-white clickable-card" onclick="location.href='view_officers.php';">
          <div class="card-body text-center">
            <i class="fas fa-user-secret dashboard-icon"></i>
            <h4 class="card-title">View Officers</h4>
            <p class="card-text text-white-50">See all registered officers</p>
            <div class="mt-4">
              <span class="btn btn-light btn-card text-secondary">
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