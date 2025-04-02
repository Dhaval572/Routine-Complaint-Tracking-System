<?php
include 'config.php';
include 'assets/alert_functions.php'; // Include alert functions

$complaint = null;
$error_type = null;

if (isset($_POST['complaint_id']) && isset($_POST['user_name'])) {
	$complaint_id = intval($_POST['complaint_id']);
	$user_name = $_POST['user_name'];

	// Check if both fields are empty
	if (empty($_POST['user_name']) && empty($_POST['complaint_id'])) {
		$error_type = "both_empty";
		$_SESSION['error_message'] = "Please enter both username and complaint ID to search.";
	} else {
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
				$error_type = "id_not_found";
				$_SESSION['error_message'] = "No complaint found with ID #$complaint_id for user '$user_name'.";
			}
		} else {
			$error_type = "user_not_found";
			$_SESSION['error_message'] = "User '$user_name' not found in our system. Please check the username.";
		}
	}
	
	// Store search values in session to repopulate form
	$_SESSION['last_search_user'] = $user_name;
	$_SESSION['last_search_id'] = $complaint_id;
}

// Get values from session if they exist
$last_user = $_SESSION['last_search_user'] ?? '';
$last_id = $_SESSION['last_search_id'] ?? '';
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
						<a href="index.php" class="text-white me-3 text-decoration-none">
							<i class="fas fa-arrow-left"></i>
						</a>
						<i class="fas fa-search me-2"></i> Track Your Complaint
					</div>
					<div class="card-body p-4">
						<!-- Form section -->
						<form method="post" class="search-form">
						    <div class="row">
						        <div class="col-md-6 mb-3">
						            <label class="form-label">User Name</label>
						            <div class="input-group">
						                <span class="input-group-text">
						                    <i class="fas fa-user"></i>
						                </span>
						                <input type="text" name="user_name" class="form-control"
						                    placeholder="Enter your username" value="<?php echo htmlspecialchars($last_user); ?>" required>
						            </div>
						        </div>
						        <div class="col-md-6 mb-3">
						            <label class="form-label">Complaint ID</label>
						            <div class="input-group">
						                <span class="input-group-text">
						                    <i class="fas fa-hashtag"></i>
						                </span>
						                <input type="number" name="complaint_id" class="form-control"
						                    placeholder="Enter complaint ID" value="<?php echo htmlspecialchars($last_id); ?>" required>
						            </div>
						        </div>
						    </div>
						    <div class="text-center mt-3">
						        <button type="submit" class="btn btn-primary btn-lg px-5">
						            <i class="fas fa-search me-2"></i> Search Complaint
						        </button>
						    </div>
						</form>

						<!-- Custom Error Alerts -->
						<?php if (isset($_SESSION['error_message'])): ?>
							<?php if ($error_type == "user_not_found"): ?>
								<div class="custom-alert user-not-found row g-0">
									<div class="col-md-3 alert-icon-column">
										<div class="alert-icon">
											<i class="fas fa-user-times fa-2x"></i>
										</div>
									</div>
									<div class="col-md-9 alert-content">
										<div class="alert-title">User Not Found</div>
										<div class="alert-message"><?php echo $_SESSION['error_message']; ?></div>
										<div class="d-flex gap-2">
											<a href="user_register.php" class="btn btn-outline-danger">
												<i class="fas fa-user-plus me-1"></i> Register Now
											</a>
											<button class="btn btn-light" onclick="document.querySelector('input[name=user_name]').focus()">
												<i class="fas fa-redo-alt me-1"></i> Try Again
											</button>
										</div>
									</div>
								</div>
							<?php elseif ($error_type == "id_not_found"): ?>
								<div class="custom-alert id-not-found row g-0">
									<div class="col-md-3 alert-icon-column">
										<div class="alert-icon">
											<i class="fas fa-exclamation-triangle fa-2x"></i>
										</div>
									</div>
									<div class="col-md-9 alert-content">
										<div class="alert-title">Complaint Not Found</div>
										<div class="alert-message"><?php echo $_SESSION['error_message']; ?></div>
										<div class="d-flex gap-2">
											<a href="register_complaint.php" class="btn btn-outline-warning">
												<i class="fas fa-file-alt me-1"></i> Register Complaint
											</a>
											<button class="btn btn-light" onclick="document.querySelector('input[name=complaint_id]').focus()">
												<i class="fas fa-redo-alt me-1"></i> Try Again
											</button>
										</div>
									</div>
								</div>
							<?php elseif ($error_type == "both_empty"): ?>
								<div class="custom-alert both-empty row g-0">
									<div class="col-md-3 alert-icon-column">
										<div class="alert-icon">
											<i class="fas fa-info-circle fa-2x"></i>
										</div>
									</div>
									<div class="col-md-9 alert-content">
										<div class="alert-title">Information Required</div>
										<div class="alert-message"><?php echo $_SESSION['error_message']; ?></div>
										<button class="btn btn-outline-primary" onclick="document.querySelector('input[name=user_name]').focus()">
											<i class="fas fa-pen me-1"></i> Fill Form
										</button>
									</div>
								</div>
							<?php endif; ?>
							<?php unset($_SESSION['error_message']); ?>
						<?php endif; ?>

						<!-- Success Alert -->
						<?php if (isset($_SESSION['success_message'])): ?>
							<div class="custom-alert success-alert row g-0">
								<div class="col-md-3 alert-icon-column">
									<div class="alert-icon">
										<i class="fas fa-check-circle fa-2x"></i>
									</div>
								</div>
								<div class="col-md-9 alert-content">
									<div class="alert-title">Success!</div>
									<div class="alert-message"><?php echo $_SESSION['success_message']; ?></div>
									<button class="btn btn-sm btn-success" onclick="window.print()">
										<i class="fas fa-print me-1"></i> Print Details
									</button>
								</div>
							</div>
							<?php unset($_SESSION['success_message']); ?>
						<?php endif; ?>

						<?php if ($complaint): 
							// Determine status classes and icons
							$statusClass = match (strtolower($complaint['status'])) {
								'pending' => 'status-badge-pending',
								'in progress' => 'status-badge-progress',
								'resolved' => 'status-badge-resolved',
								'rejected' => 'status-badge-rejected',
								default => ''
							};
							
							$statusIcon = match (strtolower($complaint['status'])) {
								'pending' => 'fa-clock',
								'in progress' => 'fa-spinner fa-spin',
								'resolved' => 'fa-check-circle',
								'rejected' => 'fa-times-circle',
								default => 'fa-question-circle'
							};
							
							$statusBgClass = match (strtolower($complaint['status'])) {
								'pending' => 'bg-warning bg-opacity-10',
								'in progress' => 'bg-info bg-opacity-10',
								'resolved' => 'bg-success bg-opacity-10',
								'rejected' => 'bg-danger bg-opacity-10',
								default => 'bg-secondary bg-opacity-10'
							};
						?>
							<div class="complaint-card">
								<div class="complaint-header d-flex justify-content-between align-items-center">
									<h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i> Complaint Details</h4>
									<span class="status-badge <?php echo $statusClass; ?>">
										<i class="fas <?php echo $statusIcon; ?>"></i>
										<?php echo ucfirst($complaint['status']); ?>
									</span>
								</div>
								<div class="card-body p-4">
									<div class="row mb-4">
										<div class="col-md-6">
											<div class="d-flex align-items-center mb-3">
												<div class="info-icon">
													<i class="fas fa-hashtag"></i>
												</div>
												<div>
													<small class="text-muted d-block">Complaint ID</small>
													<span class="fw-bold fs-5">#<?php echo $complaint['id']; ?></span>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="d-flex align-items-center mb-3">
												<div class="info-icon">
													<i class="fas fa-calendar-alt"></i>
												</div>
												<div>
													<small class="text-muted d-block">Date Filed</small>
													<span class="fw-bold"><?php echo date('F j, Y', strtotime($complaint['created_at'])); ?></span>
												</div>
											</div>
										</div>
									</div>
									
									<div class="row mb-4">
										<div class="col-md-6">
											<div class="d-flex align-items-center mb-3">
												<div class="info-icon">
													<i class="fas fa-building"></i>
												</div>
												<div>
													<small class="text-muted d-block">Department</small>
													<span class="fw-bold"><?php echo htmlspecialchars($complaint['dept_name']); ?></span>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="d-flex align-items-center mb-3">
												<div class="info-icon">
													<i class="fas fa-clock"></i>
												</div>
												<div>
													<small class="text-muted d-block">Last Updated</small>
													<span class="fw-bold">
														<?php echo date('F j, Y', strtotime($complaint['updated_at'] ?? $complaint['created_at'])); ?>
													</span>
												</div>
											</div>
										</div>
									</div>
									
									<div class="mt-4">
										<div class="complaint-title-container p-3 rounded mb-3 <?php echo $statusBgClass; ?>">
											<h5 class="mb-0"><?php echo htmlspecialchars($complaint['title']); ?></h5>
										</div>
										<div class="complaint-description">
											<?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
										</div>
									</div>
									
									<!-- Timeline with improved design -->
									<div class="timeline mt-5">
										<h5 class="border-bottom pb-2 mb-4">Complaint Timeline</h5>
										
										<div class="timeline-item">
											<div class="timeline-dot active"></div>
											<div class="timeline-content">
												<div class="fw-bold">Complaint Filed</div>
												<div class="timeline-date"><?php echo date('F j, Y', strtotime($complaint['created_at'])); ?></div>
											</div>
										</div>
										
										<?php if (strtolower($complaint['status']) != 'pending'): ?>
										<div class="timeline-item">
											<div class="timeline-dot active"></div>
											<div class="timeline-content">
												<div class="fw-bold">Processing Started</div>
												<div class="timeline-date">Complaint is being processed by department</div>
											</div>
										</div>
										<?php endif; ?>
										
										<?php if (strtolower($complaint['status']) == 'resolved' || strtolower($complaint['status']) == 'rejected'): ?>
										<div class="timeline-item">
											<div class="timeline-dot active"></div>
											<div class="timeline-content">
												<div class="fw-bold"><?php echo ucfirst($complaint['status']); ?></div>
												<div class="timeline-date"><?php echo date('F j, Y', strtotime($complaint['updated_at'] ?? $complaint['created_at'])); ?></div>
											</div>
										</div>
										<?php endif; ?>
										
										<?php if (strtolower($complaint['status']) != 'resolved' && strtolower($complaint['status']) != 'rejected'): ?>
										<div class="timeline-item">
											<div class="timeline-dot"></div>
											<div class="timeline-content">
												<div class="fw-bold text-muted">Resolution Pending</div>
												<div class="timeline-date">Waiting for final resolution</div>
											</div>
										</div>
										<?php endif; ?>
									</div>
									
									<div class="d-flex justify-content-end mt-4 action-buttons">
										<button class="btn btn-outline-primary me-2" onclick="window.print()">
											<i class="fas fa-print me-1"></i> Print Details
										</button>
										<?php if (strtolower($complaint['status']) == 'resolved'): ?>
										<a href="feedback.php?complaint_id=<?php echo $complaint['id']; ?>" class="btn btn-outline-success">
											<i class="fas fa-star me-1"></i> Give Feedback
										</a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Bootstrap 5 JS Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>