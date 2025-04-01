<?php
include('../config.php');
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// Determine the action (edit or delete)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
  header("Location: view_departments.php");
  exit;
}

// Handle DELETE action
if ($action == 'delete') {
  // Delete the department
  $sql = "DELETE FROM departments WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    $_SESSION['success_message'] = "Department deleted successfully!";
  } else {
    $_SESSION['error_message'] = "Error deleting department: " . $conn->error;
  }

  header("Location: view_departments.php");
  exit;
}

// Handle EDIT action
if ($action == 'edit') {
  // Fetch department data
  $sql = "SELECT * FROM departments WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows == 0) {
    header("Location: view_departments.php");
    exit;
  }

  $department = $result->fetch_assoc();

  // Process form submission for edit
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
      $errors[] = "Department name is required";
    }
    
    if (empty($errors)) {
      // Update department
      $update_sql = "UPDATE departments SET name = ?, description = ? WHERE id = ?";
      $update_stmt = $conn->prepare($update_sql);
      $update_stmt->bind_param("ssi", $name, $description, $id);
      
      if ($update_stmt->execute()) {
        // Set success message in session
        $_SESSION['success_message'] = "Department updated successfully!";
        header("Location: view_departments.php");
        exit;
      } else {
        $errors[] = "Error updating department: " . $conn->error;
      }
    }
  }
  
  // Display edit form
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </head>

  <body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4 shadow">
      <div class="container">
        <a class="navbar-brand font-weight-bold" href="admin_dashboard.php">
          <i class="fas fa-building mr-2"></i>Departments
        </a>
        <a href="admin_dashboard.php" class="btn btn-danger rounded-pill px-3 py-2 shadow-sm">
          <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
      </div>
    </nav>

    <div class="container py-3">
      <div class="row mb-3">
        <div class="col-md-8">
          <h2 class="text-success font-weight-bold">
            <i class="fas fa-edit mr-2"></i>Edit Department
          </h2>
        </div>
        <div class="col-md-4 text-right">
          <a href="view_departments.php" class="btn btn-outline-success rounded-pill px-4 py-2 shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Departments
          </a>
        </div>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger rounded-lg shadow-sm">
          <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
              <li><?php echo $error; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="card rounded-lg shadow border-0 mb-4">
        <div class="card-header bg-success text-white py-3">
          <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-building mr-2"></i>Department Details
          </h5>
        </div>
        <div class="card-body p-4">
          <form action="" method="POST">
            <div class="form-group">
              <label for="name" class="font-weight-bold text-success">Department Name</label>
              <input type="text" class="form-control rounded-pill" id="name" name="name" value="<?php echo htmlspecialchars($department['name']); ?>" required>
            </div>
            <div class="form-group">
              <label for="description" class="font-weight-bold text-success">Description</label>
              <textarea class="form-control rounded" id="description" name="description" rows="4"><?php echo htmlspecialchars($department['description']); ?></textarea>
            </div>
            <div class="text-center mt-4">
              <button type="submit" class="btn btn-success rounded-pill px-5 py-2 shadow-sm">
                <i class="fas fa-save mr-2"></i>Update Department
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>

  </html>
  <?php
  exit;
}

// If no valid action is specified, redirect to departments list
header("Location: view_departments.php");
exit;
?>