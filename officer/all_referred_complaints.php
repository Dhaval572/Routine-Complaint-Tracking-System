<?php
include('../config.php');
if(!isset($_SESSION['officer_id'])){
    header("Location: officer_login.php");
    exit;
}
$officer_id = $_SESSION['officer_id'];
// Select complaints that have referral history and are associated with you,
// either because you were the referrer or the complaint is currently assigned to you.
$sql = "SELECT c.*, 
        d.name as dept_name,
        (SELECT name FROM users u WHERE u.id = c.referred_by) AS referrer,
        (SELECT name FROM users u WHERE u.id = c.officer_id) AS assigned_officer
        FROM complaints c
        JOIN departments d ON c.department_id = d.id
        WHERE c.referred_by IS NOT NULL 
          AND (c.officer_id = '$officer_id' OR c.referred_by = '$officer_id')
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Referred Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    th, td { font-size: 0.9rem; }
  </style>
  <!-- Include jQuery and Bootstrap JS for modal functionality -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
  function openActivity(complaint_id){
      $.ajax({
          url: '../user/fetch_activity.php',
          type: 'GET',
          data: { complaint_id: complaint_id },
          success: function(data){
              $("#activityContent").html(data);
              $("#activityModal").modal('show');
          }
      });
  }
  </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">Referred Complaints</span>
    <div class="ml-auto">
      <a href="officer_dashboard.php" class="btn btn-outline-light">Dashboard</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h4>Complaints with Referral History</h4>
    <?php if($result && $result->num_rows > 0){ ?>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Department</th>
            <th>Referred By</th>
            <th>Referred To</th>
            <th>Registered At</th>
            <th>Status</th>
            <th>Activity</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()){
              // Determine referral display:
              // If you are the referrer (i.e. c.referred_by equals your ID), then:
              //    - "Referred By" will show "You"
              //    - "Referred To" will show the assigned officer’s name.
              // Else if you are the current assignee (c.officer_id equals your ID) and someone else referred it:
              //    - "Referred By" shows the referrer’s name
              //    - "Referred To" shows "You".
              if($row['referred_by'] == $officer_id){
                  $referredBy = "You";
                  $referredTo = $row['assigned_officer'];
              } else if($row['officer_id'] == $officer_id){
                  $referredBy = $row['referrer'];
                  $referredTo = "You";
              } else {
                  $referredBy = $row['referrer'];
                  $referredTo = $row['assigned_officer'];
              }
              ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
            <td><?php echo $referredBy; ?></td>
            <td><?php echo $referredTo; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td><?php echo ucfirst($row['status']); ?></td>
            <td>
              <button class="btn btn-info btn-sm" onclick="openActivity(<?php echo $row['id']; ?>)">View</button>
            </td>
            <td>
              <?php 
              // Allow "Solve" only if you are the current assignee and the complaint status is "referred"
              if($row['officer_id'] == $officer_id && $row['status'] == 'referred'){
                  echo '<a href="solve_referred_complaints.php?complaint_id='.$row['id'].'" class="btn btn-primary btn-sm">Solve</a>';
              } else {
                  echo "N/A";
              }
              ?>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    <?php } else {
        echo "<div class='alert alert-info'>No referred complaints found.</div>";
    } ?>
  </div>

  <!-- Modal for Activity -->
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
          <!-- Activity details will be loaded here via AJAX -->
        </div>
      </div>
    </div>
  </div>

</body>
</html>
