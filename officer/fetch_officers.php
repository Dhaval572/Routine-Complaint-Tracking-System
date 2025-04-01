<?php
include('../config.php');

if (isset($_GET['dept_id'])) {
  $dept_id = $_GET['dept_id'];
  // Fetch officers in the selected department
  $sql = "SELECT id, name FROM users WHERE role = 'officer' AND department_id = '$dept_id'";
  $result = $conn->query($sql);
  
  $options = "<option value=''>Select Officer</option>";
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $options .= "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
    }
  }
  echo $options;
}
?>
