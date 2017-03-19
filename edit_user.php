<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
}
include 'includes/settings.php';
include 'includes/db_connect.php';
include 'includes/functions.php';
if (isset($_GET['action'])) {
	default_user_image($conn);
}
if (isset($_FILES['fileToUpload'])) { upload_user_image($conn); }
include 'includes/assets/header.php';
?>
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
	<?php include 'includes/assets/footer.php'; 