<?php
include('../config.php');
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
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    function loadActivity(complaint_id) {
      $.ajax({
        url: 'fetch_activity.php',
        type: 'GET',
        data: { complaint_id: complaint_id },
        success: function (data) {
          $("#activityContent").html(data);
          $("#activityModal").modal('show');
        }
      });
    }
  </script>
</head>

<body class="min-vh-100" style="background: linear-gradient(135deg, #1a3a8f 0%, #0d2b6b 100%);">
  <!-- Animated floating blobs using Bootstrap utilities -->
  <div class="position-fixed w-100 h-100" style="z-index: -1;">
    <div class="position-absolute rounded-circle"
      style="width: 400px; height: 400px; background: rgba(100, 181, 246, 0.15); filter: blur(60px); top: 20%; left: 10%; animation: float 12s infinite;">
    </div>
    <div class="position-absolute rounded-circle"
      style="width: 300px; height: 300px; background: rgba(66, 165, 245, 0.12); filter: blur(60px); top: 50%; right: 15%; animation: float 12s infinite 4s;">
    </div>
    <div class="position-absolute rounded-circle"
      style="width: 250px; height: 250px; background: rgba(144, 202, 249, 0.15); filter: blur(60px); bottom: 10%; left: 30%; animation: float 12s infinite 8s;">
    </div>
  </div>

  <nav class="navbar navbar-expand-lg navbar-dark shadow-lg mx-md-4 mx-2" style="
    background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
    border-radius: 20px;
    margin-top: 15px;
    padding: 12px 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 5px 10px rgba(0, 0, 0, 0.05);
  ">
    <div class="container">
      <span class="navbar-brand d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-primary mr-2"
          style="width: 38px; height: 38px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
          <i class="fas fa-clipboard-list fa-sm"></i>
        </div>
        <span class="font-weight-bold ml-1" style="letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">My Complaints</span>
      </span>
      <div class="ml-auto">
        <a href="user_dashboard.php" class="btn rounded-pill px-4 shadow-sm" style="
          background: rgba(255, 255, 255, 0.15); 
          color: white; 
          border: 1px solid rgba(255, 255, 255, 0.3);
          transition: all 0.3s ease;
          backdrop-filter: blur(5px);
        " onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'"
          onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="card border-0 shadow-lg rounded-lg overflow-hidden" style="
      border-radius: 30px !important; 
      backdrop-filter: blur(20px); 
      background: rgba(255, 255, 255, 0.9);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 8px 24px -4px rgba(41, 98, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.5);
    ">
      <div class="card-header border-0 py-4" style="
        background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      ">
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center justify-content-center rounded-lg text-white mr-3" style="
            width: 50px; 
            height: 50px; 
            background: rgba(255, 255, 255, 0.2); 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.2);
            border-radius: 15px;
          ">
            <i class="fas fa-list-alt fa-lg"></i>
          </div>
          <h4 class="mb-0 text-white font-weight-bold" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">Your Registered Complaints</h4>
        </div>
      </div>

      <div class="card-body p-md-5 p-4" style="background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%);">
        <?php if ($result->num_rows > 0) { ?>
          <!-- Complaint Cards -->
          <?php while ($row = $result->fetch_assoc()) {
            $referred_to = ($row['target_id'] && $row['target_role'] != 'none') ? ucfirst($row['target_role']) : 'Dept Head';
            $status_class = $row['status'] == 'solved' ? 'success' : ($row['status'] == 'pending' ? 'warning' : 'info');
            $status_bg = $row['status'] == 'solved' ? 'rgba(40, 167, 69, 0.1)' : ($row['status'] == 'pending' ? 'rgba(255, 193, 7, 0.1)' : 'rgba(23, 162, 184, 0.1)');
            $status_border = $row['status'] == 'solved' ? 'rgba(40, 167, 69, 0.2)' : ($row['status'] == 'pending' ? 'rgba(255, 193, 7, 0.2)' : 'rgba(23, 162, 184, 0.2)');
            $created_date = date('M d, Y', strtotime($row['created_at']));
          ?>
            <div class="card mb-4 border-0 shadow-sm" style="
              border-radius: 15px; 
              overflow: hidden; 
              transition: all 0.3s ease;
              background: rgba(255, 255, 255, 0.8);
              border: 1px solid rgba(41, 98, 255, 0.1);
            " onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 15px 30px rgba(41, 98, 255, 0.15)'"
              onmouseout="this.style.transform=''; this.style.boxShadow=''">
              <div class="card-body p-0">
                <div class="d-flex flex-column flex-md-row">
                  <!-- Left side with status indicator -->
                  <div class="d-flex flex-column align-items-center justify-content-center p-4 text-center" 
                       style="background: <?php echo $status_bg; ?>; min-width: 120px; border-right: 1px solid <?php echo $status_border; ?>">
                    <div class="rounded-circle mb-2 d-flex align-items-center justify-content-center" 
                         style="width: 60px; height: 60px; background-color: white; border: 2px solid <?php echo $status_class == 'success' ? '#28a745' : ($status_class == 'warning' ? '#ffc107' : '#17a2b8'); ?>;">
                      <span class="text-<?php echo $status_class; ?> font-weight-bold">#<?php echo $row['id']; ?></span>
                    </div>
                    <span class="badge badge-pill badge-<?php echo $status_class; ?> px-3 py-2">
                      <?php echo ucfirst($row['status']); ?>
                    </span>
                  </div>
                  
                  <!-- Right side with complaint details -->
                  <div class="p-4 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <h5 class="font-weight-bold text-primary mb-0"><?php echo htmlspecialchars($row['title']); ?></h5>
                      <span class="text-muted small">
                        <i class="far fa-calendar-alt mr-1"></i> <?php echo $created_date; ?>
                      </span>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-6 mb-2 mb-md-0">
                        <div class="d-flex align-items-center">
                          <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary p-2 mr-2" style="width: 32px; height: 32px;">
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
                          <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary p-2 mr-2" style="width: 32px; height: 32px;">
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
                      <button class="btn btn-primary btn-sm rounded-pill mr-2" onclick="loadActivity(<?php echo $row['id']; ?>)" style="
                        background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
                        border: none;
                        box-shadow: 0 4px 10px rgba(41, 98, 255, 0.2);
                      ">
                        <i class="fas fa-history mr-1"></i> Activity Timeline
                      </button>
                      <?php if ($row['status'] == 'solved') { ?>
                        <a href="feedback.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm rounded-pill" style="
                          background: linear-gradient(135deg, #28a745 0%, #218838 100%);
                          border: none;
                          box-shadow: 0 4px 10px rgba(40, 167, 69, 0.2);
                        ">
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-3 bg-white rounded-lg shadow-sm" style="
              border-radius: 15px;
              background: rgba(255, 255, 255, 0.8);
              border: 1px solid rgba(41, 98, 255, 0.1);
            ">
              <span class="text-muted mb-3 mb-md-0">
                Showing <?= min($offset + 1, $total_records) ?> to <?= min($offset + $records_per_page, $total_records) ?>
                of <?= $total_records ?> complaints
              </span>
              <nav aria-label="Complaints pagination">
                <ul class="pagination pagination-sm mb-0">
                  <?php if ($page > 1): ?>
                    <li class="page-item">
                      <a class="page-link rounded-pill" href="?page=<?= $page - 1 ?>" style="border-color: rgba(41, 98, 255, 0.2);">
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
                    if ($i == 1 || $i == $total_pages) continue; ?>
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
                      <a class="page-link rounded-pill" href="?page=<?= $page + 1 ?>" style="border-color: rgba(41, 98, 255, 0.2);">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          <?php endif; ?>
          
          <div class="text-center mt-4">
            <a href="register_complaint.php" class="btn btn-primary rounded-pill px-4 mr-2" style="
              background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
              border: none;
              box-shadow: 0 4px 15px rgba(41, 98, 255, 0.3);
              transition: all 0.3s ease;
            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(41, 98, 255, 0.4)'"
              onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 15px rgba(41, 98, 255, 0.3)'">
              <i class="fas fa-plus-circle mr-2"></i>Register New Complaint
            </a>
            <a href="view_all_complaints.php" class="btn rounded-pill px-4" style="
              background: rgba(41, 98, 255, 0.1);
              color: #1565c0;
              border: 1px solid rgba(41, 98, 255, 0.3);
              box-shadow: 0 4px 15px rgba(41, 98, 255, 0.1);
              transition: all 0.3s ease;
            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(41, 98, 255, 0.2)'; this.style.background='rgba(41, 98, 255, 0.15)'"
              onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 15px rgba(41, 98, 255, 0.1)'; this.style.background='rgba(41, 98, 255, 0.1)'">
              <i class="fas fa-list-alt mr-2"></i>View All Complaints
            </a>
          </div>
        <?php } else { ?>
          <!-- Empty state with improved styling -->
          <div class="text-center py-5 my-3 rounded-lg" style="
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(240, 247, 255, 0.9) 100%);
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(41, 98, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
          ">
            <div class="mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center" style="
              width: 120px; 
              height: 120px; 
              background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
              color: white;
              box-shadow: 0 8px 20px rgba(41, 98, 255, 0.3);
            ">
              <i class="fas fa-clipboard-check fa-3x"></i>
            </div>
            <h3 class="text-primary mb-3 font-weight-bold">Your Complaint Box is Empty</h3>
            <p class="text-muted mb-4 px-4 mx-auto" style="max-width: 600px;">
              You don't have any active complaints at the moment. Need to report an issue?
            </p>

            <div class="alert alert-info mx-auto mb-4" style="
              max-width: 500px; 
              background: rgba(41, 98, 255, 0.05); 
              border: 1px solid rgba(41, 98, 255, 0.1);
              border-radius: 15px;
            ">
              <div class="d-flex align-items-center">
                <i class="fas fa-info-circle text-primary mr-3" style="font-size: 1.5rem;"></i>
                <p class="mb-0">When you submit a complaint, it will appear here for easy tracking and management.</p>
              </div>
            </div>

            <a href="register_complaint.php" class="btn btn-lg rounded-pill px-5" style="
              background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
              color: white;
              border: none;
              box-shadow: 0 8px 20px rgba(41, 98, 255, 0.3);
              transition: all 0.3s ease;
            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 25px rgba(41, 98, 255, 0.4)'"
              onmouseout="this.style.transform=''; this.style.boxShadow='0 8px 20px rgba(41, 98, 255, 0.3)'">
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
      <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow: hidden;">
        <div class="modal-header border-0" style="background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);">
          <h5 class="modal-title text-white font-weight-bold" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
            <i class="fas fa-history mr-2"></i>Complaint Activity Timeline
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-4" style="background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%);" id="activityContent">
          <!-- AJAX content loaded here -->
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Animation for floating blobs */
    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-20px); }
    }
    
    /* Status colors */
    :root {
      --success: #28a745;
      --warning: #ffc107;
      --info: #17a2b8;
    }
    
    /* Font weight helper */
    .font-weight-medium {
      font-weight: 500;
    }
    
    /* Active page item styling */
    .page-item.active .page-link {
      background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
      border-color: #1565c0;
    }
    
    /* Page link hover effect */
    .page-link:hover {
      background-color: rgba(41, 98, 255, 0.1);
    }
  </style>
