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

	<!-- Bootstrap 5 CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
		rel="stylesheet">
	<!-- Custom CSS -->
	<link rel="stylesheet" href="assets/css/search_complaint.css">
</head>

<body>
	<div class="floating-blobs">
		<div class="blob"></div>
		<div class="blob"></div>
		<div class="blob"></div>
	</div>

	<div class="container main-container">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="card card-custom">
					<?php if ($complaint) {
						// Determine status for styling
						$status = strtolower($complaint['status']);
						$headerClass = "card-header card-header-{$status}";
					} else {
						$headerClass = "card-header";
					} ?>

					<div class="<?php echo $headerClass; ?>">
						<a href="index.php" class="text-white me-3">
							<i class="fas fa-arrow-left"></i>
						</a>
						<i class="fas fa-search me-2"></i> Track Your Complaint
					</div>
					<div class="card-body p-4">
						<form method="post" class="search-form">
							<div class="row">
								<div class="col-md-6 mb-3">
									<label class="form-label">User Name</label>
									<div class="input-group">
										<span class="input-group-text">
											<i class="fas fa-user"></i>
										</span>
										<input type="text" name="user_name" class="form-control"
											placeholder="Enter your username" required>
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<label class="form-label">Complaint ID</label>
									<div class="input-group">
										<span class="input-group-text">
											<i class="fas fa-hashtag"></i>
										</span>
										<input type="number" name="complaint_id" class="form-control"
											placeholder="Enter complaint ID" required>
									</div>
								</div>
							</div>
							<div class="text-center mt-3">
								<button type="submit" class="btn btn-primary btn-lg px-5">
									<i class="fas fa-search me-2"></i> Search Complaint
								</button>
							</div>
						</form>

						<!-- Add alerts here -->
						<?php if (isset($_SESSION['success_message'])): ?>
							<div class="mt-4">
								<?php displayAlert('success', $_SESSION['success_message'], 'check-circle', true, 'Success!'); ?>
								<?php unset($_SESSION['success_message']); ?>
							</div>
						<?php endif; ?>

						<?php if (isset($_SESSION['error_message'])): ?>
							<div class="mt-4">
								<?php displayAlert('error', $_SESSION['error_message'], 'exclamation-circle', true, 'Error!'); ?>
								<?php unset($_SESSION['error_message']); ?>
							</div>
						<?php endif; ?>

						<?php if ($complaint) {
							$statusClass = match (strtolower($complaint['status'])) {
								'pending' => 'text-warning',
								'in progress' => 'text-info',
								'resolved' => 'text-success',
								'rejected' => 'text-danger',
								default => 'text-secondary'
							};
							?>
							<div class="complaint-details mt-4">
								<h4 class="mb-4">Complaint Details</h4>
								<div class="row">
									<div class="col-md-6">
										<p><strong>Complaint ID:</strong> #<?php echo $complaint['id']; ?></p>
										<p><strong>Department:</strong> <?php echo $complaint['dept_name']; ?></p>
										<p><strong>Subject:</strong> <?php echo $complaint['title']; ?></p>
									</div>
									<div class="col-md-6">
										<p><strong>Date Filed:</strong>
											<?php echo date('F j, Y', strtotime($complaint['created_at'])); ?></p>
										<p><strong>Status:</strong> <span
												class="<?php echo $statusClass; ?>"><?php echo $complaint['status']; ?></span>
										</p>
										<p><strong>Last Updated:</strong>
											<?php echo date('F j, Y', strtotime($complaint['updated_at'] ?? $complaint['created_at'])); ?>
										</p>
									</div>
								</div>
								<div class="mt-3">
									<p><strong>Description:</strong></p>
									<p class="complaint-description">
										<?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Bootstrap 5 JS Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- Remove the problematic script that references non-existent elements -->
</body>

</html>