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
		$success = "Complaint found successfully!"; // Add this line
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
	<title>Track Complaint</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<link rel="stylesheet" href="../assets/css/alerts.css">
	<link rel="stylesheet" href="../assets/css/track-complaint.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
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

<body class="min-vh-100">
    <div class="position-fixed w-100 h-100 floating-blobs">
        <div class="position-absolute rounded-circle blob-1"></div>
        <div class="position-absolute rounded-circle blob-2"></div>
        <div class="position-absolute rounded-circle blob-3"></div>
    </div>

    <!-- Add this right after <body> tag -->
    <!-- Replace the existing navbar content -->
    <nav class="navbar navbar-expand-lg custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="user_dashboard.php">
                <i class="fas fa-clipboard-list mr-2"></i>
                Complaint Tracking System
            </a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link dashboard-link" href="user_dashboard.php">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Add this for floating background -->
    <div class="floating-blob blob-1"></div>
    <div class="floating-blob blob-2"></div>

	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<div class="card border-0 shadow-lg rounded-lg overflow-hidden" style="
					border-radius: 30px !important; 
					backdrop-filter: blur(20px); 
					background: rgba(255, 255, 255, 0.9);
					box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 8px 24px -4px rgba(41, 98, 255, 0.2);
					border: 1px solid rgba(255, 255, 255, 0.5);
				">
					<div class="card-header border-0 py-4" style="
						background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);
						border-bottom: 1px solid rgba(255, 255, 255, 0.2);
					">
						<div class="d-flex align-items-center">
							<div class="d-flex align-items-center justify-content-center rounded-lg text-white mr-3"
								style="
								width: 50px; 
								height: 50px; 
								background: rgba(255, 255, 255, 0.2); 
								box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2), inset 0 2px 4px rgba(255, 255, 255, 0.2);
								border-radius: 15px;
							">
								<i class="fas fa-search"></i>
							</div>
							<h4 class="mb-0 text-white font-weight-bold"
								style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">Track Your Complaint</h4>
						</div>
					</div>

					<div class="card-body p-md-5 p-4"
						style="background: linear-gradient(135deg, #f5f9ff 0%, #e6f0ff 100%);">
						<form method="GET" action="" class="mb-4">
							<div class="form-group p-4 rounded-lg" style="background: rgba(255, 255, 255, 0.7); border: 1px solid rgba(41, 98, 255, 0.1); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
								<label class="font-weight-bold text-primary">
									<i class="fas fa-hashtag mr-2"></i>Enter Complaint ID
								</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text bg-primary text-white border-0">
											<i class="fas fa-ticket-alt"></i>
										</span>
									</div>
									<input type="number" name="complaint_id" required
										class="form-control form-control-lg border-0 shadow-sm"
										placeholder="Enter your complaint ID" style="border-radius: 0 10px 10px 0;">
								</div>
							</div>
							<button type="submit" class="btn btn-lg btn-block rounded-pill shadow" style="
								background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
								color: white;
								border: none;
								transition: all 0.3s ease;
								padding: 12px 20px;
							" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(41, 98, 255, 0.3)'"
								onmouseout="this.style.transform=''; this.style.boxShadow=''">
								<i class="fas fa-search mr-2"></i>Track Complaint
							</button>
						</form>

						<?php if (!isset($_GET['complaint_id'])) { ?>
							<div class="text-center py-5 my-3 rounded-lg" style="
								background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(240, 247, 255, 0.9) 100%);
								border-radius: 20px; 
								box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(41, 98, 255, 0.1);
								border: 1px solid rgba(255, 255, 255, 0.6);
								backdrop-filter: blur(10px);">
								<div class="mb-4 mx-auto rounded-circle d-flex align-items-center justify-content-center" style="
									width: 100px; 
									height: 100px; 
									background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
									color: white;
									box-shadow: 0 8px 20px rgba(41, 98, 255, 0.3);">
									<i class="fas fa-search fa-3x"></i>
								</div>
								<h4 class="text-primary mb-3 font-weight-bold">Enter Complaint ID to Track</h4>
								<p class="text-muted mb-4 px-4 mx-auto" style="max-width: 600px;">Please enter your complaint ID in the form above to track its current status and progress.</p>
								<div class="alert alert-info mx-auto mb-0" style="max-width: 500px; background: rgba(41, 98, 255, 0.05); border: 1px solid rgba(41, 98, 255, 0.1);">
									<div class="d-flex align-items-center">
										<i class="fas fa-info-circle text-primary mr-3" style="font-size: 1.5rem;"></i>
										<p class="mb-0">You can find your complaint ID in the confirmation email or SMS you received when registering the complaint.</p>
									</div>
								</div>
							</div>
						<?php } ?>

						<?php if (isset($error)) { ?>
							<div class="text-center py-5 my-3"
								style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.15), rgba(240, 62, 78, 0.25)); 
								border-radius: 20px; 
								box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
								border: 1px solid rgba(220, 53, 69, 0.2);">
								<div class="mb-4 text-white mx-auto rounded-circle d-flex align-items-center justify-content-center"
									style="width: 100px; height: 100px; background: rgba(220, 53, 69, 0.8); font-size: 3rem;
									box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
									<i class="fas fa-exclamation-circle"></i>
								</div>
								<h4 class="text-danger mb-3 font-weight-bold">Complaint Not Found</h4>
								<p class="text-dark mb-4">We couldn't find any complaint with the ID you provided. Please
									check the ID and try again.</p>
								<button onclick="window.history.back()" class="btn btn-danger rounded-pill px-4 shadow-sm">
									<i class="fas fa-arrow-left mr-2"></i>Go Back
								</button>
							</div>
						<?php } ?>

						<?php if ($complaint) { ?>
							<div class="complaint-details p-4 mt-4" style="
								background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(240, 247, 255, 0.9) 100%);
								border-radius: 20px; 
								box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(41, 98, 255, 0.1);
								border: 1px solid rgba(255, 255, 255, 0.6);
								backdrop-filter: blur(10px);
							">
								<div class="d-flex align-items-center mb-4">
									<div class="d-flex align-items-center justify-content-center rounded-circle mr-3" style="
										width: 60px; 
										height: 60px; 
										background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
										color: white;
										box-shadow: 0 8px 20px rgba(41, 98, 255, 0.3);
									">
										<i class="fas fa-clipboard-list fa-2x"></i>
									</div>
									<div>
										<h4 class="mb-0 font-weight-bold text-primary">Complaint
											#<?php echo $complaint['id']; ?></h4>
										<p class="text-muted mb-0">Registered on
											<?php echo date('M d, Y', strtotime($complaint['created_at'])); ?>
										</p>
									</div>
								</div>

								<div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 h-100" style="background: rgba(41, 98, 255, 0.08); border-radius: 15px; border: 1px solid rgba(41, 98, 255, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(41, 98, 255, 0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-heading mr-2"></i>Title
                            </h5>
                            <p class="mb-0 font-weight-medium text-dark">
                                <?php echo htmlspecialchars($complaint['title']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 h-100" style="background: rgba(76, 175, 80, 0.08); border-radius: 15px; border: 1px solid rgba(76, 175, 80, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(76, 175, 80, 0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <h5 class="text-success mb-3">
                                <i class="fas fa-building mr-2"></i>Department
                            </h5>
                            <p class="mb-0 font-weight-medium text-dark">
                                <?php echo htmlspecialchars($complaint['dept_name']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 h-100" style="background: rgba(255, 152, 0, 0.08); border-radius: 15px; border: 1px solid rgba(255, 152, 0, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(255, 152, 0, 0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <h5 class="text-warning mb-3">
                                <i class="fas fa-tasks mr-2"></i>Status
                            </h5>
                            <span class="badge badge-pill px-3 py-2 <?php echo $complaint['status'] == 'solved' ? 'badge-success' : ($complaint['status'] == 'pending' ? 'badge-warning' : 'badge-info'); ?>">
                                <?php echo ucfirst($complaint['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 h-100" style="background: rgba(244, 67, 54, 0.08); border-radius: 15px; border: 1px solid rgba(244, 67, 54, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(244, 67, 54, 0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <h5 class="text-danger mb-3">
                                <i class="fas fa-flag mr-2"></i>Priority
                            </h5>
                            <span class="badge badge-pill px-3 py-2 <?php echo $complaint['priority'] == 'high' ? 'badge-danger' : ($complaint['priority'] == 'medium' ? 'badge-warning' : 'badge-info'); ?>">
                                <?php echo ucfirst($complaint['priority']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 h-100" style="background: rgba(156, 39, 176, 0.08); border-radius: 15px; border: 1px solid rgba(156, 39, 176, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(156, 39, 176, 0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <h5 class="text-purple mb-3" style="color: #9c27b0;">
                                <i class="fas fa-calendar-alt mr-2"></i>Registered At
                            </h5>
                            <p class="mb-0 font-weight-medium text-dark">
                                <?php echo date('F d, Y h:i A', strtotime($complaint['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="p-3" style="background: rgba(0, 188, 212, 0.08); border-radius: 15px; border: 1px solid rgba(0, 188, 212, 0.15); transition: transform 0.3s ease, box-shadow 0.3s ease;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 20px rgba(0, 188, 212, 0.15)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                            <h5 class="mb-3" style="color: #00bcd4;">
                                <i class="fas fa-align-left mr-2"></i>Description
                            </h5>
                            <p class="mb-0 text-dark">
                                <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-lg rounded-pill shadow-sm" style="
                        background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%); 
                        color: white;
                        border: none;
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px rgba(41, 98, 255, 0.3)'"
                        onmouseout="this.style.transform=''; this.style.boxShadow=''"
                        onclick="loadActivity(<?php echo $complaint['id']; ?>)">
                        <i class="fas fa-history mr-2"></i>View Activity Timeline
                    </button>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow rounded-lg"
            style="border-radius: 20px !important; backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.98);">
            <div class="modal-header text-white border-0 rounded-top"
                style="background: linear-gradient(135deg, #2962ff 0%, #1565c0 100%);">
                <h5 class="modal-title">
                    <i class="fas fa-history mr-2"></i>Complaint Activity Timeline
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="activityContent">
                <!-- AJAX loaded content -->
            </div>
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
        box-shadow: 0 0 0 0.2rem rgba(41, 98, 255, 0.25);
        border-color: #2962ff;
    }
</style>
</body>

</html>
<!-- Success alert -->
<?php if (isset($success)) { ?>
    <div class="custom-alert alert alert-success d-flex align-items-center p-3" role="alert">
        <div class="alert-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="mb-1 font-weight-bold">Complaint Found!</h5>
            <p class="mb-0">Your complaint details are displayed below.</p>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.custom-alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 4000);
        });
    </script>
<?php } ?>

<!-- Error alert -->
<?php if (isset($error)) { ?>
    <div class="custom-alert alert alert-danger d-flex align-items-center p-3" role="alert">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="flex-grow-1">
            <h5 class="mb-1 font-weight-bold">Complaint Not Found!</h5>
            <p class="mb-0">Please check the complaint ID and try again.</p>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.custom-alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 4000);
        });
    </script>
<?php } ?>
