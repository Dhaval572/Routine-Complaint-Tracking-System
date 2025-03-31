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
        VALUES ('$citizen_id', '$dept_id', '$title', '$description', NULL, " .
		($dept_head_id ? "'$dept_head_id'" : "NULL") . ", " .
		($target_id ? "'$target_id'" : "NULL") . ", " .
		($target_option != 'none' ? "'$target_option'" : "NULL") . ", NOW())";

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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register Complaint</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<!-- jQuery is needed for show/hide behavior and AJAX -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

			$("select[name='target_dept']").change(function () {
				loadTargets();
			});

			function loadTargets() {
				var dept_id = $("select[name='target_dept']").val();
				var target_option = $("input[name='target_option']:checked").val();
				if (dept_id != '' && target_option != 'none') {
					$.ajax({
						url: 'fetch_targets.php',
						data: { dept_id: dept_id, target: target_option },
						success: function (data) {
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

<body class="min-vh-100" style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);">
	<!-- Animated floating blobs using Bootstrap utilities -->
	<div class="position-fixed w-100 h-100" style="z-index: -1;">
		<div class="position-absolute rounded-circle"
			style="width: 400px; height: 400px; background: rgba(229, 115, 115, 0.15); filter: blur(60px); top: 20%; left: 10%; animation: float 12s infinite;">
		</div>
		<div class="position-absolute rounded-circle"
			style="width: 300px; height: 300px; background: rgba(239, 83, 80, 0.12); filter: blur(60px); top: 50%; right: 15%; animation: float 12s infinite 4s;">
		</div>
		<div class="position-absolute rounded-circle"
			style="width: 250px; height: 250px; background: rgba(244, 67, 54, 0.15); filter: blur(60px); bottom: 10%; left: 30%; animation: float 12s infinite 8s;">
		</div>
	</div>

	<nav class="navbar navbar-expand-lg navbar-dark shadow-lg mx-md-4 mx-2" style="
	  background: linear-gradient(135deg, #e53935 0%, #c62828 100%); 
	  border-radius: 20px;
	  margin-top: 15px;
	  padding: 12px 20px;
	  border: 1px solid rgba(255, 255, 255, 0.2);
	  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15), 0 5px 10px rgba(0, 0, 0, 0.05);
	">
		<div class="container">
			<span class="navbar-brand d-flex align-items-center">
				<div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-danger mr-2"
					style="width: 38px; height: 38px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
					<i class="fas fa-file-alt"></i>
				</div>
				<span class="font-weight-bold ml-1"
					style="letter-spacing: 0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">Register Complaint</span>
			</span>
			<div class="ml-auto">
				<a href="user_dashboard.php" class="btn rounded-pill px-4 shadow-sm" style="
			background: rgba(255, 255, 255, 0.15); 
			color: white; 
			border: 1px solid rgba(255, 255, 255, 0.3);
			transition: all 0.3s ease;
			backdrop-filter: blur(5px);
		  " onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'"
					onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'">
					<i class="fas fa-home mr-2"></i>Dashboard
				</a>
			</div>
		</div>
	</nav>

	<div class="container py-5">
		<div class="card border-0 shadow-lg rounded-lg overflow-hidden" style="
			border-radius: 30px !important; 
			backdrop-filter: blur(20px); 
			background: rgba(255, 255, 255, 0.9);
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 8px 24px -4px rgba(229, 57, 53, 0.2);
			border: 1px solid rgba(255, 255, 255, 0.5);
		">
			<div class="card-header border-0 py-4" style="
				background: linear-gradient(135deg, #e53935 0%, #c62828 100%);
				border-bottom: 1px solid rgba(255, 255, 255, 0.2);
			">
				<div class="d-flex align-items-center">
					<div class="d-flex align-items-center justify-content-center rounded-lg text-white mr-3" style="
						width: 50px; 
						height: 50px; 
						background: rgba(255, 255, 255, 0.2); 
						box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.2);
						border-radius: 15px;
					">
						<i class="fas fa-clipboard-list"></i>
					</div>
					<h4 class="mb-0 text-white font-weight-bold" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
						Register Your Complaint</h4>
				</div>
			</div>

			<div class="card-body p-md-5 p-4" style="background: linear-gradient(135deg, #fff5f5 0%, #ffebee 100%);">
				<?php
				if (isset($success)) {
					// Replace the regular success message with JavaScript to show the modal, and ensure the modal has the right options for the user
					echo "<script>
						$(document).ready(function() {
							$('#successModal').modal('show');
						});
					</script>";
				}
				if (isset($error)) {
					echo "<div class='alert shadow-sm border-0 rounded-lg' style='background: rgba(244, 67, 54, 0.1); border-left: 4px solid #F44336;'>
						<div class='d-flex align-items-center'>
							<i class='fas fa-exclamation-circle text-danger mr-3' style='font-size: 1.5rem;'></i>
							<div>$error</div>
						</div>
					</div>";
				}
				?>
				<form method="POST" action="" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold text-danger">
									<i class="fas fa-heading mr-2"></i>Title
								</label>
								<input type="text" name="title" required
									class="form-control form-control-lg shadow-sm border-0"
									placeholder="Enter complaint title"
									style="border-radius: 10px; border-left: 4px solid #e53935;">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold text-danger">
									<i class="fas fa-building mr-2"></i>Select Department
								</label>
								<select name="dept_id" required class="form-control form-control-lg shadow-sm border-0"
									style="border-radius: 10px; border-left: 4px solid #e53935;">
									<option value="">Select Department</option>
									<?php while ($dept = $dept_result->fetch_assoc()) { ?>
										<option value="<?php echo $dept['id']; ?>">
											<?php echo htmlspecialchars($dept['name']); ?>
										</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="font-weight-bold text-danger">
							<i class="fas fa-align-left mr-2"></i>Description
						</label>
						<textarea name="description" required class="form-control shadow-sm border-0" rows="5"
							placeholder="Enter detailed description"
							style="border-radius: 10px; border-left: 4px solid #e53935;"></textarea>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold text-danger">
									<i class="fas fa-calendar-alt mr-2"></i>Date of Incident
								</label>
								<input type="date" name="incident_date" required
									class="form-control form-control-lg shadow-sm border-0"
									style="border-radius: 10px; border-left: 4px solid #e53935;">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="font-weight-bold text-danger">
									<i class="fas fa-clock mr-2"></i>Time of Incident
								</label>
								<input type="time" name="incident_time" required
									class="form-control form-control-lg shadow-sm border-0"
									style="border-radius: 10px; border-left: 4px solid #e53935;">
							</div>
						</div>
					</div>

					<div class="card mb-4 border-0 shadow-sm"
						style="border-radius: 15px; background: rgba(255, 255, 255, 0.7); border-left: 4px solid #e53935;">
						<div class="card-body">
							<h5 class="text-danger mb-3">
								<i class="fas fa-user-shield mr-2"></i>Complaint Target
							</h5>
							<div class="form-group mb-0">
								<label class="font-weight-medium">Is this complaint against a specific officer/dept
									head?</label>
								<div class="d-flex flex-wrap mt-2">
									<div class="custom-control custom-radio mr-4 mb-2">
										<input type="radio" id="target_none" name="target_option" value="none" checked
											class="custom-control-input">
										<label class="custom-control-label" for="target_none">No</label>
									</div>
									<div class="custom-control custom-radio mr-4 mb-2">
										<input type="radio" id="target_officer" name="target_option" value="officer"
											class="custom-control-input">
										<label class="custom-control-label" for="target_officer">Officer</label>
									</div>
									<div class="custom-control custom-radio mb-2">
										<input type="radio" id="target_dept_head" name="target_option" value="dept_head"
											class="custom-control-input">
										<label class="custom-control-label" for="target_dept_head">Dept Head</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="target_section" class="card mb-4 border-0 shadow-sm"
						style="border-radius: 15px; background: rgba(255, 255, 255, 0.7); border-left: 4px solid #e53935;">
						<div class="card-body">
							<h5 class="text-danger mb-3">
								<i class="fas fa-crosshairs mr-2"></i>Target Details
							</h5>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="font-weight-medium">Select Target Department</label>
										<select name="target_dept" class="form-control shadow-sm border-0"
											style="border-radius: 10px; border-left: 4px solid #e53935;">
											<option value="">Select Department</option>
											<?php
											// Re-run query for departments
											$dept_result2 = $conn->query("SELECT * FROM departments");
											while ($d = $dept_result2->fetch_assoc()) { ?>
												<option value="<?php echo $d['id']; ?>">
													<?php echo htmlspecialchars($d['name']); ?>
												</option>
											<?php } ?>
										</select>
										<small class="form-text text-muted">First select a department to see available
											personnel</small>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="font-weight-medium">Select Target Person</label>
										<select name="target_id" class="form-control shadow-sm border-0"
											style="border-radius: 10px; border-left: 4px solid #e53935;">
											<option value="">Please select a department first</option>
										</select>
										<small class="form-text text-muted">If the person is not listed, please mention
											their name in the description</small>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="font-weight-bold text-danger">
							<i class="fas fa-paperclip mr-2"></i>Attachment (if any)
						</label>
						<div class="custom-file">
							<input type="file" name="attachment" class="custom-file-input" id="customFile">
							<label class="custom-file-label" for="customFile"
								style="border-radius: 10px; border-left: 4px solid #e53935;">Choose file</label>
						</div>
						<small class="form-text text-muted">Upload any supporting documents or evidence
							(optional)</small>
					</div>

					<div class="card mb-4 border-0 shadow-sm"
						style="border-radius: 15px; background: rgba(255, 235, 238, 0.7); border-left: 4px solid #e53935;">
						<div class="card-body">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" name="consent" class="custom-control-input" id="consentCheck"
									required>
								<label class="custom-control-label" for="consentCheck">
									I confirm that all the information provided is accurate to the best of my knowledge,
									and I give my consent to register this complaint.
								</label>
							</div>
						</div>
					</div>

					<div class="text-center mt-4">
						<button type="submit" name="register_complaint" class="btn btn-lg rounded-pill shadow px-5"
							style="
							background: linear-gradient(135deg, #e53935 0%, #c62828 100%); 
							color: white;
							border: none;
							transition: all 0.3s ease;
							padding: 12px 30px;
						" onmouseover="this.style.background='linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%)'"
							onmouseout="this.style.background='linear-gradient(135deg, #e53935 0%, #c62828 100%)'">
							<i class="fas fa-paper-plane mr-2"></i>Submit Complaint
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<style>
		@keyframes float {

			0%,
			100% {
				transform: translateY(0);
			}

			50% {
				transform: translateY(-20px);
			}
		}

		.form-control:focus {
			box-shadow: 0 0 0 0.2rem rgba(229, 57, 53, 0.25);
			border-color: #e53935;
		}

		.custom-control-input:checked~.custom-control-label::before {
			background-color: #e53935;
			border-color: #c62828;
		}
	</style>

	<script>
		// Add the following code for file input
		$(".custom-file-input").on("change", function () {
			var fileName = $(this).val().split("\\").pop();
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName || "Choose file");
		});
	</script>
</body>

</html>

</script>

<!-- Success Modal -->
<?php if (isset($success)): ?>
	<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
				<div class="modal-header border-0 text-center py-4"
					style="background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);">
					<h5 class="modal-title w-100 text-white font-weight-bold" id="successModalLabel">
						<i class="fas fa-check-circle mr-2"></i>Complaint Registered
					</h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center p-5">
					<div class="mb-4">
						<div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
							style="width: 100px; height: 100px; background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%); box-shadow: 0 10px 20px rgba(46, 125, 50, 0.3);">
							<i class="fas fa-clipboard-check text-white" style="font-size: 3rem;"></i>
						</div>
						<h4 class="font-weight-bold">Thank You!</h4>
						<p class="text-muted mb-0">Your complaint has been successfully registered.</p>
					</div>

					<div class="alert alert-light border-left-0 border-right-0 rounded-0 py-3 mb-4"
						style="background-color: rgba(76, 175, 80, 0.1);">
						<div class="d-flex align-items-center">
							<i class="fas fa-info-circle text-success mr-3" style="font-size: 1.5rem;"></i>
							<div class="text-left">
								<p class="mb-0">Your complaint has been assigned to the department head and will be
									processed shortly.</p>
							</div>
						</div>
					</div>

					<div class="d-flex justify-content-center">
						<a href="user_dashboard.php" class="btn btn-success rounded-pill px-4 mr-2">
							<i class="fas fa-home mr-2"></i>Go to Dashboard
						</a>
						<button type="button" class="btn btn-outline-success rounded-pill px-4" data-dismiss="modal">
							<i class="fas fa-plus mr-2"></i>Register Another
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
</body>

</html>