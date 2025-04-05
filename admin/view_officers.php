<?php
include('../config.php');
// Removed alert_functions.php include as it wasn't used

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
  <style>
    .btn-circle {
      width: 32px;
      height: 32px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-rounded-rect {
      border-radius: 20px;
      padding: 5px 12px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .dashboard-btn {
      margin: 0 5px;
      border-radius: 5px;
      padding: 8px 15px !important;
    }

    /* Simplified CSS - removed duplicate/unsused styles */
    .alert-message {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border-left: 5px solid;
      padding: 15px 20px;
      animation: slideIn 0.5s ease-out, fadeOut 0.5s ease-in 5s forwards;
    }

    .alert-success {
      border-left-color: #28a745;
    }

    .alert-danger {
      border-left-color: #dc3545;
    }

    .alert-warning {
      border-left-color: #ffc107;
    }

    .alert-info {
      border-left-color: #17a2b8;
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

    @keyframes fadeOut {
      from {
        opacity: 1;
      }

      to {
        opacity: 0;
      }
    }

    /* Container Styles */
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 30px 15px;
    }

    /* Navbar Toggle Styles */
    .navbar-toggler {
      margin-right: 15px;
    }

    /* User Avatar Styles */
    .user-avatar {
      width: 40px;
      height: 40px;
      background-color: #4a90e2;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      margin-right: 12px;
    }

    /* Empty State Styles */
    .empty-state .empty-state-icon {
      font-size: 4rem;
      color: #6c757d;
    }

    /* Card Styles */
    .dept-head-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-bottom: 20px;
    }

    /* Button Styles */
    .add-hod-btn {
      border-radius: 8px;
      padding: 8px 20px;
    }

    /* Table Container */
    .table-container {
      margin-top: 25px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    /* Signature Image Styles */
    .signature-img {
      max-width: 120px;
      max-height: 60px;
      border: 1px solid #e0e0e0;
      padding: 5px;
      background-color: #fff;
    }

    .signature-error {
      color: #856404;
      background-color: #fff3cd;
      border: 1px solid #ffeeba;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 0.85rem;
      display: inline-block;
    }
  </style>
</head>

<body>
  <!-- Add alert container at the top of body -->
  <div id="alertContainer"></div>

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
          <a href="admin_dashboard.php" class="btn btn-danger dashboard-btn">
            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
          </a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container main-container">
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
                <th><i class="fas fa-cog mr-1"></i>Actions</th>
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
                      <?php
                      $signature_path = "../signatures/" . htmlspecialchars($row['signature_filename']);
                      if (file_exists($signature_path)):
                        ?>
                        <img src="<?php echo $signature_path; ?>" alt="Signature" class="signature-img">
                      <?php else: ?>
                        <span class="signature-error">
                          <i class="fas fa-exclamation-triangle mr-1"></i> Signature file missing
                        </span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="badge badge-warning">Not available</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                  </td>
                  <td>
                    <a href="#" class="btn btn-sm btn-danger btn-rounded-rect delete-officer"
                      data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                      <i class="fas fa-trash-alt mr-1"></i> Delete
                    </a>
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
                  <?php
                  $signature_path = "../signatures/" . htmlspecialchars($row['signature_filename']);
                  if (file_exists($signature_path)):
                    ?>
                    <img src="<?php echo $signature_path; ?>" alt="Signature" class="signature-img">
                  <?php else: ?>
                    <span class="signature-error">
                      <i class="fas fa-exclamation-triangle mr-1"></i> Signature file missing
                    </span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="badge badge-warning">Not available</span>
                <?php endif; ?>
              </div>
            </div>

            <div class="mb-3 card-content">
              <strong><i class="fas fa-calendar-alt mr-2"></i>Created:</strong>
              <span><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
            </div>

            <div class="card-actions">
              <a href="#" class="btn btn-sm btn-danger btn-rounded-rect delete-officer" data-id="<?php echo $row['id']; ?>"
                data-name="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                <i class="fas fa-trash-alt mr-1"></i> Delete
              </a>
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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.delete-officer').forEach(button => {
        button.addEventListener('click', function (e) {
          e.preventDefault();
          const officerId = this.dataset.id;
          const officerName = this.dataset.name;

          if (confirm(`Delete officer "${officerName}"?`)) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax_delete_officer.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
              if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.success) {
                  showAlert(`Officer "${officerName}" deleted.`, 'success');
                  const row = button.closest('tr, .dept-head-card');
                  row?.remove();
                  if (!document.querySelector('tbody tr, .dept-head-card')) {
                    location.reload();
                  }
                }
              }
            };
            xhr.send(`id=${officerId}`);
          }
        });
      });

      function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-message`;
        alertDiv.innerHTML = `
          <div class="alert-content">
            <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
            <div>${message}</div>
          </div>
        `;

        document.getElementById('alertContainer').appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
      }
    });
  </script>
</body>

</html>