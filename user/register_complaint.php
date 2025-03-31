<?php
include('../config.php');
if (!isset($_SESSION['user_id'])) {
	header("Location: user_login.php");
	exit;
}

// Fetch departments for complaint department selection
$dept_sql = "SELECT * FROM departments";
$dept_result = $conn->query($dept_sql);

if (isset($_POST['register_complaint'])) {
	$title = $conn->real_escape_string($_POST['title']);
	$description = $conn->real_escape_string($_POST['description']);
	$incident_date = $_POST['incident_date'];
	$incident_time = $_POST['incident_time'];
	$dept_id = $_POST['dept_id'];
	$consent = isset($_POST['consent']) ? 1 : 0;

	// Determine if complaint is against a specific target (officer or dept head)
	$target_option = $_POST['target_option']; // 'none', 'officer', or 'dept_head'
	$target_dept = ($target_option != 'none' && isset($_POST['target_dept'])) ? $_POST['target_dept'] : null;
	$target_id = ($target_option != 'none' && isset($_POST['target_id'])) ? $_POST['target_id'] : null;

	// Handle file upload (if any) – ensure you have created an 'uploads' folder with proper permissions.
	$attachment = null;
	if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
		$uploadDir = "uploads/";
		$filename = time() . "_" . basename($_FILES['attachment']['name']);
		$targetFile = $uploadDir . $filename;
		if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
			$attachment = $filename;
		}
	}

	// By default, assign complaint to the dept head of the selected department (if exists)
	$dept_head_id = null;
	$dh_sql = "SELECT id FROM users WHERE role = 'dept_head' AND department_id = '$dept_id' LIMIT 1";
	$dh_result = $conn->query($dh_sql);
	if ($dh_result && $dh_result->num_rows > 0) {
		$dh_row = $dh_result->fetch_assoc();
		$dept_head_id = $dh_row['id'];
	}

	// Insert complaint – ensure your complaints table has an 'attachment' column if you wish to store files.
	$citizen_id = $_SESSION['user_id'];
	$insert_sql = "INSERT INTO complaints 
        (citizen_id, department_id, title, description, officer_id, dept_head_id, target_id, target_role, created_at)
        VALUES ('$citizen_id', '$dept_id', '$title', '$description', NULL, '$dept_head_id', '$target_id', '$target_option', NOW())";
	if ($conn->query($insert_sql)) {
		// Get the inserted complaint id
		$complaint_id = $conn->insert_id;
		// Log activity: Complaint Registered
		$activity_sql = "INSERT INTO complaint_activity (complaint_id, activity, activity_by) VALUES ('$complaint_id', 'Complaint Registered', '$citizen_id')";
		$conn->query($activity_sql);

		$success = "Complaint registered successfully.";
	} else {
		$error = "Error: " . $conn->error;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Register Complaint</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<!-- jQuery is needed for show/hide behavior and AJAX -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script>
		$(document).ready(function () {
			$("#target_section").hide();
			
			$("input[name='target_option']").change(function () {
				if ($(this).val() == 'none') {
					$("#target_section").slideUp();
				} else {
					$("#target_section").slideDown();
					loadTargets();
				}
			});
			
			$("select[name='target_dept']").change(function(){
				loadTargets();
			});
			
			function loadTargets(){
				var dept_id = $("select[name='target_dept']").val();
				var target_option = $("input[name='target_option']:checked").val();
				if(dept_id != '' && target_option != 'none'){
					$.ajax({
						url: 'fetch_targets.php',
						data: { dept_id: dept_id, target: target_option },
						success: function(data){
							$("select[name='target_id']").html(data);
						}
					});
				} else {
					$("select[name='target_id']").html("<option value=''>Select</option>");
				}
			}
		});
	</script>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<span class="navbar-brand">Register Complaint</span>
		<div class="ml-auto">
			<a href="user_dashboard.php" class="btn btn-outline-light">Dashboard</a>
		</div>
	</nav>
	<div class="container mt-5">
		<h3>Register Your Complaint</h3>
		<?php
		if (isset($success)) {
			echo "<div class='alert alert-success'>$success</div>";
		}
		if (isset($error)) {
			echo "<div class='alert alert-danger'>$error</div>";
		}
		?>
		<form method="POST" action="" enctype="multipart/form-data">
			<div class="form-group">
				<label>Title</label>
				<input type="text" name="title" required class="form-control" placeholder="Enter complaint title">
			</div>
			<div class="form-group">
				<label>Description</label>
				<textarea name="description" required class="form-control" placeholder="Enter detailed description"></textarea>
			</div>
			<div class="form-group">
				<label>Date of Incident</label>
				<input type="date" name="incident_date" required class="form-control">
			</div>
			<div class="form-group">
				<label>Time of Incident</label>
				<input type="time" name="incident_time" required class="form-control">
			</div>
			<div class="form-group">
				<label>Select Department (Complaint About)</label>
				<select name="dept_id" required class="form-control">
					<option value="">Select Department</option>
					<?php while ($dept = $dept_result->fetch_assoc()) { ?>
						<option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="form-group">
				<label>Is this complaint against a specific officer/dept head?</label><br>
				<label class="radio-inline"><input type="radio" name="target_option" value="none" checked> No</label>
				<label class="radio-inline ml-3"><input type="radio" name="target_option" value="officer"> Officer</label>
				<label class="radio-inline ml-3"><input type="radio" name="target_option" value="dept_head"> Dept Head</label>
			</div>
			<div id="target_section">
				<div class="form-group">
					<label>Select Target Department</label>
					<select name="target_dept" class="form-control">
						<option value="">Select Department</option>
						<?php
						// Re-run query for departments
						$dept_result2 = $conn->query("SELECT * FROM departments");
						while ($d = $dept_result2->fetch_assoc()) { ?>
							<option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label>Select <?php echo isset($_POST['target_option']) ? ucfirst($_POST['target_option']) : 'Target'; ?></label>
					<select name="target_id" class="form-control">
						<option value="">Select</option>
						<!-- Options will be loaded here via AJAX -->
					</select>
				</div>
			</div>
			<div class="form-group">
				<label>Attachment (if any)</label>
				<input type="file" name="attachment" class="form-control-file">
			</div>
			<div class="form-group form-check">
				<input type="checkbox" name="consent" class="form-check-input" id="consentCheck" required>
				<label class="form-check-label" for="consentCheck">I give my consent to register this complaint.</label>
			</div>
			<button type="submit" name="register_complaint" class="btn btn-primary">Register Complaint</button>
		</form>
	</div>
</body>
</html>