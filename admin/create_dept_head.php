<?php
include('../config.php');

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// Fetch departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

if (isset($_POST['create_dept_head'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $department_id = $_POST['department_id'];
  $role = 'dept_head';

  // Insert department head details into the users table (without signature)
  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssi", $name, $email, $password, $role, $department_id);
  if ($stmt->execute()) {
    $user_id = $conn->insert_id;
    $success = "Department Head created successfully";

    // Process signature file upload and insert into the signatures table
    if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
      $uploadDir = '../signatures/'; // Ensure this folder exists and is writable
      $filename = time() . "_" . basename($_FILES['signature']['name']);
      $targetFile = $uploadDir . $filename;
      if (move_uploaded_file($_FILES['signature']['tmp_name'], $targetFile)) {
          $stmtSig = $conn->prepare("INSERT INTO signatures (user_id, signature_filename) VALUES (?, ?)");
          $stmtSig->bind_param("is", $user_id, $filename);
          $stmtSig->execute();
          $stmtSig->close();
      }
    }
  } else {
    $error = "Error creating Department Head: " . $conn->error;
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Department Head</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* Optional custom styling */
    body {
      background-color: #f8f9fa;
      color: #212529;
    }
    .card {
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2>Create Department Head</h2>
    <?php if (isset($success)): ?>
      <div class="alert alert-success py-2 d-flex align-items-center rounded">
        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
      </div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger py-2 d-flex align-items-center rounded">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" required class="form-control" placeholder="Enter name">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required class="form-control" placeholder="Enter email">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required class="form-control" placeholder="Enter password">
      </div>
      <div class="form-group">
        <label>Select Department</label>
        <select name="department_id" class="form-control" required>
          <option value="">Select Department</option>
          <?php while ($dept = $departments->fetch_assoc()) { ?>
            <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label>Department Head Signature</label>
        <input type="file" name="signature" required class="form-control-file" accept="image/*">
      </div>
      <button type="submit" name="create_dept_head" class="btn btn-primary">Create Department Head</button>
    </form>
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
