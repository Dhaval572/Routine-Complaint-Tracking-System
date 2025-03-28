<?php
include '../config.php';

if (isset($_POST['login'])) {

	$email = $conn->real_escape_string($_POST['email']);
	$password = $_POST['password'];

	$sql = "SELECT * FROM users WHERE email = '$email' AND role = 'citizen'";
	$result = $conn->query($sql);

	if ($result && $result->num_rows > 0) {

		$user = $result->fetch_assoc();

		if (password_verify($password, $user['password'])) {
			$_SESSION['user_id'] = $user['id'];
			$_SESSION['user_name'] = $user['name'];
			header("Location: user_dashboard.php");
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
	<title>User Login</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<h2 class="mt-5 text-center">Citizen Login</h2>
				<?php
				if (isset($error))
					echo "<div class='alert alert-danger'>$error</div>";
				?>
				<form method="POST" action="">
					<div class="form-group">
						<label>Email address</label>
						<input type="email" name="email" required class="form-control" placeholder="Enter email">
					</div>
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" required class="form-control" placeholder="Password">
					</div>
					<button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
				</form>
				<p class="text-center mt-3"><a href="register_user.php">Register as Citizen</a></p>
			</div>
		</div>
	</div>
</body>
</html>