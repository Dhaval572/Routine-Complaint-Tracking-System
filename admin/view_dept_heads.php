<?php
include('../config.php');
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// Handle delete request
if (isset($_POST['delete_head']) && isset($_POST['head_id'])) {
  $head_id = $_POST['head_id'];
  $delete_sql = "DELETE FROM users WHERE id = ? AND role = 'dept_head'";
  $stmt = $conn->prepare($delete_sql);
  $stmt->bind_param("i", $head_id);
  
  if ($stmt->execute()) {
    $_SESSION['alert_type'] = 'success';
    $_SESSION['alert_title'] = 'Success!';
    $_SESSION['alert_message'] = "Department Head deleted successfully!";
  } else {
    $_SESSION['alert_type'] = 'error';
    $_SESSION['alert_title'] = 'Error!';
    $_SESSION['alert_message'] = "Error deleting Department Head: " . $conn->error;
  }
  
  $stmt->close();
  header("Location: view_dept_heads.php");
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Department Heads</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      background: #f8f9fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .navbar {
      background: linear-gradient(to right, #1e3c72, #2a5298);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      padding: 15px 20px;
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      color: white !important;
    }
    
    .nav-link {
      color: rgba(255, 255, 255, 0.85) !important;
      margin: 0 5px;
      border-radius: 5px;
      padding: 8px 15px !important;
      transition: all 0.3s;
    }
    
    .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.15);
      color: white !important;
      transform: translateY(-2px);
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 30px 15px;
    }
    
    .page-header {
      background: linear-gradient(to right, #4e73df, #224abe);
      color: white;
      padding: 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 10px 20px rgba(78, 115, 223, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .page-header h2 {
      margin: 0;
      font-weight: 700;
    }
    
    .table-container {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 30px;
    }
    
    .table {
      margin-bottom: 0;
      border: none;
    }
    
    .table th {
      background-color: #4e73df;
      color: white;
      font-weight: 600;
      border: none;
      padding: 15px;
    }
    
    .table td {
      vertical-align: middle;
      padding: 15px;
      border-color: #f0f0f0;
    }
    
    .table tr {
      transition: all 0.3s;
    }
    
    .table tr:hover {
      background-color: #f8f9fc;
      transform: scale(1.01);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .btn-back {
      background: linear-gradient(to right, #6c757d, #495057);
      border: none;
      border-radius: 50px;
      padding: 12px 25px;
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3);
      transition: all 0.3s;
    }
    
    .btn-back:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(108, 117, 125, 0.4);
    }
    
    .btn-delete {
      background: linear-gradient(to right, #e74a3b, #c0392b);
      border: none;
      border-radius: 50px;
      padding: 8px 15px;
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(231, 74, 59, 0.3);
      transition: all 0.3s;
    }
    
    .btn-delete:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(231, 74, 59, 0.4);
    }
    
    .badge-department {
      background: linear-gradient(to right, #36b9cc, #2a96a5);
      color: white;
      font-weight: 600;
      padding: 8px 15px;
      border-radius: 50px;
      font-size: 0.85rem;
    }
    
    .alert {
      border-radius: 15px;
      padding: 15px 20px;
      margin-bottom: 25px;
      border: none;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }
    
    .alert-danger {
      background-color: #f8d7da;
      color: #721c24;
    }
    
    .empty-state {
      text-align: center;
      padding: 50px 20px;
      color: #6c757d;
    }
    
    .empty-state i {
      font-size: 5rem;
      margin-bottom: 20px;
      color: #4e73df;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="admin_dashboard.php">
      <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="create_department.php">
            <i class="fas fa-building mr-1"></i> Create Department
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="create_dept_head.php">
            <i class="fas fa-user-tie mr-1"></i> Create Dept Head
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="create_officer.php">
            <i class="fas fa-user-plus mr-1"></i> Create Officer
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </nav>
  
  <?php
  // Display toast alert if session variables are set
  if (isset($_SESSION['alert_type']) && isset($_SESSION['alert_message'])) {
    $title = isset($_SESSION['alert_title']) ? $_SESSION['alert_title'] : ($_SESSION['alert_type'] == 'success' ? 'Success!' : 'Error!');
    $type = $_SESSION['alert_type'];
    $message = $_SESSION['alert_message'];
    
    echo '<div class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
            <div class="toast-alert ' . $type . '">
              <div class="toast-header">
                <div class="icon-circle">
                  <i class="fas fa-' . ($type == 'success' ? 'check' : 'exclamation') . '"></i>
                </div>
                <strong class="mr-auto">' . $title . '</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="toast-body">' . $message . '</div>
            </div>
          </div>';
    
    // Clear the session variables
    unset($_SESSION['alert_type']);
    unset($_SESSION['alert_title']);
    unset($_SESSION['alert_message']);
  }
  ?>
  
  <div class="container">
    <div class="page-header">
      <div>
        <h2><i class="fas fa-users-cog mr-3"></i>Department Heads</h2>
        <p class="text-white-50 mb-0">Manage all department heads in the system</p>
      </div>
      <a href="create_dept_head.php" class="btn btn-light">
        <i class="fas fa-plus mr-2"></i>Add New Head
      </a>
    </div>
    
    <!-- Remove or comment out these alert blocks since we're using toast alerts now -->
    <?php /* if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?php echo $_SESSION['success_message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $_SESSION['error_message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; */ ?>
    
    <div class="table-container">
      <?php if ($result->num_rows > 0): ?>
        <table class="table table-hover">
          <thead>
            <tr>
              <th><i class="fas fa-id-badge mr-2"></i>ID</th>
              <th><i class="fas fa-user mr-2"></i>Name</th>
              <th><i class="fas fa-envelope mr-2"></i>Email</th>
              <th><i class="fas fa-building mr-2"></i>Department</th>
              <th><i class="fas fa-calendar-alt mr-2"></i>Created At</th>
              <th><i class="fas fa-cogs mr-2"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white mr-3">
                      <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                    </div>
                    <div>
                      <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                    </div>
                  </div>
                </td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                  <span class="badge badge-department">
                    <?php echo htmlspecialchars($row['department_name']); ?>
                  </span>
                </td>
                <td>
                  <i class="far fa-clock mr-1"></i>
                  <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                </td>
                <td>
                  <form method="POST" onsubmit="return confirm('Are you sure you want to delete this department head?');">
                    <input type="hidden" name="head_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_head" class="btn btn-delete btn-sm">
                      <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users-slash"></i>
          <h4>No Department Heads Found</h4>
          <p>There are no department heads in the system yet.</p>
          <a href="create_dept_head.php" class="btn btn-primary mt-3">
            <i class="fas fa-plus mr-2"></i>Add Department Head
          </a>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="text-center">
      <a href="admin_dashboard.php" class="btn btn-back">
        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
      </a>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
    .avatar-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1.2rem;
    }
  </style>
  
  <script>
    // Initialize toasts
    $(document).ready(function() {
      $('.toast').toast('show');
      
      // Auto-hide after 5 seconds
      setTimeout(function() {
        $('.toast').toast('hide');
      }, 5000);
    });
  </script>
</body>

</html>