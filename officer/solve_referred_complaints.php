<?php
include('../config.php');
if(!isset($_SESSION['officer_id'])){
    header("Location: officer_login.php");
    exit;
}

$officer_id = $_SESSION['officer_id'];

// If a specific complaint is selected
if(isset($_GET['complaint_id'])){
    $complaint_id = intval($_GET['complaint_id']);
    $sql = "SELECT * FROM complaints WHERE id = '$complaint_id' AND officer_id = '$officer_id' AND status = 'referred'";
    $result = $conn->query($sql);
    if($result && $result->num_rows > 0){
        $complaint = $result->fetch_assoc();
    } else {
        $error = "Complaint not found or not in referred status.";
    }
    
    if(isset($_POST['solve'])){
        $response = $conn->real_escape_string($_POST['response']);
        $remark   = $conn->real_escape_string($_POST['remark']);
        $update_sql = "UPDATE complaints SET status = 'solved', response = '$response', remarks = '$remark' WHERE id = '$complaint_id'";
        if($conn->query($update_sql)){
            // Log activity: Solved referred complaint
            $activity_sql = "INSERT INTO complaint_activity (complaint_id, activity, activity_by) VALUES ('$complaint_id', 'Referred Complaint Solved by Officer', '$officer_id')";
            $conn->query($activity_sql);
            $success = "Complaint marked as solved.";
            // Refresh complaint details
            $sql = "SELECT * FROM complaints WHERE id = '$complaint_id'";
            $result = $conn->query($sql);
            $complaint = $result->fetch_assoc();
        } else {
            $error = "Error updating complaint: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Solve Referred Complaint</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand">Solve Referred Complaint</span>
  <div class="ml-auto">
    <a href="officer_dashboard.php" class="btn btn-outline-light">Dashboard</a>
  </div>
</nav>
<div class="container mt-5">
  <?php if(isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
  <?php if(isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
  
  <?php if(isset($complaint)): ?>
    <h4>Complaint Details (ID: <?php echo $complaint['id']; ?>)</h4>
    <p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
    <hr>
    <h5>Solve This Referred Complaint</h5>
    <form method="POST" action="">
      <div class="form-group">
        <label>Response</label>
        <textarea name="response" required class="form-control" placeholder="Enter response"></textarea>
      </div>
      <div class="form-group">
        <label>Remark</label>
        <textarea name="remark" required class="form-control" placeholder="Enter remark"></textarea>
      </div>
      <button type="submit" name="solve" class="btn btn-success">Mark as Solved</button>
    </form>
    <a href="solve_referred_complaints.php" class="btn btn-secondary mt-3">Back to List</a>
  <?php else: ?>
    <h4>Complaints Referred to You</h4>
    <?php 
    $list_sql = "SELECT * FROM complaints WHERE officer_id = '$officer_id' AND status = 'referred' ORDER BY created_at DESC";
    $list_result = $conn->query($list_sql);
    if($list_result && $list_result->num_rows > 0){ ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Title</th>
            <th>Registered At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = $list_result->fetch_assoc()){ ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
              <a href="solve_referred_complaints.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Solve</a>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    <?php } else {
      echo "<div class='alert alert-info'>No referred complaints available.</div>";
    } ?>
  <?php endif; ?>
</div>
</body>
</html>
