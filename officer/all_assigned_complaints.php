<?php
include('../config.php');
if (!isset($_SESSION['officer_id'])) {
  header("Location: officer_login.php");
  exit;
}

$officer_id = $_SESSION['officer_id'];
// For complaints assigned directly to you we also show who assigned it:
// If not referred, then assigned_by is the dept head (from dept_head_id).
// If referred to you, then assigned_by is the referrer.
$sql = "SELECT c.*, d.name as dept_name,
         CASE 
           WHEN c.referred_by IS NULL THEN (SELECT name FROM users u WHERE u.id = c.dept_head_id)
           ELSE (SELECT name FROM users u WHERE u.id = c.referred_by)
         END as assigned_by,
         (SELECT name FROM users u WHERE u.id = c.officer_id) as officer_name
        FROM complaints c 
        JOIN departments d ON c.department_id = d.id
        WHERE c.officer_id = '$officer_id'
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assigned Complaints - Full Details</title>
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
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 1rem 2rem;
    }
    
    .navbar-brand {
      font-weight: 600;
      font-size: 1.4rem;
      letter-spacing: 0.5px;
    }
    
    .content-wrapper {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      margin: 2rem 0;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      animation: fadeIn 0.8s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .page-title {
      color: #4e73df;
      font-weight: 600;
      margin-bottom: 1.5rem;
      border-left: 4px solid #4e73df;
      padding-left: 15px;
    }
    
    .table {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }
    
    .table thead th {
      background-color: #4e73df;
      color: white;
      font-weight: 500;
      border: none;
      padding: 12px 15px;
      font-size: 0.85rem;
    }
    
    .table td {
      font-size: 0.85rem;
      vertical-align: middle;
      padding: 12px 15px;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(78, 115, 223, 0.05);
    }
    
    .btn-dashboard {
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
    
    .btn-dashboard:hover {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      transform: translateY(-1px);
    }
    
    .btn-dashboard i {
      margin-right: 5px;
    }
    
    .btn-view-activity {
      background-color: #4e73df;
      color: white;
      border-radius: 20px;
      padding: 0.3rem 0.8rem;
      transition: all 0.3s;
      border: none;
    }
    
    .btn-view-activity:hover {
      background-color: #3a5fc8;
      transform: translateY(-2px);
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }
    
    .modal-content {
      border-radius: 15px;
      border: none;
    }
    
    .modal-header {
      background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
      color: white;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      border-bottom: none;
    }
    
    .modal-title {
      font-weight: 600;
    }
    
    .modal-header .close {
      color: white;
      opacity: 0.8;
    }
    
    .modal-header .close:hover {
      opacity: 1;
    }
    
    .alert-info {
      background-color: #e3f2fd;
      color: #0c5460;
      border-color: #bee5eb;
      border-radius: 10px;
      padding: 1.2rem;
    }
    
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
    }
    
    .status-pending {
      background-color: #fff9c4;
      color: #856404;
    }
    
    .status-in-progress {
      background-color: #e3f2fd;
      color: #004085;
    }
    
    .status-solved {
      background-color: #e8f5e9;
      color: #155724;
    }
    
    .priority-high {
      color: #dc3545;
      font-weight: 600;
    }
    
    .priority-medium {
      color: #fd7e14;
      font-weight: 600;
    }
    
    .priority-low {
      color: #28a745;
      font-weight: 600;
    }
  </style>
  <!-- jQuery and Bootstrap JS for modal functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
    <span class="navbar-brand"><i class="fas fa-clipboard-list mr-2"></i>Assigned Complaints</span>
    <div class="ml-auto">
      <a href="officer_dashboard.php" class="btn btn-dashboard" title="Back to Dashboard">
        <i class="fas fa-home"></i> Dashboard
      </a>
    </div>
  </nav>
  
  <div class="container">
    <div class="content-wrapper">
      <h4 class="page-title">All Complaints Assigned to You</h4>
      
      <?php if ($result && $result->num_rows > 0) { ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Department</th>
                <th>Assigned By</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()) { 
                // Determine status class
                $statusClass = '';
                switch($row['status']) {
                  case 'pending':
                    $statusClass = 'status-pending';
                    break;
                  case 'in-progress':
                    $statusClass = 'status-in-progress';
                    break;
                  case 'solved':
                    $statusClass = 'status-solved';
                    break;
                }
                
                // Determine priority class
                $priorityClass = '';
                switch($row['priority']) {
                  case 'high':
                    $priorityClass = 'priority-high';
                    break;
                  case 'medium':
                    $priorityClass = 'priority-medium';
                    break;
                  case 'low':
                    $priorityClass = 'priority-low';
                    break;
                }
              ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo htmlspecialchars($row['description']); ?>">
                      <?php echo htmlspecialchars($row['title']); ?>
                    </a>
                  </td>
                  <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                  <td><span class="<?php echo $priorityClass; ?>"><?php echo ucfirst($row['priority']); ?></span></td>
                  <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
                  <td><?php echo htmlspecialchars($row['assigned_by']); ?></td>
                  <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                  <td>
                    <button class="btn btn-view-activity" onclick="viewActivity(<?php echo $row['id']; ?>)">
                      <i class="fas fa-history"></i> Activity
                    </button>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } else { ?>
        <div class="alert alert-info">
          <i class="fas fa-info-circle mr-2"></i> No complaints have been assigned to you yet.
        </div>
      <?php } ?>
    </div>
  </div>
  
  <!-- Activity Modal -->
  <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-history mr-2"></i>Complaint Activity History</h5>
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

  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>
</body>
</html>
