<?php
include('../config.php');
if (!isset($_SESSION['dept_head_id'])) {
  header("Location: dept_head_login.php");
  exit;
}

$dept_head_id = $_SESSION['dept_head_id'];
$sql = "SELECT c.*, d.name as dept_name, u.name as officer_name 
        FROM complaints c 
        LEFT JOIN departments d ON c.department_id = d.id 
        LEFT JOIN users u ON c.officer_id = u.id 
        WHERE c.dept_head_id = '$dept_head_id'
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);

// Get department name
$dept_name = "Department";
if (isset($_SESSION['dept_head_dept_id'])) {
  $dept_id = $_SESSION['dept_head_dept_id'];
  $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
  $stmt->bind_param("i", $dept_id);
  $stmt->execute();
  $dept_result = $stmt->get_result();
  if ($row = $dept_result->fetch_assoc()) {
    $dept_name = $row['name'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Assigned Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    :root {
      --primary: #4e73df;
      --secondary: #6c757d;
      --success: #1cc88a;
      --info: #36b9cc;
      --warning: #f6c23e;
      --danger: #e74a3b;
      --light: #f8f9fc;
      --dark: #5a5c69;
    }
    
    body {
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
      font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      min-height: 100vh;
    }
    
    .navbar {
      background: linear-gradient(to right, #4e73df, #224abe);
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.2rem;
    }
    
    .btn-dashboard {
      border-radius: 50px;
      padding: 0.5rem 1.5rem;
      font-weight: 600;
      transition: all 0.2s ease;
      background-color: transparent;
      border: 1px solid rgba(255,255,255,0.5);
      color: white;
    }
    
    .btn-dashboard:hover {
      border-color: rgba(255,255,255,0.9);
      box-shadow: 0 0 10px rgba(255,255,255,0.2);
      color: white;
    }
    
    .btn-dashboard:active {
      transform: translateY(1px);
    }
    
    .page-header {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    
    .page-header h3 {
      margin: 0;
      color: var(--dark);
      font-weight: 700;
      display: flex;
      align-items: center;
    }
    
    .page-header h3 i {
      color: var(--primary);
      margin-right: 0.75rem;
      font-size: 1.75rem;
    }
    
    .table-container {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      overflow: hidden;
    }
    
    .table {
      margin-bottom: 0;
    }
    
    .table thead th {
      background-color: #f8f9fc;
      border-top: none;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      color: #4e73df;
      padding: 1rem;
    }
    
    .table tbody td {
      vertical-align: middle;
      padding: 1rem;
      border-color: #f0f0f0;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: #fcfcfc;
    }
    
    .table-hover tbody tr {
      transition: all 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(78, 115, 223, 0.05);
      transform: translateY(-2px);
      box-shadow: 0 0.15rem 0.5rem rgba(58, 59, 69, 0.1);
    }
    
    .badge {
      font-weight: 600;
      padding: 0.35em 0.65em;
      border-radius: 10rem;
    }
    
    .badge-success {
      background-color: #1cc88a;
    }
    
    .badge-warning {
      background-color: #f6c23e;
      color: #fff;
    }
    
    .badge-info {
      background-color: #36b9cc;
    }
    
    .btn-view-activity {
      border-radius: 50px;
      padding: 0.375rem 0.75rem;
      font-size: 0.85rem;
      font-weight: 600;
      transition: all 0.2s;
      background: linear-gradient(to right, #36b9cc, #1a8eaf);
      border: none;
      color: white;
    }
    
    .btn-view-activity:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.15rem 0.5rem rgba(58, 59, 69, 0.2);
    }
    
    .btn-view-activity i {
      margin-right: 0.5rem;
    }
    
    .complaint-id {
      font-weight: 700;
      background-color: #4e73df;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      display: inline-block;
    }
    
    .complaint-title {
      font-weight: 600;
      color: #4e73df;
    }
    
    .complaint-date {
      color: #6e707e;
      font-size: 0.85rem;
    }
    
    .complaint-dept {
      font-weight: 600;
      color: #5a5c69;
    }
    
    .officer-name {
      font-weight: 600;
    }
    
    .not-assigned {
      color: #e74a3b;
      font-style: italic;
    }
    
    .modal-content {
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
      background: linear-gradient(to right, #4e73df, #224abe);
      color: white;
      border-bottom: none;
      border-radius: 0.5rem 0.5rem 0 0;
    }
    
    .modal-title {
      font-weight: 700;
    }
    
    .modal-body {
      padding: 1.5rem;
    }
    
    .close {
      color: white;
      text-shadow: none;
      opacity: 0.8;
    }
    
    .close:hover {
      color: white;
      opacity: 1;
    }
    
    .alert {
      border-radius: 0.5rem;
      border: none;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }
    
    .alert-info {
      background-color: #e3f3f6;
      color: #2596a8;
      border-left: 4px solid #36b9cc;
    }
    
    @media (max-width: 768px) {
      .table-responsive {
        border-radius: 0.5rem;
      }
      
      .complaint-id, .badge {
        display: inline-block;
        margin-bottom: 0.25rem;
      }
      
      .container {
        padding: 1rem;
      }
      
      .page-header {
        padding: 1rem;
      }
      
      .table thead th, .table tbody td {
        padding: 0.75rem;
      }
    }
  </style>
  <!-- jQuery and Bootstrap JS for modal functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    function viewActivity(complaint_id) {
      $.ajax({
        url: "../user/fetch_activity.php",
        type: "GET",
        data: { complaint_id: complaint_id },
        success: function(data) {
          $("#activityContent").html(data);
          $("#activityModal").modal("show");
        }
      });
    }
  </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <span class="navbar-brand">
        <i class="fas fa-chart-line mr-2"></i>Assigned Complaints
      </span>
      <div class="ml-auto">
        <a href="dept_head_dashboard.php" class="btn btn-dashboard">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
      </div>
    </div>
  </nav>
  
  <div class="container py-4">
    <div class="page-header">
      <h3><i class="fas fa-clipboard-list"></i><?php echo htmlspecialchars($dept_name); ?> Department Complaints</h3>
    </div>
    
    <?php if ($result && $result->num_rows > 0) { ?>
      <div class="table-container">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th><i class="fas fa-hashtag mr-1"></i>ID</th>
                <th><i class="fas fa-heading mr-1"></i>Title</th>
                <th><i class="fas fa-building mr-1"></i>Department</th>
                <th><i class="fas fa-flag mr-1"></i>Status</th>
                <th><i class="fas fa-user-shield mr-1"></i>Assigned Officer</th>
                <th><i class="fas fa-calendar-alt mr-1"></i>Registered</th>
                <th><i class="fas fa-cogs mr-1"></i>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()) {
                $status = strtolower(trim($row['status']));
                $target = isset($row['target_role']) ? strtolower(trim($row['target_role'])) : '';

                // Row background: Only if complaint is against an officer.
                $rowClass = ($target == 'officer') ? "table-danger" : "";

                // Status badge: solved green, referred blue, pending yellow.
                if ($status == 'solved') {
                  $badge = "badge-success";
                  $icon = "fa-check-circle";
                } elseif ($status == 'referred') {
                  $badge = "badge-info";
                  $icon = "fa-exchange-alt";
                } elseif ($status == 'in_progress') {
                  $badge = "badge-primary";
                  $icon = "fa-spinner";
                } else {
                  $badge = "badge-warning";
                  $icon = "fa-clock";
                }
              ?>
                <tr class="<?php echo $rowClass; ?>">
                  <td><span class="complaint-id"><?php echo $row['id']; ?></span></td>
                  <td class="complaint-title"><?php echo htmlspecialchars($row['title']); ?></td>
                  <td class="complaint-dept"><?php echo htmlspecialchars($row['dept_name']); ?></td>
                  <td>
                    <span class="badge <?php echo $badge; ?>">
                      <i class="fas <?php echo $icon; ?> mr-1"></i>
                      <?php echo ucfirst($status); ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($row['officer_name']) { ?>
                      <span class="officer-name">
                        <i class="fas fa-user mr-1"></i>
                        <?php echo htmlspecialchars($row['officer_name']); ?>
                      </span>
                    <?php } else { ?>
                      <span class="not-assigned">
                        <i class="fas fa-user-slash mr-1"></i>
                        Not Assigned
                      </span>
                    <?php } ?>
                  </td>
                  <td class="complaint-date">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('M d, Y g:i A', strtotime($row['created_at'])); ?>
                  </td>
                  <td>
                    <button class="btn btn-view-activity" onclick="viewActivity(<?php echo $row['id']; ?>)">
                      <i class="fas fa-history"></i>View Activity
                    </button>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } else { ?>
      <div class="alert alert-info d-flex align-items-center">
        <i class="fas fa-info-circle mr-3" style="font-size: 1.5rem;"></i>
        <div>No complaints found in your department.</div>
      </div>
    <?php } ?>
  </div>

  <!-- Activity Modal -->
  <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-history mr-2"></i>
            Complaint Activity Timeline
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="activityContent">
          <!-- Activity details loaded via AJAX -->
        </div>
      </div>
    </div>
  </div>
</body>
</html>
