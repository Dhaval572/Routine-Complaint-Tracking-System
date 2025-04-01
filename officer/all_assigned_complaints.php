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
  <title>Assigned Complaints - Full Details</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .table td, .table th {
      font-size: 0.8rem;
      vertical-align: middle;
    }
    /* You may add custom styles for better readability */
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
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">Assigned Complaints - Full Details</span>
    <div class="ml-auto">
      <a href="officer_dashboard.php" class="btn btn-outline-light">Dashboard</a>
    </div>
  </nav>
  <div class="container mt-4">
    <h4>All Complaints Assigned to You</h4>
    <?php if ($result && $result->num_rows > 0) { ?>
      <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Department</th>
            <th>Assigned Officer</th>
            <th>Assigned By</th>
            <th>Referred At</th>
            <th>Remarks</th>
            <th>Response</th>
            <th>Created At</th>
            <th>Updated At</th>
            <th>Activity</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['description']); ?></td>
              <td><?php echo ucfirst($row['status']); ?></td>
              <td><?php echo ucfirst($row['priority']); ?></td>
              <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
              <td><?php echo ($row['officer_name']) ? htmlspecialchars($row['officer_name']) : 'Not Assigned'; ?></td>
              <td><?php echo htmlspecialchars($row['assigned_by']); ?></td>
              <td><?php echo ($row['referred_at']) ? $row['referred_at'] : 'N/A'; ?></td>
              <td><?php echo ($row['remarks']) ? htmlspecialchars($row['remarks']) : 'N/A'; ?></td>
              <td><?php echo ($row['response']) ? htmlspecialchars($row['response']) : 'N/A'; ?></td>
              <td><?php echo $row['created_at']; ?></td>
              <td><?php echo $row['updated_at']; ?></td>
              <td>
                <button class="btn btn-info btn-sm" onclick="viewActivity(<?php echo $row['id']; ?>)">View Activity</button>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      </div>
    <?php } else {
      echo "<div class='alert alert-info'>No complaints assigned to you.</div>";
    } ?>
  </div>
  
  <!-- Activity Modal -->
  <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Complaint Activity</h5>
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
