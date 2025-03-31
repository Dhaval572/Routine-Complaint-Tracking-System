<?php
include '../config.php';

$error = null;
$success = null;

if (isset($_POST['register'])) {
	// Validate and sanitize inputs
	$name = trim($conn->real_escape_string($_POST['name']));
	$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
	$password = $_POST['password'];
	$passwordLength = strlen($password);
	
	// Validate inputs
	if (empty($name) || empty($email) || empty($password)) {
		$error = "All fields are required.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = "Please enter a valid email address.";
	} elseif ($passwordLength < 6) {
		$error = "Password must be at least 6 characters long.";
	} elseif ($passwordLength > 12) {
		$error = "Password cannot exceed 12 characters.";
	} elseif (!isset($_POST['terms'])) {
		$error = "You must agree to the terms and conditions.";
	} else {
		// Check if email already exists
		$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
		$check->bind_param("s", $email);
		$check->execute();
		$result = $check->get_result();
		
		if ($result->num_rows > 0) {
			$error = "Email already registered.";
		} else {
			// Hash password and insert user
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
			$role = 'citizen';

			$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

			if ($stmt->execute()) {
				// Redirect to login page instead of showing success message
				header("Location: user_login.php?registered=1");
				exit;
			} else {
				$error = "Error during registration. Please try again.";
			}
			$stmt->close();
		}
		$check->close();
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Citizen Registration</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background: linear-gradient(135deg, #0396FF 0%, #0D47A1 100%);">
	<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
		<div class="col-md-4">
			<!-- Logo or Brand Image -->
			<div class="text-center mb-3">
				<i class="fas fa-user-plus text-white" style="font-size: 3.5rem;"></i>
			</div>

			<div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
				<div class="card-header border-0 bg-white text-center py-3" style="border-radius: 1.5rem 1.5rem 0 0;">
					<div class="d-flex align-items-center justify-content-between px-3">
						<a href="../index.php" class="text-primary" style="font-size: 1.2rem;">
							<i class="fas fa-arrow-left"></i>
						</a>
						<h3 class="font-weight-bold text-primary mb-2">Create Account</h3>
						<div style="width: 20px;"></div>
					</div>
					<p class="text-muted small mb-0">Register as a citizen</p>
				</div>
				<div class="card-body px-4 py-4">
					<?php if ($error): ?>
						<div class='alert alert-danger py-2 d-flex align-items-center rounded-pill small'>
							<i class='fas fa-exclamation-circle mr-2'></i><?= $error ?>
						</div>
					<?php endif; ?>
					<form method="POST" action="">
						<div class="form-group mb-3">
							<div class="input-group shadow-sm">
								<div class="input-group-prepend">
									<span class="input-group-text bg-light border-right-0 rounded-pill px-3">
										<i class="fas fa-user text-primary"></i>
									</span>
								</div>
								<input type="text" name="name" required
									class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small"
									placeholder="Full name" autocomplete="off" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
							</div>
						</div>
						<div class="form-group mb-3">
							<div class="input-group shadow-sm">
								<div class="input-group-prepend">
									<span class="input-group-text bg-light border-right-0 rounded-pill px-3">
										<i class="fas fa-envelope text-primary"></i>
									</span>
								</div>
								<input type="email" name="email" required
									class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small"
									placeholder="Email address" autocomplete="off" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
							</div>
						</div>
						<div class="form-group mb-3">
							<div class="input-group shadow-sm">
								<div class="input-group-prepend">
									<span class="input-group-text bg-light border-right-0 rounded-pill px-3">
										<i class="fas fa-lock text-primary"></i>
									</span>
								</div>
								<input type="password" name="password" required minlength="6" maxlength="12"
									class="form-control bg-light border-left-0 rounded-pill py-2 pl-2 small"
									placeholder="Password (6-12 characters)" autocomplete="off">
							</div>
							<small class="form-text text-muted pl-2">Password must be 6-12 characters long</small>
						</div>
						<div class="form-group form-check mb-3 pl-4">
							<input type="checkbox" name="terms" class="form-check-input" id="termsCheck" required <?= isset($_POST['terms']) ? 'checked' : '' ?>>
							<label class="form-check-label small text-muted" for="termsCheck">
								I agree to the terms and conditions
							</label>
						</div>
						<button type="submit" name="register"
							class="btn btn-primary btn-block mb-3 shadow rounded-pill py-2 font-weight-bold">
							Register
						</button>
						<div class="text-center">
							<a href="user_login.php" class="text-primary small font-weight-bold">
								<i class="fas fa-sign-in-alt mr-2"></i>Already have an account? Login
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>