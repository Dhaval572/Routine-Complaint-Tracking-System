<?php
include('../config.php');
if (!isset($_SESSION['officer_id'])) {
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
    .card {
      cursor: pointer;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">Officer Dashboard</span>
    <div class="ml-auto">
      <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['officer_name']); ?></h3>
    <div class="row mt-4">
      <!-- Card: Solve Complaint -->

      <!-- Card: All Assigned Complaints -->
      <div class="col-md-4 mb-4">
        <div class="card" onclick="location.href='all_assigned_complaints.php';">
          <div class="card-body text-center">
            <h5 class="card-title">All Assigned Complaints</h5>
            <p class="card-text">View all complaints (in-progress, referred, solved).</p>
          </div>
        </div>
      </div>
      <!-- Card: All Referred Complaints -->
      <div class="col-md-4 mb-4">
        <div class="card" onclick="location.href='all_referred_complaints.php';">
          <div class="card-body text-center">
            <h5 class="card-title">All Referred Complaints</h5>
            <p class="card-text">See every complaint that has been referred to you.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
