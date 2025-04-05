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
		:root {
			--primary-color: #4361ee;
			--primary-dark: #3a56d4;
			--primary-light: #eef2ff;
			--secondary-color: #f8f9fc;
			--accent-color: #ff9e00;
			--accent-light: #fff4e0;
			--success-color: #38b000;
			--danger-color: #e63946;
			--dark-color: #1d3557;
			--light-color: #ffffff;
			--border-radius: 15px;
			--box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
			--card-bg: #ffffff;
		}

		body {
			background-color: var(--primary-light);
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		.bg-primary {
			background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
		}

		.btn-primary {
			background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
			border: none;
			box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
			transition: all 0.3s ease;
		}

		.btn-primary:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
			background: linear-gradient(to right, var(--primary-dark), var(--primary-color));
		}

		.card {
			border: none;
			border-radius: var(--border-radius);
			box-shadow: var(--box-shadow);
			overflow: hidden;
			transition: all 0.3s ease;
			background: var(--light-color);
		}

		.card:hover {
			transform: translateY(-5px);
			box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
		}

		.card-body {
			background: var(--card-bg);
			border-top: 4px solid var(--primary-color);
			position: relative;
			overflow: hidden;
		}
		
		.card-body::before {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			width: 100%;
			height: 100%;
			background: radial-gradient(circle at top right, rgba(67, 97, 238, 0.05), transparent 60%);
			z-index: 0;
		}
		
		.card-body > * {
			position: relative;
			z-index: 1;
		}

		.form-control {
			border: 2px solid #e8eaef;
			transition: all 0.3s ease;
			background-color: var(--light-color);
		}

		.form-control:focus {
			border-color: var(--primary-color);
			box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
			background-color: var(--light-color);
		}

		.rating-stars i {
			cursor: pointer;
			color: #e0e0e0;
			font-size: 40px;
			margin: 0 8px;
			transition: all 0.3s ease;
			filter: drop-shadow(0 2px 3px rgba(0,0,0,0.1));
		}

		.rating-stars i:hover {
			transform: scale(1.2) rotate(5deg);
		}

		.rating-stars i.active {
			color: var(--accent-color);
			text-shadow: 0 0 10px rgba(255, 158, 0, 0.5);
		}

		.hero-section {
			position: relative;
			overflow: hidden;
			background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
		}

		.hero-section::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.343 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
			opacity: 0.5;
		}

		.display-4 {
			font-weight: 700;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
			color: var(--light-color);
		}

		.alert-success {
			background: linear-gradient(to right, var(--success-color), #2d9300);
			color: white;
			border: none;
			border-radius: var(--border-radius);
			box-shadow: 0 5px 15px rgba(56, 176, 0, 0.3);
		}

		.alert-danger {
			background: linear-gradient(to right, var(--danger-color), #d90429);
			color: white;
			border: none;
			border-radius: var(--border-radius);
			box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
		}

		footer {
			background: linear-gradient(to right, var(--dark-color), #0a2342);
		}

		footer a {
			transition: all 0.3s ease;
			color: var(--light-color) !important;
		}

		footer a:hover {
			color: var(--accent-color) !important;
			text-decoration: none;
		}

		.text-primary {
			color: var(--primary-color) !important;
		}

		h3.text-primary {
			background: linear-gradient(to right, var(--primary-color), var(--primary-dark));
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			display: inline-block;
		}

		.rounded-pill {
			border-radius: 50px !important;
		}

		.shadow {
			box-shadow: var(--box-shadow) !important;
		}

		/* Animation for form elements */
		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.form-group {
			animation: fadeInUp 0.5s ease forwards;
			opacity: 0;
		}

		.form-group:nth-child(1) { animation-delay: 0.1s; }
		.form-group:nth-child(2) { animation-delay: 0.2s; }
		.form-group:nth-child(3) { animation-delay: 0.3s; }
		.form-group:nth-child(4) { animation-delay: 0.4s; }
		.form-group:nth-child(5) { animation-delay: 0.5s; }
	</style>
</head>

<body>
	<!-- Navigation Bar -->
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
						<a class="nav-link btn btn-light text-primary rounded-pill px-4" href="index.php">
							<i class="fas fa-home mr-2"></i>
							<span class="font-weight-bold">Home</span>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Hero Section -->
	<div class="bg-primary text-white py-5 hero-section">
		<div class="container position-relative">
			<div class="row align-items-center">
				<div class="col-lg-8 mx-auto text-center">
					<h1 class="display-4 font-weight-bold mb-4">Your Opinion Matters!</h1>
					<p class="lead">Help us improve our services by sharing your valuable feedback.</p>
					<div class="d-inline-block bg-white text-primary px-4 py-2 rounded-pill mt-3 shadow">
						<i class="fas fa-heart text-danger mr-2"></i> We value your thoughts
					</div>
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
							<h3 class="text-center mb-4 text-primary">Share Your Experience</h3>
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