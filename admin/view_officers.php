<?php
include('../config.php');
include('../assets/alert_functions.php');

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

// Join signatures table to get signature filename for each officer
$sql = "SELECT u.*, d.name AS department_name, s.signature_filename 
        FROM users u 
        LEFT JOIN departments d ON u.department_id = d.id 
        LEFT JOIN signatures s ON s.user_id = u.id
        WHERE u.role = 'officer'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>View Officers</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

  <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 30px 15px;">
    <div class="page-header">
      <div>
        <h2 class="font-weight-bold m-0"><i class="fas fa-user-tie mr-2"></i>Department Officers</h2>
        <p class="text-white-50 mb-0">Manage all department officers in the system</p>
      </div>
      <a href="create_officer.php" class="btn btn-light add-hod-btn">
        <i class="fas fa-plus mr-1"></i>Add New Officer
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
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
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
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <?php 
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
          </div>
        <?php endwhile; ?>
        
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-users-slash mb-4 empty-state-icon"></i>
          <h4>No Officers Found</h4>
          <p>There are no department officers in the system yet.</p>
          <a href="create_officer.php" class="btn btn-primary mt-3">
            <i class="fas fa-plus mr-2"></i>Add Officer
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="../assets/js/admin_dept_heads.js"></script>
</body>
</html>
