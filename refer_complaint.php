<?php
include('config.php');
if(!isset($_SESSION['officer_id'])){
    header("Location: officer_login.php");
    exit;
}

$officer_id = $_SESSION['officer_id'];
$department_id = $_SESSION['officer_department'];

if(isset($_GET['complaint_id'])){
    $complaint_id = intval($_GET['complaint_id']);
    $sql = "SELECT * FROM complaints WHERE id = '$complaint_id' AND officer_id = '$officer_id'";
    $result = $conn->query($sql);
    if($result && $result->num_rows > 0){
        $complaint = $result->fetch_assoc();
    } else {
        $error = "Complaint not found or not authorized.";
    }

    // Fetch list of officers in the same department excluding current officer
    $officer_sql = "SELECT id, name FROM users WHERE role = 'officer' AND department_id = '$department_id' AND id != '$officer_id'";
    $officer_list = $conn->query($officer_sql);

    if(isset($_POST['refer'])){
        $new_officer_id = $_POST['new_officer_id'];
        $referral_remark = $conn->real_escape_string($_POST['referral_remark']);
        // Update complaint: change officer_id, set status to referred and log referral details
        $update_sql = "UPDATE complaints SET officer_id = '$new_officer_id', status = 'referred', referred_by = '$officer_id', referred_at = NOW(), remarks = '$referral_remark' WHERE id = '$complaint_id'";
        if($conn->query($update_sql)){
            // Log activity: Complaint Referred
            $activity_sql = "INSERT INTO complaint_activity (complaint_id, activity, activity_by) VALUES ('$complaint_id', 'Complaint Referred to Officer', '$officer_id')";
            $conn->query($activity_sql);
            $success = "Complaint referred successfully.";
        } else {
            $error = "Error referring complaint: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Refer Complaint</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand">Refer Complaint</span>
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
    <h5>Refer Complaint to Another Officer</h5>
    <form method="POST" action="">
      <div class="form-group">
        <label>Select New Officer</label>
        <select name="new_officer_id" class="form-control" required>
          <option value="">Select Officer</option>
          <?php while($officer = $officer_list->fetch_assoc()){ ?>
            <option value="<?php echo $officer['id']; ?>"><?php echo htmlspecialchars($officer['name']); ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label>Referral Remark</label>
        <textarea name="referral_remark" required class="form-control" placeholder="Reason for referring this complaint"></textarea>
      </div>
      <button type="submit" name="refer" class="btn btn-warning">Refer Complaint</button>
    </form>
    <a href="refer_complaint.php" class="btn btn-secondary mt-3">Back to List</a>
  <?php else: ?>
    <h4>Assigned Complaints (In Progress)</h4>
    <?php 
    $list_sql = "SELECT * FROM complaints WHERE officer_id = '$officer_id' AND status = 'in_progress' ORDER BY created_at DESC";
    $list_result = $conn->query($list_sql);
    if($list_result && $list_result->num_rows > 0){ ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
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
              <a href="refer_complaint.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Refer</a>
            </td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    <?php } else {
      echo "<div class='alert alert-info'>No complaints available for referral.</div>";
    } ?>
  <?php endif; ?>
</div>
</body>
</html>
