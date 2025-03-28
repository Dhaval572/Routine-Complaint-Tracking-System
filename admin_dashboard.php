<?php
include('config.php');
if(!isset($_SESSION['admin_id'])){
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
  <style>
    .card {
      cursor: pointer;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand">Admin Dashboard</span>
  <div class="ml-auto">
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>
<div class="container mt-5">
  <div class="row">
    <!-- Card: Create Department -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='create_department.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Create Department</h5>
          <p class="card-text">Add a new department to the system.</p>
        </div>
      </div>
    </div>
    <!-- Card: View Departments -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='view_departments.php';">
        <div class="card-body text-center">
          <h5 class="card-title">View Departments</h5>
          <p class="card-text">See all registered departments.</p>
        </div>
      </div>
    </div>
    <!-- Card: Create Department Head -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='create_dept_head.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Create Dept Head</h5>
          <p class="card-text">Register a new department head.</p>
        </div>
      </div>
    </div>
    <!-- Card: View Department Heads -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='view_dept_heads.php';">
        <div class="card-body text-center">
          <h5 class="card-title">View Dept Heads</h5>
          <p class="card-text">See all department heads.</p>
        </div>
      </div>
    </div>
    <!-- Card: Create Officer -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='create_officer.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Create Officer</h5>
          <p class="card-text">Add a new officer to a department.</p>
        </div>
      </div>
    </div>
    <!-- Card: View Officers -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='view_officers.php';">
        <div class="card-body text-center">
          <h5 class="card-title">View Officers</h5>
          <p class="card-text">See all registered officers.</p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
