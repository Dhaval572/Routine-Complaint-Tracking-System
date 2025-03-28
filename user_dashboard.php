<?php
include('config.php');
if(!isset($_SESSION['user_id'])){
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
  <style>
    .card { cursor: pointer; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand">Citizen Dashboard</span>
  <div class="ml-auto">
    <a href="logout.php" class="btn btn-outline-light">Logout</a>
  </div>
</nav>
<div class="container mt-5">
  <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
  <div class="row mt-4">
    <!-- Card: Register Complaint -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='register_complaint.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Register Complaint</h5>
          <p class="card-text">Lodge a new complaint.</p>
        </div>
      </div>
    </div>
    <!-- Card: View All Complaints -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='view_complaints.php';">
        <div class="card-body text-center">
          <h5 class="card-title">View Complaints</h5>
          <p class="card-text">See all your registered complaints.</p>
        </div>
      </div>
    </div>
    <!-- Card: Track Complaint -->
    <div class="col-md-4 mb-4">
      <div class="card" onclick="location.href='track_complaint.php';">
        <div class="card-body text-center">
          <h5 class="card-title">Track Complaint</h5>
          <p class="card-text">Get detailed tracking info.</p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
    