<?php
include('../config.php');
include('../assets/alert_functions.php'); // Add this line to include the alert functions

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Department Heads</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  
  <style>
    /* Alert Styles */
    .notification-toast {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 350px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .notification-icon {
      font-size: 1.25rem;
      margin-right: 1rem;
    }

    .alert {
      display: flex;
      align-items: center;
      padding: 1rem;
      margin-bottom: 1rem;
      border: none;
      animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
  </style>
</head>
<body style="background:rgb(133, 158, 231); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <nav class="navbar navbar-expand-lg" style="background: linear-gradient(to right, #1e3c72, #2a5298); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); padding: 15px 20px;">
    <a class="navbar-brand font-weight-bold text-white" href="admin_dashboard.php" style="font-size: 1.5rem;">
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
    <div class="page-header d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, #4e73df, #224abe); color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 10px 20px rgba(78, 115, 223, 0.1);">
      <div>
        <h2 class="font-weight-bold m-0"><i class="fas fa-users-cog mr-3"></i>Department Heads</h2>
        <p class="text-white-50 mb-0">Manage all department heads in the system</p>
      </div>
      <a href="create_dept_head.php" class="btn btn-light" style="border-radius: 10px; padding: 10px 20px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s;">
        <i class="fas fa-plus mr-2"></i>Add New HOD
      </a>
    </div>
    
    <div class="table-container bg-white rounded" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); padding: 20px; margin-bottom: 30px;">
      <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-id-badge mr-2"></i>ID</th>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-user mr-2"></i>Name</th>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-envelope mr-2"></i>Email</th>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-building mr-2"></i>Department</th>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-signature mr-2"></i>Signature</th>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-calendar-alt mr-2"></i>Created At</th>
              <th style="background-color: #4e73df; color: white; font-weight: 600; border: none; padding: 15px;"><i class="fas fa-cogs mr-2"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr style="transition: all 0.3s;">
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;"><?php echo $row['id']; ?></td>
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;">
                  <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle mr-3" style="width: 40px; height: 40px; font-weight: bold; font-size: 1.2rem;">
                      <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                    </div>
                    <div>
                      <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                    </div>
                  </div>
                </td>
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;"><?php echo htmlspecialchars($row['email']); ?></td>
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;">
                  <span class="badge badge-pill text-white font-weight-bold" style="background: linear-gradient(to right, #36b9cc, #2a96a5); padding: 8px 15px; border-radius: 50px; font-size: 0.85rem;">
                    <?php echo htmlspecialchars($row['department_name']); ?>
                  </span>
                </td>
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;">
                  <?php if (!empty($row['signature_filename'])): ?>
                    <img src="../signatures/<?php echo htmlspecialchars($row['signature_filename']); ?>" alt="Signature" style="max-width: 100px; height: auto; border: 1px solid #e3e6f0; border-radius: 5px; padding: 5px;">
                  <?php else: ?>
                    <span class="badge badge-warning">Not available</span>
                  <?php endif; ?>
                </td>
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;">
                  <i class="far fa-clock mr-1"></i>
                  <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                </td>
                <td style="vertical-align: middle; padding: 15px; border-color: #f0f0f0;">
                  <form method="POST" onsubmit="return confirm('Are you sure you want to delete this department head?');">
                    <input type="hidden" name="head_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_head" class="btn btn-sm text-white" style="background: linear-gradient(to right, #e74a3b, #c0392b); border: none; border-radius: 50px; padding: 8px 15px; font-weight: 600; box-shadow: 0 4px 10px rgba(231, 74, 59, 0.3); transition: all 0.3s;">
                      <i class="fas fa-trash-alt mr-1"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="text-center py-5 text-secondary" style="padding: 50px 20px;">
          <i class="fas fa-users-slash mb-4" style="font-size: 5rem; margin-bottom: 20px; color: #4e73df;"></i>
          <h4>No Department Heads Found</h4>
          <p>There are no department heads in the system yet.</p>
          <a href="create_dept_head.php" class="btn btn-primary mt-3">
            <i class="fas fa-plus mr-2"></i>Add Department Head
          </a>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="text-center">
      <a href="admin_dashboard.php" class="btn text-white" style="background: linear-gradient(to right, #6c757d, #495057); border: none; border-radius: 50px; padding: 12px 25px; font-weight: 600; box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3); transition: all 0.3s;">
        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
      </a>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <script>
    $(document).ready(function() {
      // Auto-hide alerts after 5 seconds
      setTimeout(function() {
        $('.alert').fadeOut('slow');
      }, 5000);
      
      // Add hover effect to table rows
      $('tr').hover(
        function() {
          $(this).css({
            'background-color': '#f8f9fc',
            'transform': 'scale(1.01)',
            'box-shadow': '0 5px 15px rgba(0, 0, 0, 0.05)'
          });
        },
        function() {
          $(this).css({
            'background-color': '',
            'transform': '',
            'box-shadow': ''
          });
        }
      );
      
      // Add hover effect to nav links
      $('.nav-link').hover(
        function() {
          $(this).css({
            'background-color': 'rgba(255, 255, 255, 0.15)',
            'color': 'white',
            'transform': 'translateY(-2px)'
          });
        },
        function() {
          $(this).css({
            'background-color': '',
            'transform': ''
          });
        }
      );
      
      // Add hover effect to delete button
      $('.btn-delete').hover(
        function() {
          $(this).css({
            'transform': 'translateY(-2px)',
            'box-shadow': '0 6px 15px rgba(231, 74, 59, 0.4)'
          });
        },
        function() {
          $(this).css({
            'transform': '',
            'box-shadow': '0 4px 10px rgba(231, 74, 59, 0.3)'
          });
        }
      );
      
      // Add hover effect to back button
      $('.btn-back').hover(
        function() {
          $(this).css({
            'transform': 'translateY(-3px)',
            'box-shadow': '0 6px 15px rgba(108, 117, 125, 0.4)'
          });
        },
        function() {
          $(this).css({
            'transform': '',
            'box-shadow': '0 4px 10px rgba(108, 117, 125, 0.3)'
          });
        }
      );
    });
  </script>
</body>
</html>
