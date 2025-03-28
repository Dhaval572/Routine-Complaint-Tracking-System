<?php
include('../config.php');
if (!isset($_SESSION['user_id'])) {
	header("Location: user_login.php");
	exit;
}

$complaint = null;
if (isset($_GET['complaint_id'])) {
	$complaint_id = intval($_GET['complaint_id']);
	$sql = "SELECT c.*, d.name as dept_name FROM complaints c 
            LEFT JOIN departments d ON c.department_id = d.id 
            WHERE c.id = '$complaint_id' AND c.citizen_id = '" . $_SESSION['user_id'] . "'";
	$result = $conn->query($sql);
	if ($result && $result->num_rows > 0) {
		$complaint = $result->fetch_assoc();
	} else {
		$error = "Complaint not found.";
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Track Complaint</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<!-- Include jQuery and Bootstrap JS for modal functionality -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script>
		function loadActivity(complaint_id) {
			$.ajax({
				url: 'fetch_activity.php',
				type: 'GET',
				data: { complaint_id: complaint_id },
				success: function (data) {
					$("#activityContent").html(data);
					$("#activityModal").modal('show');
				}
			});
		}
	</script>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<span class="navbar-brand">Track Complaint</span>
		<div class="ml-auto">
			<a href="user_dashboard.php" class="btn btn-outline-light">Dashboard</a>
		</div>
	</nav>
	<div class="container mt-5">
		<h3>Track Your Complaint</h3>
		<form method="GET" action="">
			<div class="form-group">
				<label>Enter Complaint ID</label>
				<input type="number" name="complaint_id" required class="form-control" placeholder="Complaint ID">
			</div>
			<button type="submit" class="btn btn-primary">Track</button>
		</form>
		<?php if (isset($error)) {
			echo "<div class='alert alert-danger mt-3'>$error</div>";
		} ?>
		<?php if ($complaint) { ?>
			<div class="mt-5">
				<h4>Complaint Details</h4>
				<p><strong>Title:</strong> <?php echo htmlspecialchars($complaint['title']); ?></p>
				<p><strong>Description:</strong> <?php echo htmlspecialchars($complaint['description']); ?></p>
				<p><strong>Department:</strong> <?php echo htmlspecialchars($complaint['dept_name']); ?></p>
				<p><strong>Status:</strong> <?php echo ucfirst($complaint['status']); ?></p>
				<p><strong>Registered At:</strong> <?php echo $complaint['created_at']; ?></p>
				<button class="btn btn-info" onclick="loadActivity(<?php echo $complaint['id']; ?>)">View Activity
					Timeline</button>
			</div>
		<?php } ?>
	</div>

	<!-- Activity Modal -->
	<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><strong>Complaint Activity Timeline</strong></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="activityContent">
					<!-- AJAX loaded content -->
				</div>
			</div>
		</div>
	</div>
</body>

</html>