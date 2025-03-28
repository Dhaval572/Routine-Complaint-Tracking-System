<?php
include('config.php');
if(!isset($_SESSION['admin_id'])){
   header("Location: admin_login.php");
   exit;
}

// Fetch departments for the dropdown
$departments = $conn->query("SELECT * FROM departments");

if(isset($_POST['create_dept_head'])){
  $name          = $_POST['name'];
  $email         = $_POST['email'];
  $password      = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $department_id = $_POST['department_id'];
  $role          = 'dept_head';

  $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssi", $name, $email, $password, $role, $department_id);
  if($stmt->execute()){
      $success = "Department Head created successfully";
  } else {
      $error = "Error creating Department Head";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Department Head</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h2>Create Department Head</h2>
  <?php if(isset($success)){ echo "<div class='alert alert-success'>$success</div>"; } ?>
  <?php if(isset($error)){ echo "<div class='alert alert-danger'>$error</div>"; } ?>
  <form method="POST" action="">
    <div class="form-group">
      <label>Name</label>
      <input type="text" name="name" required class="form-control" placeholder="Enter name">
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required class="form-control" placeholder="Enter email">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" required class="form-control" placeholder="Enter password">
    </div>
    <div class="form-group">
      <label>Select Department</label>
      <select name="department_id" class="form-control" required>
        <option value="">Select Department</option>
        <?php while($dept = $departments->fetch_assoc()){ ?>
           <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
        <?php } ?>
      </select>
    </div>
    <button type="submit" name="create_dept_head" class="btn btn-primary">Create Department Head</button>
  </form>
  <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
