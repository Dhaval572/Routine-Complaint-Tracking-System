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
	<style>
		:root {
			--primary-color: #1b5e20;
			--secondary-color: #43a047;
			--success-color: #00c853;
			--info-color: #00b0ff;
			--warning-color: #ffab00;
			--danger-color: #ff3d00;

			/* Text Colors */
			--text-primary: #1e293b;
			--text-secondary: #64748b;

			/* Border Radius */
			--radius-base: 1.5rem;
			--radius-lg: 2rem;

			/* Background Colors */
			--bg-gradient-start: #81c784;
			--bg-gradient-end: #a5d6a7;
		}

		body {
			background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
			background-attachment: fixed;
			min-height: 100vh;
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(15px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		@keyframes subtle-pulse {
			0% {
				transform: scale(1);
			}

			50% {
				transform: scale(1.05);
			}

			100% {
				transform: scale(1);
			}
		}

		.animate-fade-in {
			animation: fadeIn 0.6s ease-in forwards;
		}


		.main-card {
			background: rgba(255, 255, 255, 0.95);
			border: 1px solid #e2e8f0;
			border-radius: var(--radius-lg);
			overflow: hidden;
			box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.15);
			padding: 2rem;
			margin-top: 2rem;
			margin-bottom: 2rem;
			border-top: 5px solid var(--primary-color);
		}

		/* Complaint Details Section */
		.complaint-details {
			padding: 2.5rem;
			background: rgba(255, 255, 255, 0.95);
			border-radius: var(--radius-base);
			margin-top: 2rem;
			border: 1px solid rgba(76, 175, 80, 0.3);
			box-shadow: 0 10px 20px -5px rgba(27, 94, 32, 0.15);
		}

		/* Description Section */
		.description-section {
			background: rgba(255, 255, 255, 0.98);
			border: 1px solid rgba(76, 175, 80, 0.3);
			border-radius: var(--radius-base);
			padding: 2.5rem;
			margin-top: 2rem;
			box-shadow: 0 12px 24px -8px rgba(27, 94, 32, 0.15);
			position: relative;
			overflow: hidden;
		}

		.description-section::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 6px;
			height: 100%;
			background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
		}

		.description-section h5 {
			font-size: 1.4rem;
			margin-bottom: 1.5rem;
			color: var(--primary-color);
			border-bottom: 2px dashed rgba(76, 175, 80, 0.2);
			padding-bottom: 1rem;
		}

		.description-section .info-label {
			font-size: 0.85rem;
			text-transform: uppercase;
			letter-spacing: 1px;
			color: var(--secondary-color);
			margin-bottom: 0.5rem;
			font-weight: 600;
		}

		.description-section h4 {
			font-size: 1.5rem;
			font-weight: 700;
			color: var(--text-primary);
			margin-bottom: 1.5rem;
			line-height: 1.4;
		}

		.description-content {
			line-height: 1.8;
			color: var(--text-primary);
			font-size: 1.1rem;
			background: rgba(245, 245, 245, 0.5);
			padding: 1.5rem;
			border-radius: 1rem;
			border-left: 3px solid var(--secondary-color);
			box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.03);
		}

		/* Add this for better spacing between sections */
		.description-section > div:not(:last-child) {
			margin-bottom: 2rem;
		}

		.search-form {
			background: rgba(255, 255, 255, 0.9);
			backdrop-filter: blur(4px);
			border-radius: var(--radius-base);
			padding: 2rem;
			margin-bottom: 2rem;
			border: 1px solid rgba(76, 175, 80, 0.3);
			box-shadow: 0 8px 16px -4px rgba(27, 94, 32, 0.1);
		}

		.form-label {
			color: var(--primary-color);
			font-weight: 600;
			margin-bottom: 0.5rem;
			display: block;
		}

		.form-control {
			border: 2px solid #81c784;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			border-radius: 50px !important;
			padding: 0.75rem 1.5rem;
		}

		.form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 3px rgba(27, 94, 32, 0.2);
		}

		.btn-search {
			background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
			color: white;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
			border-radius: 50px;
			padding: 0.75rem 2.5rem;
			border: none;
			font-weight: 600;
			letter-spacing: 0.5px;
		}

		.btn-search:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 15px -3px rgba(27, 94, 32, 0.4);
		}

		.btn.btn-light {
			border-radius: 50%;
			width: 40px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			background-color: white;
			border: 1px solid #e0e0e0;
			color: var(--primary-color);
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		}

		.btn.btn-light:hover {
			background-color: #f5f5f5;
			box-shadow: 0 6px 8px rgba(0, 0, 0, 0.1);
			color: var(--secondary-color);
		}

		.info-card {
			background: white;
			border: 1px solid #e2e8f0;
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			border-radius: var(--radius-base);
			padding: 1.5rem;
			height: 100%;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
		}

		.info-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
		}

		.info-card:hover .info-icon {
			animation: subtle-pulse 1.5s infinite ease-in-out;
		}

		.info-icon {
			background: transparent !important;
			border-radius: 1rem;
			width: 64px;
			height: 64px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-bottom: 1rem;
			font-size: 1.75rem;
			box-shadow: 0 6px 12px rgba(27, 94, 32, 0.2);
			position: relative;
			overflow: hidden;
			border: 2px solid currentColor;
		}

		.info-icon::after {
			display: none;
		}

		.info-icon i {
			position: relative;
			z-index: 2;
			filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.2));
			transform: scale(1.1);
		}

		.info-icon.bg-primary {
			color: var(--primary-color) !important;
			background-color: transparent !important;
		}

		.info-icon.bg-primary i {
			color: var(--primary-color) !important;
		}

		.info-icon.bg-info {
			color: var(--info-color) !important;
			background-color: transparent !important;
		}

		.info-icon.bg-info i {
			color: var(--info-color) !important;
		}

		.info-icon.bg-success {
			color: var(--success-color) !important;
			background-color: transparent !important;
		}

		.info-icon.bg-success i {
			color: var(--success-color) !important;
		}

		.info-icon.bg-warning {
			color: var(--warning-color) !important;
			background-color: transparent !important;
		}

		.info-icon.bg-warning i {
			color: var(--warning-color) !important;
		}

		.custom-alert {
			border-left: 5px solid;
			background: linear-gradient(90deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
			border-radius: var(--radius-base);
			padding: 1.25rem;
			margin: 1.5rem 0;
			display: flex;
			align-items: flex-start;
			gap: 1rem;
			box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.1);
		}

		.alert-success {
			border-color: var(--success-color);
		}

		.alert-danger {
			border-color: var(--danger-color);
		}

		.alert-success .alert-icon {
			color: var(--success-color);
		}

		.alert-danger .alert-icon {
			color: var(--danger-color);
		}

		.alert-icon {
			border-radius: 0.75rem;
			font-size: 1.75rem;
			flex-shrink: 0;
			filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.2));
		}

		.alert-content {
			flex-grow: 1;
		}

		.alert-heading {
			margin-top: 0;
			margin-bottom: 0.5rem;
			font-size: 1.2rem;
			font-weight: 600;
			color: var(--text-primary);
		}

		/* Background Colors */
		.bg-primary {
			background-color: var(--primary-color) !important;
		}

		.bg-success {
			background-color: var(--success-color) !important;
		}

		.bg-warning {
			background-color: var(--warning-color) !important;
		}

		.bg-danger {
			background-color: var(--danger-color) !important;
		}

		.bg-info {
			background-color: var(--info-color) !important;
		}

		/* Text Colors */
		.text-primary {
			color: var(--primary-color) !important;
		}

		.text-success {
			color: var(--success-color) !important;
		}

		.text-warning {
			color: var(--warning-color) !important;
		}

		.text-danger {
			color: var(--danger-color) !important;
		}

		.text-info {
			color: var(--info-color) !important;
		}

		@media (max-width: 768px) {
			.main-card {
				padding: 1.5rem;
			}

			.search-form {
				padding: 1.5rem;
			}

			.info-card {
				margin-bottom: 1rem;
			}

			.info-icon {
				width: 48px;
				height: 48px;
				font-size: 1.25rem;
			}
		}
	</style>
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
									<input type="text" name="user_name" class="form-control" required
										autocomplete="off">
								</div>
								<div class="col-md-6">
									<label class="form-label">
										<i class="fas fa-hashtag text-primary me-2"></i>Complaint ID
									</label>
									<input type="number" name="complaint_id" class="form-control" required
										autocomplete="off">
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
								<div class="alert-icon">
									<i class="fas fa-check-circle"></i>
								</div>
								<div class="alert-content">
									<h4 class="alert-heading">Success!</h4>
									<p class="mb-0"><?php echo $_SESSION['success_message']; ?></p>
								</div>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
							<?php unset($_SESSION['success_message']); ?>
						<?php endif; ?>

						<?php if (isset($_SESSION['error_message'])): ?>
							<div class="custom-alert alert alert-danger alert-dismissible fade show" role="alert">
								<div class="alert-icon">
									<i class="fas fa-exclamation-triangle"></i>
								</div>
								<div class="alert-content">
									<h4 class="alert-heading">Error Occurred!</h4>
									<p class="mb-0"><?php echo $_SESSION['error_message']; ?></p>
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