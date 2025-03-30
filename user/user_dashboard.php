<?php
include '..\config.php';
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
  <title>Citizen Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Include Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Custom CSS for futuristic look -->
  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: #f8f9fa;
    }
    .card {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 10px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .metric-card {
      background: rgba(23,162,184,0.2);
      border: none;
    }
    .metric-card h2 {
      font-weight: bold;
    }
    .chart-container {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 10px;
      padding: 20px;
      margin-top: 30px;
    }
    .dashboard-btn {
      transition: background 0.3s ease;
    }
    .dashboard-btn:hover {
      background: rgba(255,255,255,0.2);
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <span class="navbar-brand">Citizen Dashboard</span>
    <div class="ml-auto">
      <a href="../logout.php" class="btn btn-outline-light dashboard-btn">Logout</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
    <!-- Metrics Row -->
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="card text-center metric-card">
          <div class="card-body">
            <h2><?php echo $total; ?></h2>
            <p>Total Complaints</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center metric-card">
          <div class="card-body">
            <h2><?php echo $pending; ?></h2>
            <p>Pending Complaints</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center metric-card">
          <div class="card-body">
            <h2><?php echo $solved; ?></h2>
            <p>Solved Complaints</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center metric-card">
          <div class="card-body">
            <h2><?php echo $referred; ?></h2>
            <p>Referred Complaints</p>
          </div>
        </div>
      </div>
    </div>
    <!-- Navigation Cards -->
    <div class="row mt-4">
      <!-- Register Complaint Card -->
      <div class="col-md-4 mb-4">
        <div class="card" onclick="location.href='register_complaint.php';">
          <div class="card-body text-center">
            <h5 class="card-title">Register Complaint</h5>
            <p class="card-text">Lodge a new complaint</p>
          </div>
        </div>
      </div>
      <!-- View Complaints Card -->
      <div class="col-md-4 mb-4">
        <div class="card" onclick="location.href='view_complaints.php';">
          <div class="card-body text-center">
            <h5 class="card-title">View Complaints</h5>
            <p class="card-text">See all your registered complaints</p>
          </div>
        </div>
      </div>
      <!-- Track Complaint Card -->
      <div class="col-md-4 mb-4">
        <div class="card" onclick="location.href='track_complaint.php';">
          <div class="card-body text-center">
            <h5 class="card-title">Track Complaint</h5>
            <p class="card-text">Get detailed tracking info</p>
          </div>
        </div>
      </div>
    </div>
    <!-- Chart Section -->
    <div class="chart-container">
      <h4 class="mb-4">Complaints Registered by Date</h4>
      <canvas id="complaintChart"></canvas>
    </div>
  </div>
  
  <script>
    // Prepare data for Chart.js
    var ctx = document.getElementById('complaintChart').getContext('2d');
    var complaintChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($chartLabels); ?>,
        datasets: [{
          label: 'Complaints',
          data: <?php echo json_encode($chartData); ?>,
          backgroundColor: 'rgba(23, 162, 184, 0.2)',
          borderColor: 'rgba(23, 162, 184, 1)',
          borderWidth: 2,
          pointBackgroundColor: 'rgba(23, 162, 184, 1)',
          pointRadius: 4,
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: {
            title: {
              display: true,
              text: 'Date'
            }
          },
          y: {
            title: {
              display: true,
              text: 'Number of Complaints'
            },
            beginAtZero: true,
            precision: 0
          }
        },
        plugins: {
          legend: {
            display: true,
            labels: {
              color: '#f8f9fa'
            }
          }
        }
      }
    });
  </script>
</body>
</html>
