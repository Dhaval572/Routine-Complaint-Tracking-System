<?php
include('../config.php');
if (!isset($_SESSION['officer_id'])) {
  header("Location: officer_login.php");
  exit;
}

$officer_id = $_SESSION['officer_id'];
// Fetch complaints where referred_by is not null and the current officer is assigned
$sql = "SELECT * FROM complaints WHERE officer_id = '$officer_id' AND referred_by IS NOT NULL ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Referred Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">Referred Complaints</span>
    <div class="ml-auto">
      <a href="officer_dashboard.php" class="btn btn-outline-light">Dashboard</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h4>Complaints Referred to You</h4>
    <?php if ($result && $result->num_rows > 0) { ?>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Referred By (Officer ID)</th>
            <th>Referred At</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo $row['referred_by']; ?></td>
              <td><?php echo $row['referred_at']; ?></td>
              <td><?php echo ucfirst($row['status']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else {
      echo "<div class='alert alert-info'>No complaints have been referred to you.</div>";
    } ?>
  </div>
</body>

</html>