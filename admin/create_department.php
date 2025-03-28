<?php
include('../config.php');
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

if (isset($_POST['create_dept'])) {
  $name = $_POST['name'];
  $description = $_POST['description'];

  $stmt = $conn->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
  $stmt->bind_param("ss", $name, $description);
  if ($stmt->execute()) {
    $success = "Department created successfully";
  } else {
    $error = "Error creating department";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Department</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h2>Create Department</h2>
    <?php if (isset($success)) {
      echo "<div class='alert alert-success'>$success</div>";
    } ?>
    <?php if (isset($error)) {
      echo "<div class='alert alert-danger'>$error</div>";
    } ?>
    <form method="POST" action="">
      <div class="form-group">
        <label>Department Name</label>
        <input type="text" name="name" required class="form-control" placeholder="Enter department name">
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" placeholder="Enter description"></textarea>
      </div>
      <button type="submit" name="create_dept" class="btn btn-primary">Create Department</button>
    </form>
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>
</body>

</html>