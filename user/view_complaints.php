<?php
include('../config.php');
include('../assets/alert_functions.php'); // Include alert functions

if (!isset($_SESSION['user_id'])) {
  header("Location: user_login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$records_per_page = 3;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total number of complaints for pagination
$total_query = "SELECT COUNT(*) as total FROM complaints WHERE citizen_id = '$user_id'";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get complaints with pagination
$sql = "SELECT c.*, d.name as dept_name 
        FROM complaints c 
        LEFT JOIN departments d ON c.department_id = d.id 
        WHERE c.citizen_id = '$user_id' 
        ORDER BY c.created_at DESC
        LIMIT $offset, $records_per_page";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/view_complaints.css">
  <!-- Toast Alert CSS -->
  <link rel="stylesheet" href="../assets/css/toast-alerts.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body class="min-vh-100 user-bg-gradient">
  <!-- Toast Container -->
  <div id="toastContainer" class="toast-container"></div>

  <!-- Animated floating blobs using Bootstrap utilities -->
  <div class="position-fixed w-100 h-100 blob-container">
    <div class="position-absolute rounded-circle blob-large"></div>
    <div class="position-absolute rounded-circle blob-medium"></div>
    <div class="position-absolute rounded-circle blob-small"></div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark shadow-lg mx-md-4 mx-2 navbar-custom">
    <div class="container">
      <span class="navbar-brand d-flex align-items-center">
        <div
          class="d-flex align-items-center justify-content-center rounded-circle bg-white text-primary mr-2 navbar-brand-icon">
          <i class="fas fa-clipboard-list fa-sm"></i>
        </div>
        <span class="font-weight-bold ml-1 navbar-brand-text">My Complaints</span>
      </span>
      <div class="ml-auto">
        <a href="user_dashboard.php" class="btn rounded-pill px-4 shadow-sm dashboard-btn">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden main-card">
      <div class="card-header border-0 py-4 card-header-custom">
        <div class="d-flex align-items-center">
          <div
            class="d-flex align-items-center justify-content-center rounded-lg text-white mr-3 header-icon-container">
            <i class="fas fa-list-alt fa-lg"></i>
          </div>
          <h4 class="mb-0 text-white font-weight-bold header-title">Your Registered Complaints</h4>
        </div>
      </div>

      <div class="card-body p-md-5 p-4 card-body-bg">
        <?php if ($result->num_rows > 0) { ?>
          <!-- Complaint Cards -->
          <?php while ($row = $result->fetch_assoc()) {
            $referred_to = ($row['target_id'] && $row['target_role'] != 'none') ? ucfirst($row['target_role']) : 'Dept Head';
            $status_class = $row['status'] == 'solved' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'info');
            $created_date = date('M d, Y', strtotime($row['created_at']));
            ?>
            <div class="card mb-4 border-0 shadow-sm complaint-card">
              <div class="card-body p-0">
                <div class="d-flex flex-column flex-md-row">
                  <!-- Left side with status indicator -->
                  <div
                    class="d-flex flex-column align-items-center justify-content-center p-4 text-center status-indicator status-<?php echo $status_class; ?>-bg status-<?php echo $status_class; ?>-border">
                    <div
                      class="rounded-circle mb-2 d-flex align-items-center justify-content-center status-circle status-<?php echo $status_class; ?>-circle">
                      <span class="text-<?php echo $status_class; ?> font-weight-bold">#<?php echo $row['id']; ?></span>
                    </div>
                    <span class="badge badge-pill badge-<?php echo $status_class; ?> px-3 py-2">
                      <?php echo ucfirst($row['status']); ?>
                    </span>
                  </div>

                  <!-- Right side with complaint details -->
                  <div class="p-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h5 class="complaint-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                      <span class="text-muted small">
                        <i class="far fa-calendar-alt mr-1"></i> <?php echo $created_date; ?>
                      </span>
                    </div>

                    <div class="row mb-3">
                      <div class="col-md-6 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                          <div class="info-icon-container">
                            <i class="fas fa-building fa-sm text-white"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Department</small>
                            <span class="font-weight-medium"><?php echo htmlspecialchars($row['dept_name']); ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="d-flex align-items-center">
                          <div
                            class="d-flex align-items-center justify-content-center rounded-circle bg-primary p-2 mr-2 info-icon-container">
                            <i class="fas fa-user-tie fa-sm text-white"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Referred To</small>
                            <span class="font-weight-medium"><?php echo $referred_to; ?></span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end mt-3">
                      <button class="btn btn-primary btn-sm rounded-pill mr-2 action-btn-primary"
                        onclick="loadActivity(<?php echo $row['id']; ?>)">
                        <i class="fas fa-history mr-1"></i> Activity Timeline
                      </button>
                      <?php if ($row['status'] == 'solved') { ?>
                        <a href="feedback.php?complaint_id=<?php echo $row['id']; ?>"
                          class="btn btn-success btn-sm rounded-pill action-btn-success">
                          <i class="fas fa-star mr-1"></i> Give Feedback
                        </a>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>

          <!-- Pagination Controls -->
          <?php if ($total_pages > 1): ?>
            <div
              class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-3 bg-white rounded-lg shadow-sm pagination-container">
              <span class="text-muted mb-3 mb-md-0">
                Showing <?= min($offset + 1, $total_records) ?> to <?= min($offset + $records_per_page, $total_records) ?>
                of <?= $total_records ?> complaints
              </span>
              <nav aria-label="Complaints pagination">
                <ul class="pagination pagination-sm mb-0">
                  <?php if ($page > 1): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill page-link-border" href="?page=<?= $page - 1 ?>">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    </li>
                  <?php endif; ?>

                  <?php
                  $start_page = max(1, $page - 1);
                  $end_page = min($total_pages, $page + 1);

                  if ($start_page > 1): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill" href="?page=1">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                      <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                    <?php endif; ?>
                  <?php endif; ?>

                  <?php for ($i = $start_page; $i <= $end_page; $i++):
                    if ($i == 1 || $i == $total_pages)
                      continue; ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                      <a class="page-link rounded-pill" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($end_page < $total_pages):
                    if ($end_page < $total_pages - 1): ?>
                      <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill" href="?page=<?= $total_pages ?>"><?= $total_pages ?></a>
                    </li>
                  <?php endif; ?>

                  <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill page-link-border" href="?page=<?= $page + 1 ?>">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          <?php endif; ?>

          <div class="text-center mt-4">
            <a href="register_complaint.php" class="btn btn-primary rounded-pill px-4 mr-2 register-btn">
              <i class="fas fa-plus-circle mr-2"></i>Register New Complaint
            </a>
            <a href="view_all_complaints.php" class="btn rounded-pill px-4 view-all-btn">
              <i class="fas fa-list-alt mr-2"></i>View All Complaints
            </a>
          </div>
        <?php } else { ?>
          <!-- Empty state with improved styling -->
          <div class="text-center py-5 my-3 rounded-lg empty-state">
            <div class="mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center empty-state-icon">
              <i class="fas fa-clipboard-check fa-3x"></i>
            </div>
            <h3 class="text-primary mb-3 font-weight-bold">Your Complaint Box is Empty</h3>
            <p class="text-muted mb-4 px-4 mx-auto empty-state-text">
              You don't have any active complaints at the moment. Need to report an issue?
            </p>

            <div class="alert alert-info mx-auto mb-4 info-alert">
              <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-primary mr-3 info-icon"></i>
                <p class="mb-0">When you submit a complaint, it will appear here for easy tracking and management.</p>
              </div>
            </div>

            <a href="register_complaint.php" class="btn btn-lg rounded-pill px-5 register-btn">
              <i class="fas fa-plus-circle mr-2"></i>Register New Complaint
            </a>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- Activity Modal with improved styling -->
  <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content border-0 shadow modal-content-custom">
        <div class="modal-header border-0 modal-header-custom">
          <h5 class="modal-title text-white font-weight-bold modal-title-text">
            <i class="fas fa-history mr-2"></i>Complaint Activity Timeline
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4 card-body-bg" id="activityContent">
          <!-- AJAX content loaded here -->
        </div>
      </div>
    </div>
  </div>

  <!-- Custom JS -->
  <script src="../assets/js/view_complaints.js"></script>
  <!-- Toast Alert JS -->
  <script src="../assets/js/toast-alerts.js"></script>

  <!-- Initialize Toasts -->
  <script>
    // Create a global object to store session messages
    var sessionMessages = {};

    <?php if (isset($_SESSION['success'])): ?>
      sessionMessages.success = '<?php echo $_SESSION['success']; ?>';
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      sessionMessages.error = '<?php echo $_SESSION['error']; ?>';
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
      sessionMessages.warning = '<?php echo $_SESSION['warning']; ?>';
      <?php unset($_SESSION['warning']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['info'])): ?>
      sessionMessages.info = '<?php echo $_SESSION['info']; ?>';
      <?php unset($_SESSION['info']); ?>
    <?php endif; ?>
  </script>
</body>

</html>