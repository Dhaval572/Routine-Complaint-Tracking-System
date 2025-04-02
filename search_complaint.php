<?php
include 'config.php';

$complaint = null;

if (isset($_POST['complaint_id'])) {

	$complaint_id = intval($_POST['complaint_id']);

	$user_name = $_POST['user_name'] ?? null; // Get user name from session or request
	$user_id = mysqli_query($conn, "SELECT id FROM users WHERE name = '$user_name'")->fetch_assoc()['id'];

	$sql = "SELECT c.*, d.name as dept_name FROM complaints c
            LEFT JOIN departments d ON c.department_id = d.id 
            WHERE c.id = '$complaint_id' AND c.citizen_id = '" . $user_id . "'";

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
						<form method="post" class="mb-4">
							<div class="mb-3">
								<label for="complaintId" class="form-label">Enter Complaint ID</label>
								<div class="input-group">
									<span class="input-group-text bg-light border-end-0">
										<i class="fas fa-hashtag 
										<?php
										$textClass = 'text-primary';
										if ($complaint) {
											if ($status == 'resolved' || $status == 'solved') {
												$textClass = 'text-success';
											} elseif ($status == 'pending') {
												$textClass = 'text-warning';
											} elseif ($status == 'processing') {
												$textClass = 'text-info';
											} elseif ($status == 'rejected') {
												$textClass = 'text-danger';
											}
										}
										echo $textClass;
										?>"></i>
									</span>
									<input type="text" name="user_name" class="form-control border-start-0" placeholder="User Name" required>
									<input type="number" name="complaint_id" id="complaintId" class="form-control border-start-0" placeholder="Complain ID" required>
								</div>
							</div>

							<?php if ($complaint) {
								// Determine button class based on status
								$btnClass = "";
								if ($status == 'resolved' || $status == 'solved') {
									$btnClass = "btn-success";
								} else if ($status == 'pending') {
									$btnClass = "btn-warning";
								} else if ($status == 'processing') {
									$btnClass = "btn-info";
								} else if ($status == 'rejected') {
									$btnClass = "btn-danger";
								} else {
									$btnClass = "btn-primary";
								}
							?>
								<button type="submit" class="btn <?php echo $btnClass; ?>">
									<i class="fas fa-search me-2"></i> Search Complaint
								</button>
							<?php } else { ?>
								<button type="submit" class="btn btn-primary">
									<i class="fas fa-search me-2"></i> Search Complaint
								</button>
							<?php } ?>
						</form>

						<?php if (isset($error)) { ?>
							<div class="alert alert-custom alert-danger mt-4">
								<i class="fas fa-exclamation-circle me-2"></i>
								<?php echo $error; ?>
							</div>
						<?php } ?>

						<?php if ($complaint) {
							// Determine status class
							$statusClass = '';
							switch ($status) {
								case 'pending':
									$statusClass = 'status-pending';
									$statusIcon = 'clock';
									break;
								case 'processing':
									$statusClass = 'status-processing';
									$statusIcon = 'spinner';
									break;
								case 'resolved':
								case 'solved':
									$statusClass = 'status-resolved';
									$statusIcon = 'check-circle';
									break;
								case 'rejected':
									$statusClass = 'status-rejected';
									$statusIcon = 'times-circle';
									break;
								default:
									$statusClass = 'status-pending';
									$statusIcon = 'question-circle';
							}

							// Determine complaint details class
							$detailsClass = "complaint-details complaint-details-{$status}";
							$titleClass = "complaint-title complaint-title-{$status}";
						?>
							<div class="<?php echo $detailsClass; ?>">
								<h4 class="<?php echo $titleClass; ?>">
									<i class="fas fa-file-alt me-2"></i>
									Complaint #<?php echo $complaint['id']; ?>
								</h4>

								<div class="complaint-info">
									<div class="info-item">
										<div class="info-label">Title</div>
										<div class="info-value"><?php echo htmlspecialchars($complaint['title']); ?></div>
									</div>

									<div class="info-item">
										<div class="info-label">Department</div>
										<div class="info-value">
											<i class="fas fa-building me-1 
											<?php echo ($status == 'resolved' || $status == 'solved') ? 'text-success' : ($status == 'pending' ? 'text-warning' : ($status == 'processing' ? 'text-info' : ($status == 'rejected' ? 'text-danger' : 'text-primary'))); ?>"></i>
											<?php echo htmlspecialchars($complaint['dept_name']); ?>
										</div>
									</div>

									<div class="info-item">
										<div class="info-label">Status</div>
										<div class="info-value">
											<span class="status-badge <?php echo $statusClass; ?>">
												<i class="fas fa-<?php echo $statusIcon; ?> me-1"></i>
												<?php echo ucfirst($complaint['status']); ?>
											</span>
										</div>
									</div>

									<div class="info-item">
										<div class="info-label">Registered At</div>
										<div class="info-value">
											<i class="far fa-calendar-alt me-1 
											<?php echo ($status == 'resolved' || $status == 'solved') ? 'text-success' : ($status == 'pending' ? 'text-warning' : ($status == 'processing' ? 'text-info' : ($status == 'rejected' ? 'text-danger' : 'text-primary'))); ?>"></i>
											<?php echo date('F d, Y h:i A', strtotime($complaint['created_at'])); ?>
										</div>
									</div>
								</div>

								<div class="description-box">
									<div class="info-label">Description</div>
									<div class="info-value">
										<?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
									</div>
								</div>

								<!-- <div class="mt-4 text-end">
									<?php
									// Determine button class based on status
									// $btnOutlineClass = "";
									// if ($status == 'resolved' || $status == 'solved') {
									// 	$btnOutlineClass = "btn-outline-success";
									// } else if ($status == 'pending') {
									// 	$btnOutlineClass = "btn-outline-warning";
									// } else if ($status == 'processing') {
									// 	$btnOutlineClass = "btn-outline-info";
									// } else if ($status == 'rejected') {
									// 	$btnOutlineClass = "btn-outline-danger";
									// } else {
									// 	$btnOutlineClass = "btn-outline-primary";
									// }
									?>
									<button type="button" class="btn <?php // echo $btnOutlineClass; 
																		?>"
										onclick="loadActivity(<?php // echo $complaint['id']; 
																?>)">
										<i class="fas fa-history me-2"></i> View Activity
									</button>
								</div> -->
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

	<script>
		function loadActivity(complaint_id) {
			$.ajax({
				url: 'fetch_activity.php',
				type: 'GET',
				data: {
					complaint_id: complaint_id
				},
				success: function(data) {
					$("#activityContent").html(data);
					new bootstrap.Modal(document.getElementById('activityModal')).show();
				}
			});
		}
	</script>
</body>

</html>