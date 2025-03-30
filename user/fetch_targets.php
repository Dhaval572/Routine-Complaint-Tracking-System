<?php
include '../config.php';

if (isset($_GET['dept_id']) && isset($_GET['target'])) {
	$dept_id = $_GET['dept_id'];
	$target = $_GET['target']; // Expected values: 'officer' or 'dept_head'

	$sql = "SELECT id, name FROM users WHERE role = '$target' AND department_id = '$dept_id'";
	$result = $conn->query($sql);

	$options = "<option value=''>Select</option>";
	while ($row = $result->fetch_assoc()) {
		$options .= "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
	}
	echo $options;
}
