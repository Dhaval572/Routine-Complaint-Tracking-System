<?php
include('../config.php');
if (!isset($_SESSION['user_id'])) {
  header("Location: user_login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$error = null;

// Process account deletion
if (isset($_POST['confirm_delete'])) {
  $password = $_POST['password'];
  
  // Verify password
  $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      // Start transaction to ensure data integrity
      $conn->begin_transaction();
      
      try {
        // First get all complaints by this user
        $get_complaints = $conn->prepare("SELECT id FROM complaints WHERE citizen_id = ?");
        $get_complaints->bind_param("i", $user_id);
        $get_complaints->execute();
        $complaints_result = $get_complaints->get_result();
        
        // Delete activity records for each complaint
        while ($complaint = $complaints_result->fetch_assoc()) {
          $complaint_id = $complaint['id'];
          
          // Delete complaint activity records
          $delete_activity = $conn->prepare("DELETE FROM complaint_activity WHERE complaint_id = ?");
          $delete_activity->bind_param("i", $complaint_id);
          $delete_activity->execute();
          
          // Check if feedback table has a complaint_id column before attempting to delete
          $check_feedback_column = $conn->query("SHOW COLUMNS FROM feedback LIKE 'complaint_id'");
          if ($check_feedback_column->num_rows > 0) {
            // Delete any feedback for this complaint if the column exists
            $delete_feedback = $conn->prepare("DELETE FROM feedback WHERE complaint_id = ?");
            $delete_feedback->bind_param("i", $complaint_id);
            $delete_feedback->execute();
          }
        }
        
        // Now delete all complaints by this user
        $delete_complaints = $conn->prepare("DELETE FROM complaints WHERE citizen_id = ?");
        $delete_complaints->bind_param("i", $user_id);
        $delete_complaints->execute();
        
        // Finally delete the user account
        $delete_user = $conn->prepare("DELETE FROM users WHERE id = ?");
        $delete_user->bind_param("i", $user_id);
        $delete_user->execute();
        
        // If we got here, commit the transaction
        $conn->commit();
        
        // Clear session and set a specific session flag for deletion
        session_start();
        session_unset();
        $_SESSION['show_delete_modal'] = true;
        
        // Redirect to index.php
        header("Location: ../index.php");
        exit;
      } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $conn->rollback();
        $error = "Error deleting account: " . $e->getMessage();
      }
    } else {
      $error = "Incorrect password. Account deletion canceled.";
    }
  }
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delete Account</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="min-vh-100" style="background: linear-gradient(135deg, #ff6b6b 0%, #cc0000 100%);">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
          <div class="card-header bg-danger text-white text-center py-4">
            <div class="d-flex align-items-center justify-content-center">
              <div class="rounded-circle bg-white text-danger d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px;">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
              </div>
              <h4 class="mb-0 font-weight-bold">Delete Account</h4>
            </div>
          </div>

          <div class="card-body p-4">
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-circle mr-2"></i>
              <strong>Warning:</strong> This action cannot be undone. All your data, including complaints and personal information, will be permanently deleted.
            </div>
            
            <?php if ($error): ?>
              <div class="alert alert-danger">
                <i class="fas fa-times-circle mr-2"></i> <?= $error ?>
              </div>
            <?php endif; ?>

            <form method="POST" action="" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action CANNOT be undone.');">
              <div class="form-group">
                <label for="password"><strong>Enter Your Password to Confirm</strong></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-light">
                      <i class="fas fa-lock text-danger"></i>
                    </span>
                  </div>
                  <input type="password" id="password" name="password" required 
                    class="form-control" 
                    placeholder="Enter your password">
                </div>
                <small class="form-text text-muted">You must enter your current password to confirm account deletion.</small>
              </div>
              
              <div class="d-flex justify-content-between mt-4">
                <a href="user_dashboard.php" class="btn btn-secondary">
                  <i class="fas fa-arrow-left mr-2"></i> Cancel
                </a>
                <button type="submit" name="confirm_delete" class="btn btn-danger">
                  <i class="fas fa-trash-alt mr-2"></i> Permanently Delete Account
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>