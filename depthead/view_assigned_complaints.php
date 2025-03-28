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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>View Assigned Complaints</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand">Assigned Complaints</span>
    <div class="ml-auto">
      <a href="dept_head_dashboard.php" class="btn btn-outline-light">Dashboard</a>
    </div>
  </nav>
  <div class="container mt-5">
    <h3>Complaints in Your Department</h3>
    <?php if ($result && $result->num_rows > 0) { ?>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>Title</th>
            <th>Department</th>
            <th>Status</th>
            <th>Assigned Officer</th>
            <th>Registered At</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['dept_name']); ?></td>
              <td><?php echo ucfirst($row['status']); ?></td>
              <td><?php echo ($row['officer_name']) ? htmlspecialchars($row['officer_name']) : 'Not Assigned'; ?></td>
              <td><?php echo $row['created_at']; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else {
      echo "<div class='alert alert-info'>No complaints found.</div>";
    } ?>
  </div>
</body>

</html>