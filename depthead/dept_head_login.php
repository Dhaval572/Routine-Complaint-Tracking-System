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
	<title>Department Head Login</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
	<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
		<div class="col-md-4">
			<!-- Logo or Brand Image -->
			<div class="text-center mb-3">
				<i class="fas fa-user-tie text-white" style="font-size: 4rem;"></i>
			</div>

			<div class="card border-0 shadow-lg" style="border-radius: 1.5rem;">
				<div class="card-header border-0 bg-white text-center py-4" style="border-radius: 1.5rem 1.5rem 0 0;">
					<h3 class="font-weight-bold text-dark mb-2">Department Head Portal</h3>
					<p class="text-muted small mb-0">Access your administrative dashboard</p>
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
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>