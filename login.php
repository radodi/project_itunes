
<?php
session_start();
if (isset($_SESSION['user_id'])) {
	header('Location: http://localhost/project_itunes/');
}
include 'includes/db_connect.php';
include 'includes/functions.php';
if (isset($_POST['reg'])) {
	register_user($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['password_re'], $conn);
} elseif (isset($_POST['log'])) {
	login_user($_POST['email'], $_POST['password'], $conn);
}
?>
<!DOCTYPE html>
<html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>My Tunes - Log in / Register.</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<!-- jQuery  -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<!-- Open Sans Font -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600" rel="stylesheet"> 
	<!-- Material Icons Font -->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/desktop.css">
</head>
<body>
	<div class="container-fluid header">
		<!-- header -->
		<div class="row">
			<div class="col-md-4">
				<div class="box">
					<a href="index.php"><img src="img/logo.png" alt="My Tunes Logo"></a>
					<audio id="player"></audio>
				</div>
				<div class="box">
					<h1 class="name">My Tunes</h1>
					<h2 class="slogan">Music Sharing <i class="material-icons">headset</i></h2>
				</div>
			</div>
			<div class="col-md-4">
			<form action="login.php" method="post" class="form-inline header-form">
				<div class="form-group">
					<input type="email" class="form-control input-sm" name="email" placeholder="e-Mail" value="<?php if (isset($_POST['email'])){ echo $_POST['email'];} ?>">
					<input type="password" class="form-control input-sm" name="password" placeholder="Password">
					<input type="submit" class="btn btn-default" name="log" value="Log in">
				</div>
				<?php if (isset($login_err)) { echo $login_err; } ?>
			</form>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="col-md-12">
			<?php
			if (!isset($regmsg)) {
			?>
			<h3>Register:</h3>
			<?php
			} else {
				echo $regmsg;
			}
			?>
			<form class="register-form" action="login.php" method="post">
				<div class="form-group">
					<label for="first_name">First Name</label>
					<input type="text" class="form-control input-sm" name="first_name" id="first_name" value="<?php if (isset($_POST['first_name'])) { echo $_POST['first_name']; } ?>">
				</div>
				<div class="form-group">
					<label for="last_name">Last Name</label>
					<input type="text" class="form-control input-sm" name="last_name" id="last_name" value="<?php if (isset($_POST['last_name'])) { echo $_POST['last_name']; } ?>">
					<?php if (isset($name_err)) { echo $name_err; } ?>
				</div>
				<div class="form-group">
					<label for="email">e-Mail</label>
					<input type="email" class="form-control input-sm" name="email" id="email" value="<?php if (isset($_POST['email'])) { echo $_POST['email']; } ?>">
					<?php if (isset($email_err)) { echo $email_err; } ?>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control input-sm" name="password" id="password" value="<?php if (isset($_POST['password'])) { echo $_POST['password']; } ?>">
					<?php if (isset($password_err)) { echo $password_err; } ?>
					<small class="form-text text-muted">The password should be at least 6 characters, must contain at least 1 uppercase letter, 1 lowercase letter and 1 number.</small>
				</div>
				<div class="form-group">
					<label for="password_re">Retype Password</label>
					<input type="password" class="form-control input-sm" name="password_re" id="password_re">
					<?php if (isset($password_err_re)) { echo $password_err_re; } ?>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-default" name="reg" value="Register">
				</div>
			</form>
		</div>
	</div>
	<div class="container-fluid footer">
		&copy; 2017 - My Tunes
	</div>
</body>
</html>