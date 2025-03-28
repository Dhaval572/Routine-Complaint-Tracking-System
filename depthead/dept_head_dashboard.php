<?php
include('../config.php');
if (!isset($_SESSION['dept_head_id'])) {
  header("Location: dept_head_login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dept Head Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .card {
      cursor: pointer;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">Dept Head Dashboard</span>
    <div class="ml-auto">
      <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['dept_head_name']); ?></h3>
    <div class="row mt-4">
      <!-- Card: Assign Complaint -->
      <div class="col-md-6 mb-4">
        <div class="card" onclick="location.href='assign_complaint.php';">
          <div class="card-body text-center">
            <h5 class="card-title">Assign Complaint</h5>
            <p class="card-text">Assign complaints to officers.</p>
          </div>
        </div>
      </div>
      <!-- Card: View Assigned Complaints -->
      <div class="col-md-6 mb-4">
        <div class="card" onclick="location.href='view_assigned_complaints.php';">
          <div class="card-body text-center">
            <h5 class="card-title">View Assigned Complaints</h5>
            <p class="card-text">See status of complaints in your department.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>