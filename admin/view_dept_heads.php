<?php
include('../config.php');
include('../assets/alert_functions.php');

// Check if signatures table exists and create it if it doesn't
$tableCheckQuery = "SHOW TABLES LIKE 'signatures'";
$tableExists = $conn->query($tableCheckQuery)->num_rows > 0;

if (!$tableExists) {
  // Create signatures table if it doesn't exist
  $createTableQuery = "CREATE TABLE signatures (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    signature_filename VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  )";
  $conn->query($createTableQuery);
}

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

$sql = "SELECT u.*, d.name AS department_name, s.signature_filename 
        FROM users u 
        LEFT JOIN departments d ON u.department_id = d.id 
        LEFT JOIN signatures s ON s.user_id = u.id 
        WHERE u.role = 'dept_head'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>View Department Heads</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../assets/css/admin_dept_heads.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
    <a class="navbar-brand" href="admin_dashboard.php">
      <i class="fas fa-user-shield mr-2"></i>Admin Dashboard
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a href="admin_dashboard.php" class="btn btn-danger" style="margin: 0 5px; border-radius: 5px; padding: 8px 15px !important;">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
          </a>
        </li>
      </ul>
    </div>
  </nav>
  
  <?php
  // Display alert if session variables are set
  if (isset($_SESSION['alert_type']) && isset($_SESSION['alert_message'])) {
    $title = isset($_SESSION['alert_title']) ? $_SESSION['alert_title'] : ($_SESSION['alert_type'] == 'success' ? 'Success!' : 'Error!');
    displayAlert($_SESSION['alert_type'], $_SESSION['alert_message'], null, true, $title);
  
    // Clear the session variables
    unset($_SESSION['alert_type']);
    unset($_SESSION['alert_title']);
    unset($_SESSION['alert_message']);
  }
  ?>
  
  <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 30px 15px;">
    <div class="page-header">
      <div>
        <h2 class="font-weight-bold m-0"><i class="fas fa-users-cog mr-2"></i>Department Heads</h2>
        <p class="text-white-50 mb-0">Manage all department heads in the system</p>
      </div>
      <a href="create_dept_head.php" class="btn btn-light add-hod-btn">
        <i class="fas fa-plus mr-1"></i>Add New HOD
      </a>
    </div>
    
    <div class="table-container">
      <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th><i class="fas fa-id-badge mr-1"></i>ID</th>
                <th><i class="fas fa-user mr-1"></i>Name</th>
                <th><i class="fas fa-envelope mr-1"></i>Email</th>
                <th><i class="fas fa-building mr-1"></i>Department</th>
                <th><i class="fas fa-signature mr-1"></i>Signature</th>
                <th><i class="fas fa-calendar-alt mr-1"></i>Created</th>
                <th><i class="fas fa-cogs mr-1"></i>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              // Reset result pointer
              $result->data_seek(0);
              while ($row = $result->fetch_assoc()): 
              ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="user-avatar">
                        <?php echo strtoupper(substr($row['name'] ?? '', 0, 1)); ?>
                      </div>
                      <div>
                        <strong><?php echo htmlspecialchars($row['name'] ?? ''); ?></strong>
                      </div>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                  <td>
                    <span class="dept-badge">
                      <?php echo htmlspecialchars($row['department_name'] ?? 'No Department'); ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($row['signature_filename'])): ?>
                      <img src="../signatures/<?php echo htmlspecialchars($row['signature_filename']); ?>" alt="Signature" class="signature-img">
                    <?php else: ?>
                      <span class="badge badge-warning">Not available</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                  </td>
                  <td>
                    <form method="POST" name="delete-form">
                      <input type="hidden" name="head_id" value="<?php echo $row['id']; ?>">
                      <button type="submit" name="delete_head" class="btn btn-sm btn-gradient-danger">
                        <i class="fas fa-trash-alt"></i> Delete
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <?php 
        // Reset result pointer
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()): 
        ?>
          <div class="dept-head-card">
            <div class="d-flex align-items-center mb-3">
              <div class="user-avatar">
                <?php echo strtoupper(substr($row['name'] ?? '', 0, 1)); ?>
              </div>
              <div class="card-content">
                <h5 class="mb-0"><?php echo htmlspecialchars($row['name'] ?? ''); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
              </div>
            </div>
            
            <div class="mb-2 card-content">
              <strong><i class="fas fa-building mr-2"></i>Department:</strong>
              <div class="mt-1">
                <span class="dept-badge">
                  <?php echo htmlspecialchars($row['department_name'] ?? 'No Department'); ?>
                </span>
              </div>
            </div>
            
            <div class="mb-2 card-content">
              <strong><i class="fas fa-signature mr-2"></i>Signature:</strong>
              <div class="mt-1">
                <?php if (!empty($row['signature_filename'])): ?>
                  <img src="../signatures/<?php echo htmlspecialchars($row['signature_filename']); ?>" alt="Signature" class="signature-img">
                <?php else: ?>
                  <span class="badge badge-warning">Not available</span>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="mb-3 card-content">
              <strong><i class="fas fa-calendar-alt mr-2"></i>Created:</strong>
              <span><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
            </div>
            
            <div class="text-right">
              <form method="POST" name="delete-form">
                <input type="hidden" name="head_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="delete_head" class="btn btn-sm btn-gradient-danger">
                  <i class="fas fa-trash-alt mr-1"></i> Delete
                </button>
              </form>
            </div>
          </div>
        <?php endwhile; ?>
        
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users-slash mb-4 empty-state-icon"></i>
          <h4>No Department Heads Found</h4>
          <p>There are no department heads in the system yet.</p>
          <a href="create_dept_head.php" class="btn btn-primary mt-3">
            <i class="fas fa-plus mr-2"></i>Add Department Head
          </a>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="text-center">
      <a href="admin_dashboard.php" class="btn btn-gradient-secondary">
        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
      </a>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Custom JS -->
  <script src="../assets/js/admin_dept_heads.js"></script>
</body>
</html>
