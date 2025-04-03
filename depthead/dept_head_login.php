<?php
include '../config.php';

if (isset($_POST['login'])) {

	$email = $conn->real_escape_string($_POST['email']);
	$password = $_POST['password'];

	// Only allow dept head login
	$sql = "SELECT * FROM users WHERE email = '$email' AND role = 'dept_head'";
	$result = $conn->query($sql);

	if ($result && $result->num_rows > 0) {

		$user = $result->fetch_assoc();

		if (password_verify($password, $user['password'])) {
			$_SESSION['dept_head_id'] = $user['id'];
			$_SESSION['dept_head_name'] = $user['name'];
			$_SESSION['dept_head_department'] = $user['department_id'];
			header("Location: dept_head_dashboard.php");
			exit;
		} else {
			$error = "Invalid credentials.";
		}

	} else {
		$error = "Invalid credentials.";
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title>Department Head Login</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<style>
		body {
			background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
			min-height: 100vh;
			padding: 15px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.login-container {
			width: 100%;
			max-width: 450px;
			margin: 0 auto;
		}

		.card {
			margin: 0 10px;
			transition: transform 0.3s ease;
		}

		.card:hover {
			transform: translateY(-3px);
		}

		@media (max-width: 576px) {
			.card-body {
				padding: 1.25rem !important;
			}

			.card-header h3 {
				font-size: 1.4rem;
			}

			.form-control,
			.btn {
				padding: 0.6rem 1rem !important;
			}

			.input-group-text {
				padding: 0.6rem 0.8rem !important;
			}
		}

		@media (max-height: 700px) {
			body {
				padding: 30px 15px;
			}

			.card {
				margin-top: 1rem;
			}
		}

		.form-control:focus {
			box-shadow: none;
			border-color: #1e3c72;
		}

		.input-group:focus-within .input-group-text {
			border-color: #1e3c72;
		}

		@media (prefers-reduced-motion: reduce) {
			.card {
				transition: none;
			}
		}
	</style>
</head>

<body>
	<div class="login-container">
		<!-- Existing body content remains unchanged -->
		<div class="text-center mb-3">
			<i class="fas fa-user-tie text-white" style="font-size: 4rem;"></i>
		</div>

		<div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
			<div class="card-header border-0 bg-white text-center py-4" style="border-radius: 1.5rem 1.5rem 0 0;">
				<div class="d-flex align-items-center justify-content-between px-3">
					<a href="../index.php" class="text-dark" style="font-size: 1.2rem;">
						<i class="fas fa-arrow-left"></i>
					</a>
					<h3 class="font-weight-bold mb-2">Department Head</h3>
					<div style="width: 20px;"></div>
				</div>
				<p class="text-muted small mb-0">Welcome back to your workspace</p>
			</div>
			<div class="card-body px-4 py-4">
				<?php if (isset($error)): ?>
					<div class='alert alert-danger py-2 d-flex align-items-center rounded-pill small'>
						<i class='fas fa-exclamation-circle mr-2'></i><?php echo $error; ?>
					</div>
				<?php endif; ?>
				<form method="POST" action="">
					<div class="form-group mb-4">
						<div class="input-group shadow-sm">
							<div class="input-group-prepend">
								<span class="input-group-text bg-light border-right-0 rounded-pill px-3">
									<i class="fas fa-envelope text-secondary"></i>
								</span>
							</div>
							<input type="email" name="email" required
								class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small"
								placeholder="Official email address" autocomplete="off">
						</div>
					</div>
					<div class="form-group mb-4">
						<div class="input-group shadow-sm">
							<div class="input-group-prepend">
								<span class="input-group-text bg-light border-right-0 rounded-pill px-3">
									<i class="fas fa-lock text-secondary"></i>
								</span>
							</div>
							<input type="password" name="password" required
								class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small"
								placeholder="Password" autocomplete="off">
						</div>
					</div>
					<button type="submit" name="login"
						class="btn btn-dark btn-block mb-4 shadow rounded-pill py-2 font-weight-bold">
						<i class="fas fa-sign-in-alt mr-2"></i>Access Dashboard
					</button>
					<div class="text-center small">
						<span class="text-muted">Secure Administrative Access</span>
						<i class="fas fa-shield-alt text-secondary ml-2"></i>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>