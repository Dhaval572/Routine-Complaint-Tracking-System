<?php
include('config.php');
if (!isset($_SESSION['user_id'])) {
  header("Location: user_login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
// Fetch complaints of the logged in citizen
$sql = "SELECT c.*, d.name as dept_name 
        FROM complaints c 
        LEFT JOIN departments d ON c.department_id = d.id 
        WHERE c.citizen_id = '$user_id' 
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>My Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Include jQuery and Bootstrap JS for modal functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    // Load complaint activity via AJAX into modal
    function loadActivity(complaint_id) {
      $.ajax({
        url: 'fetch_activity.php',
        type: 'GET',
        data: {
          complaint_id: complaint_id
        },
        success: function(data) {
          $("#activityContent").html(data);
          $("#activityModal").modal('show');
        }
      });
    }
  </script>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">My Complaints</span>
    <div class="ml-auto">
      <a href="user_dashboard.php" class="btn btn-outline-light">Dashboard</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h3>Your Registered Complaints</h3>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Department</th>
          <th>Status</th>
          <th>AI Summary (Complaint)</th>
          <th>AI Summary (Response)</th>
          <th>Referred To</th>
          <th>Activity</th>
          <th>Feedback</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) {
          // Determine referred to (display target if exists, else dept head)
          $referred_to = ($row['target_id'] && $row['target_role'] != 'none') ? $row['target_role'] : 'Dept Head';
        ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
              <?php
              if ($row['ai_summary_complaint'] == null) {
                echo "<span class='text-danger'>Not available</span>";
              } else {
                echo htmlspecialchars($row['ai_summary_complaint']);
              }
              ?>
            </td>
            <td>
              <?php
              if ($row['ai_summary_response'] == null) {
                echo "<span class='text-danger'>Not available</span>";
              } else {
                echo htmlspecialchars($row['ai_summary_response']);
              }
              ?>
            </td>
            <td><?php echo $referred_to; ?></td>
            <td><button class="btn btn-info btn-sm" onclick="loadActivity(<?php echo $row['id']; ?>)">View</button></td>
            <td>
              <?php if ($row['status'] == 'solved') { ?>
                <a href="feedback.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">Feedback</a>
              <?php } else { ?>
                <button class="btn btn-secondary btn-sm" disabled>Feedback</button>
              <?php } ?>
            </td>
            <td><?php echo $row['created_at']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- Activity Modal -->
  <div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><strong>Complaint Activity</strong></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="activityContent">
          <!-- AJAX content loaded here -->
        </div>
      </div>
    </div>
  </div>
</body>

</html>