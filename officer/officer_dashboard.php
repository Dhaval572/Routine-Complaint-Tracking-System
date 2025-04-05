<?php
include('../config.php');
if (!isset($_SESSION['officer_id'])) {
  header("Location: officer_login.php");
  exit;
}

// We'll remove the problematic database queries and use placeholder values instead
$pending_count = 0;
$inprogress_count = 0;
$solved_count = 0;

// Once the database structure is updated, you can uncomment these queries

$officer_id = $_SESSION['officer_id'];
$pending_query = $conn->prepare("SELECT COUNT(*) as count FROM complaints WHERE officer_id = ? AND status = 'pending'");
$pending_query->bind_param("i", $officer_id);
$pending_query->execute();
$pending_result = $pending_query->get_result();
$pending_count = $pending_result->fetch_assoc()['count'];

$inprogress_query = $conn->prepare("SELECT COUNT(*) as count FROM complaints WHERE officer_id = ? AND status = 'in-progress'");
$inprogress_query->bind_param("i", $officer_id);
$inprogress_query->execute();
$inprogress_result = $inprogress_query->get_result();
$inprogress_count = $inprogress_result->fetch_assoc()['count'];

$solved_query = $conn->prepare("SELECT COUNT(*) as count FROM complaints WHERE officer_id = ? AND status = 'solved'");
$solved_query->bind_param("i", $officer_id);
$solved_query->execute();
$solved_result = $solved_query->get_result();
$solved_count = $solved_result->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Officer Dashboard</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
      color: #333;
      min-height: 100vh;
    }

    .navbar {
      background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 1rem 2rem;
    }

    .welcome-section {
      background: linear-gradient(135deg, #f8f9fc 0%, #eaedff 100%);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      border-left: 4px solid #4e73df;
      position: relative;
      overflow: hidden;
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Custom background for cards */
    .pending-card,
    .progress-card,
    .solved-card,
    .assigned-card,
    .referred-card,
    .pending-action-card {
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
      animation: slideIn 0.5s ease-in-out;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .stat-card:hover,
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .container {
      animation: containerFade 1s ease-in-out;
    }

    @keyframes containerFade {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .quick-actions-title {
      position: relative;
      z-index: 1;
      margin-bottom: 2rem;
      animation: titleSlide 0.7s ease-in-out;
    }

    @keyframes titleSlide {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .card-icon {
      transition: all 0.3s ease;
    }

    .card:hover .card-icon {
      transform: scale(1.1);
    }

    .navbar-brand {
      font-weight: 600;
      font-size: 1.4rem;
      letter-spacing: 0.5px;
    }

    .welcome-section {
      background: linear-gradient(135deg, #f8f9fc 0%, #eaedff 100%);
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      border-left: 4px solid #4e73df;
      position: relative;
      overflow: hidden;
    }

    .welcome-section::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 150px;
      height: 150px;
      background: radial-gradient(circle, rgba(78, 115, 223, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
      border-radius: 50%;
      transform: translate(30%, -30%);
    }

    .welcome-title {
      color: #4e73df;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
      color: #6c757d;
      font-size: 1rem;
    }

    .stats-container {
      margin-bottom: 2rem;
    }

    .quick-actions-title {
      color: #ffffff;
      font-weight: 700;
      margin-bottom: 1.5rem;
      padding: 0.8rem 1.5rem;
      background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
      border-radius: 10px;
      display: inline-block;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      height: 100%;
    }

    /* Custom background for pending complaints card */
    .pending-card {
      background: linear-gradient(135deg, #fff9c4 0%, #fffde7 100%);
      /* Enhanced yellow gradient */
      border-left: 4px solid #f6c23e;
    }

    /* Custom background for in-progress complaints card */
    .progress-card {
      background: linear-gradient(135deg, #bbdefb 0%, #e3f2fd 100%);
      /* Enhanced blue gradient */
      border-left: 4px solid #4e73df;
    }

    /* Custom background for solved complaints card */
    .solved-card {
      background: linear-gradient(135deg, #c8e6c9 0%, #e8f5e9 100%);
      /* Enhanced green gradient */
      border-left: 4px solid #1cc88a;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
      font-size: 2rem;
      margin-bottom: 1rem;
      display: inline-block;
      padding: 15px;
      border-radius: 50%;
    }

    .pending-icon {
      color: #f6c23e;
      background-color: rgba(246, 194, 62, 0.1);
    }

    .progress-icon {
      color: #4e73df;
      background-color: rgba(78, 115, 223, 0.1);
    }

    .solved-icon {
      color: #1cc88a;
      background-color: rgba(28, 200, 138, 0.1);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      height: 100%;
      cursor: pointer;
    }

    /* Custom backgrounds for action cards */
    .assigned-card {
      background: linear-gradient(135deg, #e8eaf6 0%, #f3e5f5 100%);
      border-left: 4px solid #4e73df;
    }

    .referred-card {
      background: linear-gradient(135deg, #e0f7fa 0%, #e0f2f1 100%);
      border-left: 4px solid #36b9cc;
    }

    .pending-action-card {
      background: linear-gradient(135deg, #fff3e0 0%, #fffde7 100%);
      border-left: 4px solid #f6c23e;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .card-body {
      padding: 2rem;
    }

    .card-title {
      color: #4e73df;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .card-text {
      color: #6c757d;
    }

    .card-icon {
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
      color: #4e73df;
    }

    .btn-logout {
      background-color: transparent;
      border: 1px solid rgba(255, 255, 255, 0.8);
      border-radius: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      transition: all 0.2s ease;
      padding: 0.4rem 1.2rem;
    }

    .btn-logout:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      transform: translateY(-1px);
    }

    .btn-logout i {
      margin-right: 5px;
    }

    /* Footer styles removed */

    @media (max-width: 768px) {
      .navbar {
        padding: 0.8rem 1rem;
      }

      .welcome-section {
        padding: 1.5rem;
      }

      .stat-card,
      .card {
        margin-bottom: 1rem;
      }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <span class="navbar-brand"><i class="fas fa-shield-alt mr-2"></i>Officer Dashboard</span>
    <div class="ml-auto">
      <a href="../logout.php" class="btn btn-logout" title="Logout">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </div>
  </nav>

  <div class="container py-5">
    <div class="welcome-section">
      <h3 class="welcome-title">Welcome, <?php echo htmlspecialchars($_SESSION['officer_name']); ?></h3>
      <p class="welcome-subtitle">Here's an overview of your complaint management activities</p>
    </div>

    <!-- Stats Row -->
    <div class="row stats-container">
      <div class="col-md-4 mb-4">
        <div class="stat-card text-center pending-card">
          <div class="stat-icon pending-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-value"><?php echo $pending_count; ?></div>
          <div class="stat-label">Pending Complaints</div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="stat-card text-center progress-card">
          <div class="stat-icon progress-icon">
            <i class="fas fa-spinner"></i>
          </div>
          <div class="stat-value"><?php echo $inprogress_count; ?></div>
          <div class="stat-label">In-Progress Complaints</div>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <div class="stat-card text-center solved-card">
          <div class="stat-icon solved-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-value"><?php echo $solved_count; ?></div>
          <div class="stat-label">Solved Complaints</div>
        </div>
      </div>
    </div>

    <h4 class="quick-actions-title">Quick Actions</h4>

    <div class="row">
      <!-- Card: All Assigned Complaints -->
      <div class="col-md-6 mb-4">
        <div class="card assigned-card" onclick="location.href='all_assigned_complaints.php';">
          <div class="card-body text-center">
            <div class="card-icon">
              <i class="fas fa-tasks"></i>
            </div>
            <h5 class="card-title">All Assigned Complaints</h5>
            <p class="card-text">View all complaints (in-progress, referred, solved).</p>
          </div>
        </div>
      </div>

      <!-- Card: All Referred Complaints -->
      <div class="col-md-6 mb-4">
        <div class="card referred-card" onclick="location.href='all_referred_complaints.php';">
          <div class="card-body text-center">
            <div class="card-icon">
              <i class="fas fa-exchange-alt"></i>
            </div>
            <h5 class="card-title">All Referred Complaints</h5>
            <p class="card-text">See every complaint that has been referred to you.</p>
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