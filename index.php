<?php
include 'config.php';

// Process account deletion message
$show_delete_alert = false;
if (isset($_SESSION['show_delete_modal']) && $_SESSION['show_delete_modal'] === true) {
  $show_delete_alert = true;
  unset($_SESSION['show_delete_modal']); // Clear immediately after checking
}

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$user_data = null;

// Fetch user data if logged in 
if ($user_logged_in) {
  $user_id = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user_data = $result->fetch_assoc();
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Routine Complaint Tracking System</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .delete-alert {
      animation: slideInDown 0.5s ease-out, fadeOut 0.5s ease-in 5s forwards;
      position: fixed;
      top: 20px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 9999;
      width: auto;
      min-width: 300px;
    }

    @keyframes slideInDown {
      from {
        top: -100px;
        opacity: 0;
      }

      to {
        top: 20px;
        opacity: 1;
      }
    }

    @keyframes fadeOut {
      from {
        opacity: 1;
      }

      to {
        opacity: 0;
        visibility: hidden;
      }
    }
  </style>
</head>

<body>
  <!-- Add account deleted notification here -->
  <?php if (isset($_GET['account_deleted'])): ?>
    <div class="container mt-3">
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle mr-2"></i> Your account has been successfully deleted.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Profile Modal with Bootstrap-only styling -->
  <?php if ($user_logged_in): ?>
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="profileModalLabel">
              <i class="fas fa-user-circle mr-2"></i>My Profile
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body bg-light">
            <div class="text-center mb-4">
              <div class="bg-primary text-white rounded-circle p-3 d-inline-block mb-3">
                <i class="fas fa-user-circle" style="font-size: 3rem;"></i>
              </div>
              <h5 class="font-weight-bold"><?= htmlspecialchars($user_data['name']) ?></h5>
              <p class="text-muted"><i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($user_data['email']) ?></p>
            </div>

            <div class="row">
              <div class="col-sm-6 mb-3">
                <a href="user/profile.php" class="btn btn-primary btn-block rounded-pill">
                  <i class="fas fa-user-edit mr-2"></i>Edit Profile
                </a>
              </div>
              <div class="col-sm-6 mb-3">
                <a href="user/change_password.php" class="btn btn-outline-primary btn-block rounded-pill">
                  <i class="fas fa-key mr-2"></i>Change Password
                </a>
              </div>
            </div>

            <div class="mt-2">
              <a href="user/delete_account.php" class="btn btn-outline-danger btn-block rounded-pill">
                <i class="fas fa-user-times mr-2"></i>Delete Account
              </a>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">Close</button>
            <a href="logout.php" class="btn btn-danger rounded-pill">
              <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm py-2">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <i class="fas fa-comments mr-2"></i>
        <span style="font-size: 1.1rem;">Complaint Tracking System</span>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item mb-2 mb-lg-0">
            <a class="nav-link px-4 py-2 font-weight-bold btn btn-primary rounded-pill" 
              href="search_complaint.php" style="font-size: 1rem;">
              <i class="fas fa-search mr-1"></i> Search Complaint
            </a>
          </li>
        </ul>
        <ul class="navbar-nav ml-auto">
          <?php if ($user_logged_in): ?>
            <li class="nav-item mb-2 mb-lg-0">
              <a class="nav-link btn btn-light text-primary rounded-pill px-3 py-2" href="#" data-toggle="modal"
                data-target="#profileModal">
                <i class="fas fa-user mr-1"></i> My Profile
              </a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link px-3 py-2 d-inline-block" href="about.php" title="About">
              <i class="fas fa-info-circle fa-2x" style="color: white; text-shadow: 0 0 5px rgba(255,255,255,0.5);"></i>
              <span class="d-inline d-lg-none ml-2">About</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <style>
    /* Mobile navbar improvements */
    @media (max-width: 991px) {
      .navbar-collapse {
        background-color: rgba(0, 123, 255, 0.95);
        padding: 15px;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-top: 10px;
      }

      .navbar-nav .nav-item {
        margin-bottom: 10px;
      }

      .navbar-nav .nav-item:last-child {
        margin-bottom: 0;
      }

      .navbar-nav .nav-link {
        text-align: left;
        padding: 10px 15px;
        border-radius: 5px;
      }

      .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
      }

      .navbar-toggler {
        border: none;
        padding: 0.25rem 0.5rem;
        font-size: 1.1rem;
      }

      .navbar-toggler:focus {
        outline: none;
        box-shadow: none;
      }
    }
  </style>

  <!-- Enhanced Feedback button - more attractive and responsive -->
  <div class="position-fixed" style="bottom: 30px; right: 30px; z-index: 1000;">
    <a href="feedback.php"
      class="btn btn-warning rounded-circle shadow-lg p-3 d-flex align-items-center justify-content-center feedback-btn"
      style="width: 60px; height: 60px; transition: all 0.3s ease;" title="Provide Feedback"
      onmouseover="this.classList.add('pulse')" onmouseout="this.classList.remove('pulse')">
      <i class="fas fa-comment fa-lg"></i>
    </a>
    <span class="badge badge-danger position-absolute"
      style="top: -5px; right: -5px; animation: pulse 1.5s infinite;">New</span>
    <div class="feedback-label bg-dark text-white px-3 py-1 rounded position-absolute"
      style="right: 70px; top: 15px; opacity: 0; transition: opacity 0.3s ease; white-space: nowrap;">Send Feedback
    </div>
  </div>

  <style>
    @keyframes pulse {
      0% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.1);
      }

      100% {
        transform: scale(1);
      }
    }

    .pulse {
      animation: pulse 1.5s infinite;
    }

    .feedback-btn:hover+.feedback-label,
    .feedback-btn:hover {
      opacity: 1 !important;
      transform: scale(1.1);
    }

    @media (max-width: 768px) {
      .position-fixed {
        bottom: 20px !important;
        right: 20px !important;
      }

      .feedback-btn {
        width: 50px !important;
        height: 50px !important;
      }
    }
  </style>

  <div class="bg-primary text-white py-5">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8 text-center text-lg-left">
          <h1 class="display-4 font-weight-bold mb-4">Welcome to Complaint Tracking System</h1>
          <p class="lead mb-4">
            Efficiently manage and track complaints with our comprehensive system.
            We provide a streamlined solution for handling citizen complaints and ensuring timely resolutions.
          </p>
          <div class="d-flex flex-wrap justify-content-center justify-content-lg-start">
            <a href="user/register_user.php" class="btn btn-light btn-lg rounded-pill mr-3 mb-3">
              <i class="fas fa-user-plus mr-2"></i>Get Started
            </a>
            <a href="learn_more.php" class="btn btn-outline-light btn-lg rounded-pill mb-3">
              <i class="fas fa-info-circle mr-2"></i>Learn More
            </a>
            <!-- Removed the temporary View Complaints button -->
          </div>
          <div class="mt-4 d-none d-lg-block">
            <div class="row">
              <div class="col-auto pr-4 border-right">
                <h3 class="font-weight-bold mb-1">24/7</h3>
                <p class="mb-0 small">Support</p>
              </div>
              <div class="col-auto pr-4 border-right">
                <h3 class="font-weight-bold mb-1">100%</h3>
                <p class="mb-0 small">Secure</p>
              </div>
              <div class="col-auto">
                <h3 class="font-weight-bold mb-1">Fast</h3>
                <p class="mb-0 small">Response</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block">
          <div class="p-4 bg-white rounded-lg shadow-lg text-dark">
            <div class="d-flex align-items-center mb-3">
              <i class="fas fa-check-circle text-success fa-2x mr-3"></i>
              <div>
                <h5 class="mb-0">Easy Submission</h5>
                <small>Submit complaints easily</small>
              </div>
            </div>
            <div class="d-flex align-items-center mb-3">
              <i class="fas fa-history text-primary fa-2x mr-3"></i>
              <div>
                <h5 class="mb-0">Real-time Tracking</h5>
                <small>Track status instantly</small>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <i class="fas fa-shield-alt text-danger fa-2x mr-3"></i>
              <div>
                <h5 class="mb-0">Secure System</h5>
                <small>Your data is protected</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container py-5">
    <div class="row mb-5">
      <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
          <div class="card-body text-center p-4">
            <div class="icon-wrapper mb-3">
              <i class="fas fa-ticket-alt fa-3x mb-3"></i>
            </div>
            <h5 class="card-title">Total Complaints</h5>
            <h2 class="mb-0 font-weight-bold">1,234</h2>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-success text-white">
          <div class="card-body text-center p-4">
            <div class="icon-wrapper mb-3">
              <i class="fas fa-check-circle fa-3x mb-3"></i>
            </div>
            <h5 class="card-title">Resolved</h5>
            <h2 class="mb-0 font-weight-bold">789</h2>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-warning text-white">
          <div class="card-body text-center p-4">
            <div class="icon-wrapper mb-3">
              <i class="fas fa-clock fa-3x mb-3"></i>
            </div>
            <h5 class="card-title">Pending</h5>
            <h2 class="mb-0 font-weight-bold">445</h2>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-info text-white">
          <div class="card-body text-center p-4">
            <div class="icon-wrapper mb-3">
              <i class="fas fa-users fa-3x mb-3"></i>
            </div>
            <h5 class="card-title">Users</h5>
            <h2 class="mb-0 font-weight-bold">5,678</h2>
          </div>
        </div>
      </div>
    </div>

    <style>
      /* Stats boxes enhancements */
      .card {
        transition: all 0.3s ease;
        overflow: hidden;
        border-radius: 12px;
      }

      .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
      }

      .icon-wrapper {
        position: relative;
        display: inline-block;
      }

      .icon-wrapper:after {
        content: '';
        position: absolute;
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 0;
      }

      .card i {
        position: relative;
        z-index: 1;
      }

      @media (max-width: 767px) {
        .card-body {
          padding: 1.5rem !important;
        }

        .card i {
          font-size: 2.5rem !important;
        }

        .card h2 {
          font-size: 1.8rem;
        }
      }
    </style>

    <div class="card border-0 shadow-lg rounded-lg mb-5">
      <div class="card-body p-4">
        <h3 class="text-center mb-4">Login Access</h3>
        <div class="row">
          <div class="col-sm-6 mb-3">
            <a href="user/user_login.php"
              class="btn btn-success btn-lg btn-block d-flex align-items-center justify-content-center py-3 rounded-pill shadow-sm">
              <i class="fas fa-user-circle fa-lg mr-2"></i>
              <span>User Login</span>
            </a>
          </div>
          <div class="col-sm-6 mb-3">
            <a href="officer/officer_login.php"
              class="btn btn-primary btn-lg btn-block d-flex align-items-center justify-content-center py-3 rounded-pill shadow-sm">
              <i class="fas fa-briefcase fa-lg mr-2"></i>
              <span>Officer Login</span>
            </a>
          </div>
          <div class="col-sm-6 mb-3">
            <a href="depthead/dept_head_login.php"
              class="btn btn-warning btn-lg btn-block d-flex align-items-center justify-content-center py-3 rounded-pill shadow-sm">
              <i class="fas fa-building fa-lg mr-2"></i>
              <span>Department Head</span>
            </a>
          </div>
          <div class="col-sm-6 mb-3">
            <a href="admin/admin_login.php"
              class="btn btn-danger btn-lg btn-block d-flex align-items-center justify-content-center py-3 rounded-pill shadow-sm">
              <i class="fas fa-shield-alt fa-lg mr-2"></i>
              <span>Admin Login</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-5">
      <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-lg h-100 hover-card">
          <div class="card-body p-4">
            <h3 class="mb-4">
              <i class="fas fa-star text-warning mr-2"></i>Key Features
            </h3>
            <?php
            $features = [
              ['icon' => 'mobile-alt', 'color' => 'primary', 'title' => 'Easy Access', 'desc' => 'Submit and track complaints from any device, anytime'],
              ['icon' => 'bell', 'color' => 'success', 'title' => 'Instant Updates', 'desc' => 'Get real-time notifications on complaint status'],
              ['icon' => 'chart-line', 'color' => 'info', 'title' => 'Progress Tracking', 'desc' => 'Monitor complaint resolution progress step by step']
            ];

            foreach ($features as $feature): ?>
              <div class="d-flex align-items-start mb-3 feature-item">
                <div class="bg-<?= $feature['color'] ?> text-white rounded-circle p-3 mr-3 icon-box">
                  <i class="fas fa-<?= $feature['icon'] ?>"></i>
                </div>
                <div class="content-box">
                  <h5><?= $feature['title'] ?></h5>
                  <p class="text-muted mb-0"><?= $feature['desc'] ?></p>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-lg h-100 bg-primary text-white hover-card">
          <div class="card-body p-4">
            <h3 class="mb-4">
              <i class="fas fa-users mr-2"></i>User Benefits
            </h3>
            <?php
            $benefits = [
              ['icon' => 'check-circle', 'title' => 'Transparent Process', 'desc' => 'Clear visibility of complaint handling stages'],
              ['icon' => 'shield-alt', 'title' => 'Secure Platform', 'desc' => 'Your information is protected with advanced security'],
              ['icon' => 'clock', 'title' => 'Quick Resolution', 'desc' => 'Efficient handling of complaints with fast response']
            ];

            foreach ($benefits as $benefit): ?>
              <div class="benefit-item mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-<?= $benefit['icon'] ?> fa-2x mr-3 benefit-icon"></i>
                  <div class="benefit-content">
                    <h5 class="mb-1"><?= $benefit['title'] ?></h5>
                    <p class="mb-0 benefit-desc"><?= $benefit['desc'] ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <footer class="bg-dark text-white py-4">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h5>Complaint Tracking System</h5>
          <p class="small mb-0">© 2025 All rights reserved.</p>
        </div>
        <div class="col-md-6 text-md-right">
          <div class="mb-2">
            <a href="#" class="text-white mr-3"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white mr-3"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white"><i class="fab fa-linkedin"></i></a>
          </div>
          <p class="small mb-0">Feedback: feedback@example.com</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Profile Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="profileModalLabel">
            <i class="fas fa-user-circle mr-2"></i>My Profile
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body bg-light">
          <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle p-3 d-inline-block mb-3">
              <i class="fas fa-user-circle" style="font-size: 3rem;"></i>
            </div>
            <h5 class="font-weight-bold"><?= htmlspecialchars($user_data['name']) ?></h5>
            <p class="text-muted"><i class="fas fa-envelope mr-2"></i><?= htmlspecialchars($user_data['email']) ?></p>
          </div>

          <div class="row">
            <div class="col-sm-6 mb-3">
              <a href="user/profile.php" class="btn btn-primary btn-block rounded-pill">
                <i class="fas fa-user-edit mr-2"></i>Edit Profile
              </a>
            </div>
            <div class="col-sm-6 mb-3">
              <a href="user/change_password.php" class="btn btn-outline-primary btn-block rounded-pill">
                <i class="fas fa-key mr-2"></i>Change Password
              </a>
            </div>
          </div>

          <div class="mt-2">
            <a href="user/delete_account.php" class="btn btn-outline-danger btn-block rounded-pill">
              <i class="fas fa-user-times mr-2"></i>Delete Account
            </a>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">Close</button>
          <a href="logout.php" class="btn btn-danger rounded-pill">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Add before the closing body tag, but after jQuery and Bootstrap scripts -->
  <?php if ($show_delete_alert): ?>
    <div class="delete-alert alert alert-success alert-dismissible fade show shadow-lg" role="alert">
      <div class="d-flex align-items-center">
        <i class="fas fa-check-circle fa-2x mr-2"></i>
        <div>
          <h5 class="mb-0">Account Successfully Deleted</h5>
          <p class="mb-0 small">Your account and all associated data have been permanently removed.</p>
        </div>
      </div>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  <?php endif; ?>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="assets/js/menu.js"></script>
</body>