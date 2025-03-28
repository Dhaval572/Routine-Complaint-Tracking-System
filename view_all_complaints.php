<?php
include('config.php');
if(!isset($_SESSION['officer_id'])){
    header("Location: officer_login.php");
    exit;
}

$officer_id = $_SESSION['officer_id'];
$sql = "SELECT * FROM complaints WHERE officer_id = '$officer_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <span class="navbar-brand">My Complaints</span>
  <div class="ml-auto">
    <a href="officer_dashboard.php" class="btn btn-outline-light">Dashboard</a>
  </div>
</nav>
<div class="container mt-5">
  <h4>All Complaints Assigned to You</h4>
  <?php if($result && $result->num_rows > 0){ ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Status</th>
          <th>Registered At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php while($row = $result->fetch_assoc()){ ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['title']); ?></td>
          <td><?php echo ucfirst($row['status']); ?></td>
          <td><?php echo $row['created_at']; ?></td>
          <td>
            <?php if($row['status'] == 'in_progress'){ ?>
              <a href="solve_complaint.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Solve</a>
              <a href="refer_complaint.php?complaint_id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Refer</a>
            <?php } else { echo "N/A"; } ?>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  <?php } else {
      echo "<div class='alert alert-info'>No complaints assigned to you.</div>";
  } ?>
</div>
</body>
</html>
