<?php
include('../config.php');
if (!isset($_SESSION['dept_head_id'])) {
  header("Location: dept_head_login.php");
  exit;
}

// Get complaints assigned to this dept head that have not been assigned to an officer
// and that are not solved.
$dept_head_id = $_SESSION['dept_head_id'];
$sql = "SELECT c.*, d.name as dept_name 
        FROM complaints c 
        LEFT JOIN departments d ON c.department_id = d.id 
        WHERE c.dept_head_id = '$dept_head_id' 
          AND c.officer_id IS NULL 
          AND TRIM(LOWER(c.status)) != 'solved'
        ORDER BY c.created_at DESC";
$result = $conn->query($sql);

// Fetch officers in the same department (for normal complaints)
$department_id = $_SESSION['dept_head_department'];
$officer_sql = "SELECT id, name FROM users WHERE role = 'officer' AND department_id = '$department_id'";
$officers = $conn->query($officer_sql);

// Also fetch dept heads (for referral in complaints against officers)
// Exclude self from referral dropdown.
$dept_heads_query = "SELECT id, name FROM users WHERE role = 'dept_head' AND id != '$dept_head_id'";
$dept_heads = $conn->query($dept_heads_query);

if (isset($_POST['assign'])) {
  $complaint_id = $_POST['complaint_id'];
  $officer_id = $_POST['officer_id'];
  // Update complaint: assign officer and change status to in_progress
  $update_sql = "UPDATE complaints SET officer_id = '$officer_id', status = 'in_progress' WHERE id = '$complaint_id'";
  if ($conn->query($update_sql)) {
    // Log activity: Assigned to Officer
    $activity_sql = "INSERT INTO complaint_activity (complaint_id, activity, activity_by) VALUES ('$complaint_id', 'Assigned to Officer', '$dept_head_id')";
    $conn->query($activity_sql);
    $success = "Complaint #$complaint_id assigned successfully.";
  } else {
    $error = "Error assigning complaint: " . $conn->error;
  }
}

