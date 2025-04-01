<?php
include('../config.php');
if (!isset($_SESSION['admin_id'])) {
  header("Location: admin_login.php");
  exit;
}

if (isset($_POST['create_dept'])) {
  $name = $_POST['name'];
  $description = $_POST['description'];

  $stmt = $conn->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
  $stmt->bind_param("ss", $name, $description);
  if ($stmt->execute()) {
    $success = "Department created successfully";
    $show_modal = true; // Flag to show the modal
  } else {
    $error = "Error creating department";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Department</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background-color: rgb(144, 238, 144);">
  <nav class="navbar navbar-dark bg-success shadow-sm">
    <div class="container">
      <span class="navbar-brand font-weight-bold">
        <i class="fas fa-building mr-2"></i>Create Department
      </span>
      <a href="admin_dashboard.php" class="btn btn-danger rounded-pill px-3 py-2">
        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
      </a>
    </div>
  </nav>

  <div class="container-fluid py-3">
    <div class="row justify-content-center">
      <div class="col-xl-7 col-lg-8 col-md-10 col-sm-12">
        <div class="card border-0 shadow-lg" style="border-radius: 8px;">
          <div class="card-header bg-danger text-white p-3"
            style="border-top-left-radius: 8px; border-top-right-radius: 8px;">
            <h3 class="mb-0 h4">
              <i class="fas fa-plus-circle mr-2"></i>Create New Department
            </h3>
          </div>
          <div class="card-body p-3 p-sm-4">
            <?php if (isset($success)) { ?>
              <div class="alert alert-success alert-dismissible fade show rounded-pill py-2" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php } ?>

            <?php if (isset($error)) { ?>
              <div class="alert alert-danger alert-dismissible fade show rounded-pill py-2" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <?php } ?>

            <form method="POST" action="" autocomplete="off">
              <div class="form-group mb-3">
                <label for="name" class="font-weight-bold">
                  <i class="fas fa-tag mr-2 text-danger"></i>Department Name
                </label>
                <div class="input-group input-group-lg">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-danger text-white border-0">
                      <i class="fas fa-building"></i>
                    </span>
                  </div>
                  <input type="text" id="name" name="name" required class="form-control shadow-sm rounded-right"
                    placeholder="Enter department name">
                </div>
              </div>

              <div class="form-group">
                <label for="description" class="font-weight-bold">
                  <i class="fas fa-align-left mr-2 text-danger"></i>Description
                </label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-danger text-white border-0 h-100">
                      <i class="fas fa-quote-left"></i>
                    </span>
                  </div>
                  <textarea id="description" name="description" class="form-control shadow-sm" rows="4"
                    placeholder="Enter department description"></textarea>
                </div>
              </div>

              <div class="text-center mt-4">
                <button type="submit" name="create_dept" class="btn btn-danger btn-lg px-5 rounded-pill">
                  <i class="fas fa-save mr-2"></i>Create Department
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Success Modal -->
  <?php if (isset($show_modal) && $show_modal) { ?>
  <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
        <div class="modal-header bg-success text-white py-3" style="border-bottom: none;">
          <h5 class="modal-title font-weight-bold" id="successModalLabel">
            <i class="fas fa-check-circle mr-2 pulse-animation"></i>Success!
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <div class="text-center py-4 px-4 bg-light">
            <div class="success-icon-container mb-3">
              <i class="fas fa-building text-success" style="font-size: 5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));"></i>
              <div class="success-circle"></div>
            </div>
            <h4 class="font-weight-bold text-success">Department Created Successfully!</h4>
            <p class="text-muted mb-0">What would you like to do next?</p>
          </div>
          <div class="p-4">
            <div class="row">
              <div class="col-sm-6 mb-3 mb-sm-0">
                <a href="create_department.php" class="btn btn-outline-success btn-lg btn-block rounded-pill shadow-sm hover-effect">
                  <i class="fas fa-plus-circle mr-2"></i>Add Another
                </a>
              </div>
              <div class="col-sm-6">
                <a href="admin_dashboard.php" class="btn btn-success btn-lg btn-block rounded-pill shadow-sm hover-effect">
                  <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    .pulse-animation {
      animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }
    
    .success-icon-container {
      position: relative;
      display: inline-block;
    }
    
    .success-circle {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: rgba(40, 167, 69, 0.1);
      animation: ripple 2s infinite ease-in-out;
    }
    
    @keyframes ripple {
      0% { transform: translate(-50%, -50%) scale(0.8); opacity: 1; }
      100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; }
    }
    
    .hover-effect {
      transition: all 0.3s ease;
    }
    
    .hover-effect:hover {
      transform: translateY(-3px);
    }
    
    @media (max-width: 576px) {
      .modal-dialog {
        margin: 0.5rem;
      }
    }
  </style>
  
  <script>
    $(document).ready(function() {
      $('#successModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
      });
    });
  </script>
  <?php } ?>
</body>

</html>