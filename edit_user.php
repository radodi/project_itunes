<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
}
include 'includes/db_connect.php';
include 'includes/functions.php';
if (isset($_GET['action'])) {
	default_user_image($conn);
}
if (isset($_FILES['fileToUpload'])) { upload_user_image($conn); }
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>My Tunes - Music sharing.</title>
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
</body>
	<div class="container-fluid header">
		<!-- header -->
		<div class="row">
			<div class="col-md-4">
				<div class="box">
					<a href="http://localhost/project_itunes/"><img src="img/logo.png" alt="My Tunes Logo"></a>
					<audio id="player"></audio>
				</div>
				<div class="box">
					<h1 class="name">My Tunes</h1>
					<h2 class="slogan">Music Sharing <i class="material-icons">headset</i></h2>
				</div>
			</div>
			<div class="col-md-4">
				<form class="form-inline header-form">
					<div class="form-group">
						<input class="form-control input-sm" type="text" name="search" placeholder=" Type name of song or artist.."> 
						<button class="btn btn-default btn-sm"><i class="material-icons ico18">search</i> Search</button>
					</div>
				</form>
				<ul class="mynav">
					<li><a href="http://localhost/project_itunes/">Library</a></li>
					<li><a href="add_song.php">Add Song</a></li>
					<li><a href="my_songs.php">My Songs</a></li>
				</ul>
			</div>
			<div class="col-md-4 user-col">
				<div class="box userinfo">
					<div>Hello <strong><?= $_SESSION['first_name']?> <?= $_SESSION['last_name']?></strong>.</div>
					<div><a href="edit_user.php"><i class="material-icons">person_pin</i>Edit my info</a></div>
					<div><a href="logout.php"><i class="material-icons">power_settings_new</i>Logout</a></div>
				</div>
				<div class="box thumbnail avatar">
					<img src="<?php show_user_image($conn); ?>" alt="User Picture">
				</div>
			</div>
		</div>
	</div>
	<div class="container">
	<div class="row">
		<div class="col-md-6">
			<form class="form white-form" action="edit_user.php" method="post" enctype="multipart/form-data">
			<div class="box thumbnail">
				<img src="<?php show_user_image($conn); ?>" alt="User Picture">
				<div class="caption">
					<a href="edit_user.php?action=delete"><i class="material-icons">delete</i> Delete</a>
				</div>
			</div>
			<div class="box">
				<h4>Change Picture</h4>
				<div class="form-group">
					<input class="form-control-file" type="file" name="fileToUpload" id="fileToUpload">
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-default" name="submit"><i class="material-icons">file_upload</i> Upload</button>
				</div>
			</div>
			</form>
			<?php
			if (isset($_POST['del'])) {
				delete_user($_POST['user_name'], $_POST['password'], $conn);
				unset($_POST);
			}
			?>
			<form class="white-form" action="edit_user.php" method="post">
				<h4>Delete Account</h4>
				<?php if (isset($del_err)) { echo $del_err; } ?>
				<div class="form-group">
						<label for="user_name">Type your User Name</label>
						<input type="text" class="form-control input-sm" name="user_name" id="user_name">
					<?php if (isset($uname_err)) { echo $uname_err; } ?>
				</div>
				<div class="form-group">
					<label for="password">Type your Password</label>
					<input type="password" class="form-control input-sm" name="password" id="password">
					<?php if (isset($pwd_err)) { echo $pwd_err; } ?>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-default" name="del"><i class="material-icons">delete</i> Delete</button>
				</div>
			</form>
			<?php
			if (isset($picture_err)) { echo $picture_err; unset($GLOBALS['picture_err']); }
			if (isset($_POST['upd'])) {
				update_user_info($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['password_re'], $_POST['user_name'], $conn, true);
				unset($_POST);
			}
			?>
		</div>
		<div class="col-md-6">
			<?php
			if (isset($regmsg)) {
				echo $regmsg;
			}
			?>
			<form class="white-form" action="edit_user.php" method="post">
			<h4>Update User info</h4>
				<div class="form-group">
					<label for="first_name">First Name</label>
					<input type="text" class="form-control input-sm" name="first_name" id="first_name" value="<?php if (isset($_SESSION['first_name'])) { echo $_SESSION['first_name']; } ?>">
				</div>
				<div class="form-group">
					<label for="last_name">Last Name</label>
					<input type="text" class="form-control input-sm" name="last_name" id="last_name" value="<?php if (isset($_SESSION['last_name'])) { echo $_SESSION['last_name']; } ?>">
					<?php if (isset($name_err)) { echo $name_err; } ?>
				</div>
				<div class="form-group">
					<label for="user_name">User Name</label>
					<input type="text" class="form-control input-sm" name="user_name" id="user_name" value="<?php if (isset($_SESSION['user_name'])) { echo $_SESSION['user_name']; } ?>">
					<?php if (isset($username_err)) { echo $username_err; } ?>
				</div>
				<div class="form-group">
					<label for="email">e-Mail</label>
					<input type="email" class="form-control input-sm" name="email" id="email" value="<?php if (isset($_SESSION['email'])) { echo $_SESSION['email']; } ?>">
					<?php if (isset($email_err)) { echo $email_err; } ?>
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input type="password" class="form-control input-sm" name="password" id="password" value="<?php if (isset($_SESSION['password'])) { echo $_SESSION['password']; } ?>">
					<?php if (isset($password_err)) { echo $password_err; } ?>
					<small class="form-text text-muted">The password should be at least 6 characters, must contain at least 1 uppercase letter, 1 lowercase letter and 1 number.</small>
				</div>
				<div class="form-group">
					<label for="password_re">Retype Password</label>
					<input type="password" class="form-control input-sm" name="password_re" id="password_re">
					<?php if (isset($password_err_re)) { echo $password_err_re; } ?>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-default" name="upd"><i class="material-icons">update</i> Update</button>
				</div>
			</form>
		</div>
		</div>
	</div>
	<div class="container-fluid footer">
		&copy; 2017 - My Tunes
	</div>
</html>