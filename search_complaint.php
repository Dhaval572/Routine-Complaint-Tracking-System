<?php
include 'config.php';
include 'assets/alert_functions.php'; // Include alert functions

$complaint = null;

if (isset($_POST['complaint_id']) && isset($_POST['user_name'])) {
	$complaint_id = intval($_POST['complaint_id']);
	$user_name = $_POST['user_name'];

	// First check if user exists
	$user_query = mysqli_query($conn, "SELECT id FROM users WHERE name = '$user_name'");

	if ($user_query && $user_query->num_rows > 0) {
		$user_id = $user_query->fetch_assoc()['id'];

		$sql = "SELECT c.*, d.name as dept_name FROM complaints c
                LEFT JOIN departments d ON c.department_id = d.id 
                WHERE c.id = '$complaint_id' AND c.citizen_id = '$user_id'";

		$result = $conn->query($sql);

		if ($result && $result->num_rows > 0) {
			$complaint = $result->fetch_assoc();
			$_SESSION['success_message'] = "Complaint found successfully!";
		} else {
			$_SESSION['error_message'] = "No complaint found with the provided ID for this user.";
		}
	} else {
		$_SESSION['error_message'] = "User not found. Please check the username.";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Track Complaint | Citizen Portal</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="assets/css/search_complaint.css">
</head>

<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="main-card">
					<div class="d-flex align-items-center mb-4">
						<a href="index.php" class="btn btn-light me-3">
							<i class="fas fa-arrow-left"></i>
						</a>
						<h2 class="mb-0"><i class="fas fa-search me-2 text-primary"></i>Track Your Complaint</h2>
					</div>

					<div class="search-form">
						<form method="post" autocomplete="off">
							<div class="row g-4">
								<div class="col-md-6">
									<label class="form-label">
										<i class="fas fa-user text-primary me-2"></i>User Name
									</label>
									<input type="text" name="user_name" class="form-control" required autocomplete="off">
								</div>
								<div class="col-md-6">
									<label class="form-label">
										<i class="fas fa-hashtag text-primary me-2"></i>Complaint ID
									</label>
									<input type="number" name="complaint_id" class="form-control" required autocomplete="off">
								</div>
							</div>
							<div class="text-center mt-4">
								<button type="submit" class="btn btn-primary btn-search">
									<i class="fas fa-search me-2"></i>Search Complaint
								</button>
							</div>
						</form>
					</div>

					<!-- Update alert sections -->
					<div class="alerts-container">
					    <?php if (isset($_SESSION['success_message'])): ?>
					        <div class="custom-alert alert alert-success alert-dismissible fade show" role="alert">
					            <div class="alert-content">
					                <h4>Success!</h4>
					                <p><?php echo $_SESSION['success_message']; ?></p>
					            </div>
					            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					        </div>
					        <?php unset($_SESSION['success_message']); ?>
					    <?php endif; ?>

					    <?php if (isset($_SESSION['error_message'])): ?>
					        <div class="custom-alert alert alert-danger alert-dismissible fade show" role="alert">
					            <div class="alert-content">
					                <h4>Error!</h4>
					                <p><?php echo $_SESSION['error_message']; ?></p>
					            </div>
					            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					        </div>
					        <?php unset($_SESSION['error_message']); ?>
					    <?php endif; ?>
					</div>

					<?php if ($complaint): ?>
						<div class="complaint-details animate-fade-in">
							<div class="d-flex justify-content-between align-items-center mb-4">
								<div>
									<h3 class="fw-bold mb-2">Complaint Details</h3>
									<p class="text-muted mb-0">Reference ID: #<?php echo $complaint['id']; ?></p>
								</div>
								<?php
								$statusInfo = match (strtolower($complaint['status'])) {
									'registered' => ['class' => 'primary', 'icon' => 'file-check', 'text' => 'Registered'],
									'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending Review'],
									'in progress' => ['class' => 'info', 'icon' => 'spinner fa-spin', 'text' => 'In Progress'],
									'resolved' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Resolved'],
									'rejected' => ['class' => 'danger', 'icon' => 'times-circle', 'text' => 'Rejected'],
									default => ['class' => 'secondary', 'icon' => 'info-circle', 'text' => 'Processing']
								};
								?>
								<div
									class="status-badge bg-<?php echo $statusInfo['class']; ?> bg-opacity-10 text-<?php echo $statusInfo['class']; ?>">
									<i class="fas fa-<?php echo $statusInfo['icon']; ?>"></i>
									<?php echo $statusInfo['text']; ?>
								</div>
							</div>

							<div class="row g-4">
								<div class="col-md-6 col-lg-3">
									<div class="info-card">
										<div class="info-icon bg-primary bg-opacity-10 text-primary">
											<i class="fas fa-hashtag"></i>
										</div>
										<div class="info-label">Complaint ID</div>
										<div class="info-value">#<?php echo $complaint['id']; ?></div>
									</div>
								</div>
								<div class="col-md-6 col-lg-3">
									<div class="info-card">
										<div class="info-icon bg-info bg-opacity-10 text-info">
											<i class="fas fa-building"></i>
										</div>
										<div class="info-label">Department</div>
										<div class="info-value"><?php echo $complaint['dept_name']; ?></div>
									</div>
								</div>
								<div class="col-md-6 col-lg-3">
									<div class="info-card">
										<div class="info-icon bg-success bg-opacity-10 text-success">
											<i class="fas fa-calendar-alt"></i>
										</div>
										<div class="info-label">Filed Date</div>
										<div class="info-value">
											<?php echo date('M j, Y', strtotime($complaint['created_at'])); ?>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-lg-3">
									<div class="info-card">
										<div class="info-icon bg-warning bg-opacity-10 text-warning">
											<i class="fas fa-clock"></i>
										</div>
										<div class="info-label">Last Updated</div>
										<div class="info-value">
											<?php echo date('M j, Y', strtotime($complaint['updated_at'] ?? $complaint['created_at'])); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="description-section">
								<h5 class="fw-bold mb-4">
									<i class="fas fa-file-alt text-primary me-2"></i>
									Complaint Summary
								</h5>
								<div class="mb-4">
									<div class="info-label">Subject</div>
									<h4 class="mb-3"><?php echo htmlspecialchars($complaint['title']); ?></h4>
								</div>
								<div>
									<div class="info-label">Description</div>
									<div class="description-content">
										<?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			// Initialize Bootstrap alerts
			var alertList = document.querySelectorAll('.alert');
			alertList.forEach(function (alert) {
				new bootstrap.Alert(alert);
			});

			// Auto-close alerts after 5 seconds
			setTimeout(function () {
				alertList.forEach(function (alert) {
					var bsAlert = bootstrap.Alert.getInstance(alert);
					if (bsAlert) {
						bsAlert.close();
					}
				});
			}, 5000);

			// Add fade out animation
			alertList.forEach(function (alert) {
				alert.addEventListener('click', function (e) {
					if (e.target.classList.contains('btn-close')) {
						alert.style.transition = 'opacity 0.5s ease-out';
						alert.style.opacity = '0';
						setTimeout(function () {
							alert.remove();
						}, 500);
					}
				});
			});
		});
	</script>
	<!-- Add before closing body tag -->
	<script src="assets/js/search_complaint.js"></script>
</body>

</html>