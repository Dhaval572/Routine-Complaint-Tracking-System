<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Add form submission check to prevent double submission
	if (
		!isset($_SESSION['last_feedback_time']) ||
		(time() - $_SESSION['last_feedback_time']) > 5
	) {

		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$rating = (int) $_POST['rating'];
		$category = mysqli_real_escape_string($conn, $_POST['category']);
		$message = mysqli_real_escape_string($conn, $_POST['message']);

		$query = "INSERT INTO feedback (user_name, email, rating, category, message) 
                VALUES ('$name', '$email', $rating, '$category', '$message')";

		if (mysqli_query($conn, $query)) {
			$_SESSION['last_feedback_time'] = time();
			$success_message = "Thank you for your feedback!";
		} else {
			$error_message = "Error submitting feedback. Please try again.";
		}
	} else {
		$error_message = "Please wait a few seconds before submitting another feedback.";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Feedback - Complaint Tracking System</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<style>
		.rating-stars i {
			cursor: pointer;
			color: #ddd;
			font-size: 30px;
			transition: color 0.2s;
		}

		.rating-stars i.active {
			color: #ffc107;
		}

		.category-card {
			transition: transform 0.3s, box-shadow 0.3s;
		}

		.category-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
		}
	</style>
</head>

<body class="bg-light">
	<!-- Navigation Bar -->
	<!-- Update the navbar class -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
		<div class="container">
			<a class="navbar-brand d-flex align-items-center" href="index.php">
				<i class="fas fa-comments mr-2"></i>
				Complaint Tracking System
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav ml-auto">
					<li class="nav-item mx-1">
						<a class="nav-link btn btn-primary btn-lg rounded-pill px-4" href="index.php">
							<i class="fas fa-home mr-2"></i>
							<span class="font-weight-bold">Home</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Hero Section -->
	<div class="bg-primary text-white py-5">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-8 mx-auto text-center">
					<h1 class="display-4 font-weight-bold mb-4">Your Opinion Matters!</h1>
					<p class="lead">Help us improve our services by sharing your valuable feedback.</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Main Content -->
	<div class="container py-5">
		<!-- Update the success message section -->
		<?php if (isset($success_message)): ?>
			<div class="alert alert-success text-center py-4" role="alert">
				<i class="fas fa-check-circle fa-3x mb-3"></i>
				<h4 class="alert-heading mb-3"><?php echo $success_message; ?></h4>
				<div class="mt-4 d-flex justify-content-center">
					<a href="index.php" class="btn btn-primary btn-lg rounded-pill">
						<i class="fas fa-home mr-2"></i>Return to Home
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if (isset($error_message)): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<i class="fas fa-exclamation-circle mr-2"></i><?php echo $error_message; ?>
				<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
			</div>
		<?php endif; ?>

		<!-- Feedback Form - Show only if no success message -->
		<?php if (!isset($success_message)): ?>
			<div class="row">
				<div class="col-lg-8 mx-auto">
					<div class="card border-0 shadow-lg">
						<div class="card-body p-5">
							<h3 class="text-center mb-4">Share Your Experience</h3>
							<form method="POST" action="" autocomplete="off">
								<div class="form-group text-center">
									<label class="h5 mb-3">How would you rate our system?</label>
									<div class="rating-stars mb-3">
										<i class="fas fa-star" data-rating="1"></i>
										<i class="fas fa-star" data-rating="2"></i>
										<i class="fas fa-star" data-rating="3"></i>
										<i class="fas fa-star" data-rating="4"></i>
										<i class="fas fa-star" data-rating="5"></i>
									</div>
									<input type="hidden" name="rating" id="selected-rating" required>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<input type="text" name="name" class="form-control form-control-lg rounded-pill"
												placeholder="Your Name" required autocomplete="off">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<input type="email" name="email"
												class="form-control form-control-lg rounded-pill" placeholder="Your Email"
												required autocomplete="off">
										</div>
									</div>
								</div>

								<div class="form-group">
									<select name="category" class="form-control form-control-lg rounded-pill" required>
										<option value="">Select Feedback Category</option>
										<option value="System Performance">System Performance</option>
										<option value="User Interface">User Interface</option>
										<option value="Complaint Resolution">Complaint Resolution</option>
										<option value="Customer Support">Customer Support</option>
										<option value="Suggestions">Suggestions</option>
									</select>
								</div>

								<div class="form-group">
									<textarea name="message" class="form-control rounded" rows="5"
										placeholder="Your Feedback Message" required></textarea>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-primary btn-lg rounded-pill px-5">
										<i class="fas fa-paper-plane mr-2"></i>Submit Feedback
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const stars = document.querySelectorAll('.rating-stars i');
			const ratingInput = document.getElementById('selected-rating');

			stars.forEach(star => {
				star.addEventListener('mouseover', function () {
					const rating = this.dataset.rating;
					highlightStars(rating);
				});

				star.addEventListener('click', function () {
					const rating = this.dataset.rating;
					ratingInput.value = rating;
					highlightStars(rating);
				});
			});

			document.querySelector('.rating-stars').addEventListener('mouseout', function () {
				const rating = ratingInput.value || 0;
				highlightStars(rating);
			});

			function highlightStars(rating) {
				stars.forEach(star => {
					star.classList.toggle('active', star.dataset.rating <= rating);
				});
			}
		});
	</script>

	<!-- Footer -->
	<footer class="bg-dark text-white py-4 mt-5">
		<div class="container">
			<div class="row">
				<div class="col-md-6 text-center text-md-left">
					<p class="mb-0">&copy; 2025 Complaint Tracking System. All rights reserved.</p>
				</div>
				<div class="col-md-6 text-center text-md-right">
					<a href="#" class="text-white mx-2">Terms of Service</a>
					<a href="#" class="text-white mx-2">Privacy Policy</a>
				</div>
			</div>
		</div>
	</footer>

	<!-- Add Bootstrap JS and dependencies -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>