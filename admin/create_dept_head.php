<?php
include('../config.php');

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

/**
 * Display a toast-style alert message using Bootstrap classes
 * 
 * @param string $type Type of alert ('success' or 'error')
 * @param string $title Alert title
 * @param string $message Alert message
 * @return void
 */
function displayToastAlert($type, $title, $message)
{
  $icon = ($type == 'success') ? 'check' : 'exclamation';
  $bgClass = ($type == 'success') ? 'bg-success text-white' : 'bg-danger text-white';

  echo '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000" id="toast' . time() . '" 
          style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 350px; max-width: 90%;">
          <div class="toast-header ' . $bgClass . '">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-white mr-2" 
                 style="width: 30px; height: 30px;">
              <i class="fas fa-' . $icon . ' fa-sm text-' . ($type == 'success' ? 'success' : 'danger') . '"></i>
            </div>
            <strong class="mr-auto">' . $title . '</strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="toast-body p-3 shadow-sm">
            ' . $message . '
          </div>
        </div>';
}

// Fetch departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

if (isset($_POST['create_dept_head'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $department_id = $_POST['department_id'];
  $role = 'dept_head';

  // Password validation - must be 6-10 characters
  if (strlen($password) < 6 || strlen($password) > 10) {
    $_SESSION['alert_type'] = 'error';
    $_SESSION['alert_title'] = 'Error';
    $_SESSION['alert_message'] = "Password must be between 6 and 10 characters long";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  } else {
    // Hash password and proceed with insertion
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $name, $email, $hashed_password, $role, $department_id);
    if ($stmt->execute()) {
      $user_id = $conn->insert_id;

      // Process signature file upload
      if (isset($_FILES['signature']) && $_FILES['signature']['error'] == 0) {
        $uploadDir = '../signatures/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }
        
        $filename = time() . "_" . basename($_FILES['signature']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['signature']['tmp_name'], $targetFile)) {
          // Check if signatures table exists
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
          
          // Now insert the signature
          $stmtSig = $conn->prepare("INSERT INTO signatures (user_id, signature_filename) VALUES (?, ?)");
          $stmtSig->bind_param("is", $user_id, $filename);
          $stmtSig->execute();
          $stmtSig->close();
        }
      }

      $_SESSION['alert_type'] = 'success';
      $_SESSION['alert_title'] = 'Success!';
      $_SESSION['alert_message'] = "Department Head created successfully";
    } else {
      $_SESSION['alert_type'] = 'error';
      $_SESSION['alert_title'] = 'Error';
      $_SESSION['alert_message'] = "Error creating Department Head";
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Department Head</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .card {
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      border: none;
      max-width: 550px;
      width: 100%;
    }

    .card-header {
      background: linear-gradient(to right, #4e73df, #224abe);
      color: white;
      text-align: center;
      padding: 25px;
      border-bottom: none;
    }

    .card-header h3 {
      margin-bottom: 0;
      font-weight: 600;
    }

    .card-body {
      padding: 30px;
    }

    .form-group label {
      font-weight: 600;
      color: #4e73df;
      margin-bottom: 8px;
    }

    .form-control {
      border-radius: 10px;
      padding: 12px 15px;
      height: auto;
      border: 1px solid #e1e5eb;
      box-shadow: none;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: #4e73df;
      box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn-primary {
      background: linear-gradient(to right, #4e73df, #224abe);
      border: none;
      border-radius: 50px;
      padding: 12px 20px;
      font-weight: 600;
      letter-spacing: 0.5px;
      box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
      transition: all 0.3s;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(78, 115, 223, 0.6);
    }

    .btn-secondary {
      background: #6c757d;
      border: none;
      border-radius: 50px;
      padding: 12px 20px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s;
    }

    .btn-secondary:hover {
      background: #5a6268;
    }

    .header-icon {
      font-size: 3rem;
      margin-bottom: 15px;
      color: white;
    }

    .toast {
      opacity: 1 !important;
      border: none;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .card-footer {
      background-color: #f8f9fc;
      border-top: 1px solid #e3e6f0;
      padding: 20px 30px;
      text-align: center;
    }
  </style>
</head>

<body>
  <?php
  // Display toast alert if session variables are set
  if (isset($_SESSION['alert_type']) && isset($_SESSION['alert_message'])) {
    $title = isset($_SESSION['alert_title']) ? $_SESSION['alert_title'] : ($_SESSION['alert_type'] == 'success' ? 'Success!' : 'Error!');
    displayToastAlert($_SESSION['alert_type'], $title, $_SESSION['alert_message']);

    // Clear the session variables
    unset($_SESSION['alert_type']);
    unset($_SESSION['alert_title']);
    unset($_SESSION['alert_message']);
  }
  ?>

  <div class="container">
    <div class="card mx-auto">
      <div class="card-header">
        <i class="fas fa-user-tie header-icon"></i>
        <h3>Create Department Head</h3>
        <p class="text-white-50 mb-0">Add a new department head to the system</p>
      </div>

      <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data" autocomplete="off">
          <div class="form-group">
            <label><i class="fas fa-user mr-2"></i>Full Name</label>
            <div class="input-group">
              <input type="text" name="name" required class="form-control" placeholder="Enter full name">
            </div>
          </div>

          <div class="form-group">
            <label><i class="fas fa-envelope mr-2"></i>Email Address</label>
            <div class="input-group">
              <input type="email" name="email" required class="form-control" placeholder="Enter email address">
            </div>
          </div>

          <div class="form-group">
            <label><i class="fas fa-lock mr-2"></i>Password</label>
            <div class="input-group">
              <input type="password" name="password" required class="form-control"
                placeholder="6-10 characters password">
            </div>
            <small class="form-text text-muted">Password must be between 6 and 10 characters long.</small>
          </div>

          <div class="form-group">
            <label><i class="fas fa-building mr-2"></i>Department</label>
            <select name="department_id" class="form-control" required>
              <option value="">Select Department</option>
              <?php while ($dept = $departments->fetch_assoc()) { ?>
                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label><i class="fas fa-signature mr-2"></i>Department Head Signature</label>
            <input type="file" name="signature" required class="form-control-file" accept="image/*">
            <small class="form-text text-muted">Upload a clear image of the signature.</small>
          </div>

          <div class="text-center mt-4">
            <button type="submit" name="create_dept_head" class="btn btn-primary btn-block">
              <i class="fas fa-user-plus mr-2"></i>Create Department Head
            </button>
          </div>
        </form>
      </div>

      <div class="card-footer">
        <a href="admin_dashboard.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    // Initialize toasts after jQuery is loaded
    $(document).ready(function () {
      $('.toast').toast('show');

      // Auto-hide after 5 seconds
      setTimeout(function () {
        $('.toast').toast('hide');
      }, 5000);
    });
  </script>
</body>

</html>