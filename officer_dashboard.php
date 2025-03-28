<?php
include('config.php');
if(!isset($_SESSION['officer_id'])){
    header("Location: officer_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Officer Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .card { cursor: pointer; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand">Officer Dashboard</span>
  <div class="ml-auto">
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>
<div class="container mt-5">
  <h3>Welcome, <?php echo htmlspecialchars($_SESSION['officer_name']); ?></h3>
  <div class="row mt-4">
    <!-- Card: Solve Complaint -->
    <div class="col-md-3 mb-4">
      <div class="card" onclick="location.href='solve_complaint.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Solve Complaint</h5>
          <p class="card-text">Update with response and remark.</p>
        </div>
      </div>
    </div>
    <!-- Card: Refer Complaint -->
    <div class="col-md-3 mb-4">
      <div class="card" onclick="location.href='refer_complaint.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Refer Complaint</h5>
          <p class="card-text">Refer complaint to another officer.</p>
        </div>
      </div>
    </div>
    <!-- Card: View All Complaints -->
    <div class="col-md-3 mb-4">
      <div class="card" onclick="location.href='view_all_complaints.php';">
        <div class="card-body text-center">
          <h5 class="card-title">My Complaints</h5>
          <p class="card-text">View complaints assigned to you.</p>
        </div>
      </div>
    </div>
    <!-- Card: View Referred Complaints -->
    <div class="col-md-3 mb-4">
      <div class="card" onclick="location.href='view_referred_complaints.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Referred Complaints</h5>
          <p class="card-text">Complaints referred by others.</p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
