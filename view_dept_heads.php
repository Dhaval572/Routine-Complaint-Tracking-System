<?php
include('config.php');
if(!isset($_SESSION['admin_id'])){
   header("Location: admin_login.php");
   exit;
}

$sql = "SELECT u.*, d.name AS department_name 
        FROM users u 
        LEFT JOIN departments d ON u.department_id = d.id 
        WHERE u.role = 'dept_head'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Department Heads</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="admin_dashboard.php">Admin Dashboard</a>
  <div class="navbar-nav">
    <a class="nav-item nav-link" href="create_department.php">Create Department</a>
    <a class="nav-item nav-link" href="create_dept_head.php">Create Dept Head</a>
    <a class="nav-item nav-link" href="create_officer.php">Create Officer</a>
    <a class="nav-item nav-link" href="logout.php">Logout</a>
  </div>
</nav>
<div class="container mt-5">
  <h2>Department Heads</h2>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Department</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['department_name']); ?></td>
        <td><?php echo $row['created_at']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
