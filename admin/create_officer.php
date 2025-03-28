<?php
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// Fetch departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

if (isset($_POST['create_officer'])) {

  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $department_id = $_POST['department_id'];
  $role = 'officer';

  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssi", $name, $email, $password, $role, $department_id);

  if ($stmt->execute()) {
    $success = "Officer created successfully";
  } else {
    $error = "Error creating officer";
  }
  $stmt->close();

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Officer</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/create_officer.css">
</head>

<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-lg">
          <div class="card-header bg-white text-center py-3">
            <h3 class="font-weight-bold text-dark mb-2">
              <i class="fas fa-user-plus mr-2 text-primary"></i>Create Officer Account
            </h3>
            <p class="text-muted small mb-0">Add a new department officer to the system</p>
          </div>
          <div class="card-body p-4">

            <?php if (isset($success)): ?>
              <div class='alert alert-success py-2 d-flex align-items-center rounded'>
                <i class='fas fa-check-circle mr-2'></i><?php echo $success; ?>
              </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
              <div class='alert alert-danger py-2 d-flex align-items-center rounded'>
                <i class='fas fa-exclamation-circle mr-2'></i><?php echo $error; ?>
              </div>
            <?php endif; ?>

            <form method="POST" action="">
              <div class="form-group">
                <label><i class="fas fa-user text-primary mr-2"></i>Officer Name</label>
                <input type="text" name="name" required class="form-control shadow-sm" placeholder="Enter full name">
              </div>
              <div class="form-group">
                <label><i class="fas fa-envelope text-primary mr-2"></i>Email Address</label>
                <input type="email" name="email" required class="form-control shadow-sm"
                  placeholder="Enter official email">
              </div>
              <div class="form-group">
                <label><i class="fas fa-lock text-primary mr-2"></i>Password</label>
                <input type="password" name="password" required class="form-control shadow-sm"
                  placeholder="Create a strong password">
              </div>
              <div class="form-group">
                <label><i class="fas fa-building text-primary mr-2"></i>Department</label>
                <select name="department_id" class="form-control shadow-sm" required>
                  <option value="">Select Department</option>
                  <?php while ($dept = $departments->fetch_assoc()) { ?>
                    <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group mt-4 mb-3">
                <button type="submit" name="create_officer" class="btn btn-primary btn-block py-2 shadow">
                  <i class="fas fa-user-plus mr-2"></i>Create Officer Account
                </button>
              </div>
            </form>
            <div class="text-center mt-3">
              <a href="admin_dashboard.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>