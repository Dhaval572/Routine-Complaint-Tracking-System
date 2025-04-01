<?php
include('../config.php');
include('../assets/alert_functions.php'); // Updated path to include alert functions

if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

$sql = "SELECT * FROM departments";
$result = $conn->query($sql);
$count = $result->num_rows; // Define count variable here
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Departments</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/view_departments.css">
</head>

<body class="bg-light" style="background-color:rgb(132, 247, 142) !important;">
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

  <div class="container py-3 bg-white rounded shadow-sm">
    <div class="row mb-3">
      <div class="col-md-8 mb-3 mb-md-0">
        <h2 class="text-success font-weight-bold">
          <i class="fas fa-building mr-2"></i>Departments List
        </h2>
      </div>
      <div class="col-md-4 text-md-right text-center">
        <a href="create_department.php" class="btn btn-success rounded-pill px-4 py-2 shadow-sm">
          <i class="fas fa-plus-circle mr-2"></i>Add Department
        </a>
      </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
      <?php displayAlert('success', $_SESSION['success_message'], 'check-circle', true, 'Success!'); ?>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
      <?php displayAlert('error', $_SESSION['error_message'], 'exclamation-circle', true, 'Error!'); ?>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card rounded-lg shadow border-0 mb-4">
      <div
        class="card-header bg-success text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
        <h5 class="mb-2 mb-md-0 font-weight-bold">
          <i class="fas fa-list mr-2"></i>All Departments
        </h5>
        <div
          class="bg-success text-white rounded-pill px-4 py-2 shadow-sm d-flex align-items-center justify-content-center">
          <i class="fas fa-database mr-2"></i>
          <span class="font-weight-bold">Total: <?php echo $count; ?></span>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive-md">
          <table class="table table-hover mb-0">
            <thead class="bg-light">
              <tr class="text-success">
                <th class="text-center" style="width: 60px;">ID</th>
                <th>Department Name</th>
                <th class="d-none d-md-table-cell">Description</th>
                <th class="d-none d-md-table-cell">Created At</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  ?>
                  <tr class="border-left border-success shadow-sm transition-all table-row-hover">
                    <td class="text-center align-middle">
                      <span class="badge badge-success rounded-circle d-flex justify-content-center align-items-center shadow-sm badge-circle badge-hover">
                          <?php echo $row['id']; ?>
                      </span>
                    </td>
                    <td class="font-weight-bold text-success align-middle">
                      <?php echo htmlspecialchars($row['name']); ?>
                      <div class="d-md-none small text-muted mt-1">
                        <?php echo htmlspecialchars(substr($row['description'], 0, 50)) . (strlen($row['description']) > 50 ? '...' : ''); ?>
                      </div>
                      <div class="d-md-none small text-muted">
                        <i class="far fa-calendar-alt mr-1"></i><?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                      </div>
                    </td>
                    <td class="align-middle d-none d-md-table-cell"><?php echo htmlspecialchars($row['description']); ?>
                    </td>
                    <td class="align-middle d-none d-md-table-cell">
                      <span class="badge badge-light rounded-pill px-3 py-2 shadow-sm">
                        <i class="far fa-calendar-alt mr-1"></i>
                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                      </span>
                    </td>
                    <td class="text-center align-middle">
                      <div class="d-flex justify-content-center">
                        <a href="department_actions.php?action=edit&id=<?php echo $row['id']; ?>"
                          class="btn btn-sm btn-outline-success rounded-circle mx-1 shadow-sm d-flex justify-content-center align-items-center"
                          title="Edit" style="width: 35px; height: 35px;">
                          <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" data-toggle="modal" data-target="#deleteModal<?php echo $row['id']; ?>"
                          class="btn btn-sm btn-outline-danger rounded-circle mx-1 shadow-sm d-flex justify-content-center align-items-center"
                          title="Delete" style="width: 35px; height: 35px;">
                          <i class="fas fa-trash-alt"></i>
                        </a>
                      </div>

                      <!-- Delete Confirmation Modal -->
                      <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog"
                        aria-labelledby="deleteModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                          <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-danger text-white">
                              <h5 class="modal-title" id="deleteModalLabel<?php echo $row['id']; ?>">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                              </h5>
                              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body p-4">
                              <div class="text-center mb-4">
                                <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                              </div>
                              <p class="text-center mb-1">Are you sure you want to delete this department?</p>
                              <h5 class="text-center text-danger font-weight-bold mb-3">
                                <?php echo htmlspecialchars($row['name']); ?>
                              </h5>
                              <p class="text-center text-muted small">This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer bg-light justify-content-between">
                              <button type="button" class="btn btn-secondary rounded-pill px-4" data-dismiss="modal">
                                <i class="fas fa-times mr-2"></i>Cancel
                              </button>
                              <a href="department_actions.php?action=delete&id=<?php echo $row['id']; ?>"
                                class="btn btn-danger rounded-pill px-4">
                                <i class="fas fa-trash-alt mr-2"></i>Delete
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <div class="bg-light p-4 rounded shadow-sm d-inline-block">
                      <i class="fas fa-folder-open text-muted" style="font-size: 3rem;"></i>
                      <p class="mt-3 mb-0">No departments found. Create your first department!</p>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="text-center">
      <!-- Back to Dashboard button removed -->
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    $(document).ready(function () {
      // Modified hover effects for department records - removed transformations that could affect modal positioning
      $('tbody tr').hover(
        function () {
          $(this).addClass('bg-white shadow');
          // Removed transform that was causing positioning issues
          $(this).css('transition', 'all 0.3s ease');
          $(this).find('.badge').css('transform', 'scale(1.05)');
          $(this).find('.badge').css('transition', 'transform 0.3s ease');
          $(this).find('.btn').css('transform', 'scale(1.05)');
          $(this).find('.btn').css('transition', 'transform 0.3s ease');
        },
        function () {
          $(this).removeClass('bg-white shadow');
          $(this).find('.badge').css('transform', '');
          $(this).find('.btn').css('transform', '');
        }
      );

      // Simplified modal handling
      $('.modal').on('shown.bs.modal', function () {
        // Ensure modal is properly positioned
        $(this).css('display', 'block');
        $(this).find('.modal-dialog').css({
          'margin': '1.75rem auto'
        });
      });

      // Ensure clean modal dismissal
      $('.modal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
      });
    });
  </script>
</body>

</html>