if (isset($_POST['refer_dept_head'])) {
  $complaint_id = $_POST['complaint_id'];
  $new_dept_head_id = $_POST['dept_head_id'];
  // Update complaint: set officer_id remains NULL, change status to referred, and record referral details.
  $update_sql = "UPDATE complaints SET status = 'referred', referred_by = '$dept_head_id', officer_id = NULL WHERE id = '$complaint_id'";
  if ($conn->query($update_sql)) {
    // Log activity: Referred to Dept Head
    $activity_sql = "INSERT INTO complaint_activity (complaint_id, activity, activity_by) VALUES ('$complaint_id', 'Referred to Dept Head (ID: $new_dept_head_id)', '$dept_head_id')";
    $conn->query($activity_sql);
    $success = "Complaint #$complaint_id referred to Dept Head successfully.";
  } else {
    $error = "Error referring complaint: " . $conn->error;
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assign Complaint</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    :root {
      --primary: #4e73df;
      --secondary: #6c757d;
      --success: #1cc88a;
      --info: #36b9cc;
      --warning: #f6c23e;
      --danger: #e74a3b;
      --light: #f8f9fc;
      --dark: #5a5c69;
    }

    body {
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
      font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .navbar {
      background: linear-gradient(to right, #4e73df, #224abe);
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.2rem;
    }

    .btn-dashboard {
      border-radius: 50px;
      padding: 0.5rem 1.5rem;
      font-weight: 600;
      transition: all 0.2s ease;
      background-color: transparent;
      border: 1px solid rgba(255, 255, 255, 0.5);
      color: white;
    }

    .btn-dashboard:hover {
      border-color: rgba(255, 255, 255, 0.9);
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
      color: white;
    }

    .btn-dashboard:active {
      transform: translateY(1px);
    }

    .page-header {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .page-header h3 {
      margin: 0;
      color: var(--dark);
      font-weight: 700;
      display: flex;
      align-items: center;
    }

    .page-header h3 i {
      color: var(--primary);
      margin-right: 0.75rem;
      font-size: 1.75rem;
    }

    .alert {
      border-radius: 0.5rem;
      border: none;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    .alert-success {
      background-color: #e6f8f3;
      color: #169b6b;
      border-left: 4px solid #1cc88a;
    }

    .alert-danger {
      background-color: #fcecea;
      color: #d52a1a;
      border-left: 4px solid #e74a3b;
    }

    .alert-info {
      background-color: #e3f3f6;
      color: #2596a8;
      border-left: 4px solid #36b9cc;
    }

    .card {
      border: none;
      border-radius: 0.5rem;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
    }

    .table-container {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
      overflow: hidden;
    }

    .table {
      margin-bottom: 0;
    }

    .table thead th {
      background-color: #f8f9fc;
      border-top: none;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      color: #4e73df;
      padding: 1rem;
    }

    .table tbody td {
      vertical-align: middle;
      padding: 1rem;
      border-color: #f0f0f0;
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: #fcfcfc;
    }

    .badge {
      font-weight: 600;
      padding: 0.35em 0.65em;
      border-radius: 10rem;
    }

    .form-control {
      border-radius: 0.5rem;
      padding: 0.75rem 1rem;
      border: 1px solid #d1d3e2;
      font-size: 0.9rem;
      transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
      border-color: #bac8f3;
      box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn {
      border-radius: 0.5rem;
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-primary {
      background-color: #4e73df;
      border-color: #4e73df;
    }

    .btn-primary:hover {
      background-color: #2e59d9;
      border-color: #2653d4;
      transform: translateY(-2px);
    }

    .btn-warning {
      background-color: #f6c23e;
      border-color: #f6c23e;
    }

    .btn-warning:hover {
      background-color: #f4b619;
      border-color: #f4b30d;
      transform: translateY(-2px);
    }

    .complaint-title {
      font-weight: 600;
      color: #4e73df;
    }

    .complaint-id {
      font-weight: 700;
      background-color: #4e73df;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
    }

    .complaint-date {
      color: #6e707e;
      font-size: 0.85rem;
    }

    .complaint-dept {
      font-weight: 600;
      color: #5a5c69;
    }

    .action-container {
      background-color: #f8f9fc;
      border-radius: 0.5rem;
      padding: 1rem;
    }

    .description-cell {
      max-width: 300px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .description-cell:hover {
      white-space: normal;
      overflow: visible;
    }

    @media (max-width: 768px) {
      .table-responsive {
        border-radius: 0.5rem;
      }

      .action-container {
        padding: 0.75rem;
      }
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <span class="navbar-brand">
        <i class="fas fa-tasks mr-2"></i>Assign Complaint
      </span>
      <div class="ml-auto">
        <a href="dept_head_dashboard.php" class="btn btn-dashboard">
          <i class="fas fa-home mr-2"></i>Dashboard
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="page-header">
      <h3><i class="fas fa-clipboard-list"></i>Complaints Pending Assignment</h3>
    </div>

    <?php
    if (isset($success)) {
      echo "<div class='alert alert-success d-flex align-items-center mb-4'>
              <i class='fas fa-check-circle mr-3' style='font-size: 1.5rem;'></i>
              <div>$success</div>
            </div>";
    }
    if (isset($error)) {
      echo "<div class='alert alert-danger d-flex align-items-center mb-4'>
              <i class='fas fa-exclamation-circle mr-3' style='font-size: 1.5rem;'></i>
              <div>$error</div>
            </div>";
    }
    ?>

    <?php if ($result && $result->num_rows > 0) { ?>
      <div class="table-container">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th><i class="fas fa-hashtag mr-1"></i>ID</th>
                <th><i class="fas fa-heading mr-1"></i>Title</th>
                <th><i class="fas fa-building mr-1"></i>Department</th>
                <th><i class="fas fa-align-left mr-1"></i>Description</th>
                <th><i class="fas fa-calendar-alt mr-1"></i>Registered</th>
                <th><i class="fas fa-cogs mr-1"></i>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                  <td><span class="complaint-id"><?php echo $row['id']; ?></span></td>
                  <td class="complaint-title"><?php echo htmlspecialchars($row['title']); ?></td>
                  <td class="complaint-dept"><?php echo htmlspecialchars($row['dept_name']); ?></td>
                  <td class="description-cell"><?php echo htmlspecialchars($row['description']); ?></td>
                  <td class="complaint-date">
                    <i class="far fa-clock mr-1"></i>
                    <?php echo date('M d, Y g:i A', strtotime($row['created_at'])); ?>
                  </td>
                  <td>
                    <div class="action-container">
                      <?php if ($row['target_role'] != 'officer') { ?>
                        <!-- Normal complaint: allow assignment to an officer -->
                        <form method="POST" action="">
                          <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                          <div class="form-group mb-2">
                            <select name="officer_id" class="form-control" required>
                              <option value="">Select Officer</option>
                              <?php while ($officer = $officers->fetch_assoc()) { ?>
                                <option value="<?php echo $officer['id']; ?>"><?php echo htmlspecialchars($officer['name']); ?>
                                </option>
                              <?php } ?>
                            </select>
                          </div>
                          <button type="submit" name="assign" class="btn btn-primary btn-block">
                            <i class="fas fa-user-check mr-2"></i>Assign Officer
                          </button>
                        </form>
                        <?php
                        // Reset officers result pointer for next row
                        $officers->data_seek(0);
                        ?>
                      <?php } else { ?>
                        <!-- Complaint against an officer: allow dept head to solve directly or refer to another dept head -->
                        <a href="solve_complaint_dept_head.php?complaint_id=<?php echo $row['id']; ?>"
                          class="btn btn-primary btn-block mb-3">
                          <i class="fas fa-check-circle mr-2"></i>Solve Directly
                        </a>
                        <div class="text-center mb-2">
                          <span class="badge badge-light">OR</span>
                        </div>
                        <form method="POST" action="">
                          <input type="hidden" name="complaint_id" value="<?php echo $row['id']; ?>">
                          <?php
                          // Exclude self from referral dropdown:
                          $dept_heads_query = "SELECT id, name FROM users WHERE role = 'dept_head' AND id != '$dept_head_id'";
                          $dept_heads = $conn->query($dept_heads_query);
                          ?>
                          <div class="form-group mb-2">
                            <select name="dept_head_id" class="form-control" required>
                              <option value="">Select Dept Head</option>
                              <?php while ($dh = $dept_heads->fetch_assoc()) { ?>
                                <option value="<?php echo $dh['id']; ?>"><?php echo htmlspecialchars($dh['name']); ?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <button type="submit" name="refer_dept_head" class="btn btn-warning btn-block">
                            <i class="fas fa-exchange-alt mr-2"></i>Refer to Dept Head
                          </button>
                        </form>
                        <?php
                        // Reset dept_heads result pointer for next row
                        $dept_heads->data_seek(0);
                        ?>
                      <?php } ?>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php } else { ?>
      <div class="alert alert-info d-flex align-items-center">
        <i class="fas fa-info-circle mr-3" style="font-size: 1.5rem;"></i>
        <div>No complaints pending assignment.</div>
      </div>
    <?php } ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>