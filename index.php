<?php
include 'config.php';

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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <i class="fas fa-comments mr-2"></i>Complaint Tracking System
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active mx-1">
            <a class="nav-link btn btn-primary btn-lg rounded-pill px-4" href="#">
              <i class="fas fa-home mr-2"></i>
              <span class="font-weight-bold">Home</span>
            </a>
          </li>
          <li class="nav-item mx-1">
            <a class="nav-link btn btn-primary btn-lg rounded-pill px-4" href="about.php">
              <i class="fas fa-info-circle mr-2"></i>
              <span class="font-weight-bold">About</span>
            </a>
          </li>
          <li class="nav-item mx-1">
            <a class="nav-link btn btn-primary btn-lg rounded-pill px-4" href="feedback.php">
              <i class="fas fa-star mr-2"></i>
              <span class="font-weight-bold">Feedback</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

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
      <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
            <h5 class="card-title">Total Complaints</h5>
            <h2 class="mb-0">1,234</h2>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5 class="card-title">Resolved</h5>
            <h2 class="mb-0">789</h2>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
            <h5 class="card-title">Pending</h5>
            <h2 class="mb-0">445</h2>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body text-center">
            <i class="fas fa-users fa-3x text-info mb-3"></i>
            <h5 class="card-title">Users</h5>
            <h2 class="mb-0">5,678</h2>
          </div>
        </div>
      </div>
    </div>

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
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <h3 class="mb-4">
              <i class="fas fa-star text-warning mr-2"></i>Key Features
            </h3>
            <div class="d-flex align-items-start mb-3">
              <div class="bg-primary text-white rounded-circle p-3 mr-3">
                <i class="fas fa-mobile-alt"></i>
              </div>
              <div>
                <h5>Easy Access</h5>
                <p class="text-muted mb-0">Submit and track complaints from any device, anytime</p>
              </div>
            </div>
            <div class="d-flex align-items-start mb-3">
              <div class="bg-success text-white rounded-circle p-3 mr-3">
                <i class="fas fa-bell"></i>
              </div>
              <div>
                <h5>Instant Updates</h5>
                <p class="text-muted mb-0">Get real-time notifications on complaint status</p>
              </div>
            </div>
            <div class="d-flex align-items-start">
              <div class="bg-info text-white rounded-circle p-3 mr-3">
                <i class="fas fa-chart-line"></i>
              </div>
              <div>
                <h5>Progress Tracking</h5>
                <p class="text-muted mb-0">Monitor complaint resolution progress step by step</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100 bg-primary text-white">
          <div class="card-body p-4">
            <h3 class="mb-4">
              <i class="fas fa-users mr-2"></i>User Benefits
            </h3>
            <ul class="list-unstyled">
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-check-circle fa-2x mr-3"></i>
                  <div>
                    <h5 class="mb-1">Transparent Process</h5>
                    <p class="mb-0">Clear visibility of complaint handling stages</p>
                  </div>
                </div>
              </li>
              <li class="mb-3">
                <div class="d-flex align-items-center">
                  <i class="fas fa-shield-alt fa-2x mr-3"></i>
                  <div>
                    <h5 class="mb-1">Secure Platform</h5>
                    <p class="mb-0">Your information is protected with advanced security</p>
                  </div>
                </div>
              </li>
              <li>
                <div class="d-flex align-items-center">
                  <i class="fas fa-clock fa-2x mr-3"></i>
                  <div>
                    <h5 class="mb-1">Quick Resolution</h5>
                    <p class="mb-0">Efficient handling of complaints with fast response</p>
                  </div>
                </div>
              </li>
            </ul>
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

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Account Deleted Modal -->
  <div class="modal fade" id="accountDeletedModal" tabindex="-1" role="dialog"
    aria-labelledby="accountDeletedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="accountDeletedModalLabel">
            <i class="fas fa-user-times mr-2"></i>Account Deleted
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4">
          <div class="text-center mb-4">
            <div class="bg-danger text-white rounded-circle p-3 d-inline-block mb-3">
              <i class="fas fa-user-times" style="font-size: 3rem;"></i>
            </div>
            <h5 class="font-weight-bold">Your account has been successfully deleted</h5>
            <p class="text-muted">All your data has been permanently removed from our system.</p>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-danger rounded-pill" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Show account deleted modal if needed
    <?php if (isset($_GET['show_delete_popup'])): ?>
      $(document).ready(function () {
        $('#accountDeletedModal').modal('show');
        // Remove the query parameter from URL without refreshing
        history.replaceState({}, document.title, window.location.pathname);
      });
    <?php endif; ?>
  </script>
</body>

</html>