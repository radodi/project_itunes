<?php
//User Registration
function update_user_info($first_name, $last_name, $email, $password, $password_re, $conn, $update = false){
	if (!preg_match('/^[a-zA-Z]+$/', $first_name) || !preg_match('/^[a-zA-Z]+$/', $last_name)) {
		$error = true;
		$GLOBALS['name_err'] = '<div class="msg"><i class="material-icons">error_outline</i> First and Last name can contain only letters!</div>';
	} else {
		unset($GLOBALS['name_err']);
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = true;
		$GLOBALS['email_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Invalid email format!</div>';
	} else {
		unset($GLOBALS['email_err']);
		if ($update == false) {
			$q = "SELECT `email` FROM `users` WHERE `email` = '$email'";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) !==0) {
				$GLOBALS['email_err'] = '<div class="msg"><i class="material-icons">error_outline</i> E-mail address: ' . $email . ' is already registered!</div>';
			}
		} else {
			$q = "SELECT `email` FROM `users` WHERE `email` = '$email' AND`user_id` != " . $_SESSION['user_id'] . "";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) !==0) {
				$GLOBALS['email_err'] = '<div class="msg"><i class="material-icons">error_outline</i> E-mail address: ' . $email . ' is already registered!</div>';
			}
		}
	}
	if(!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || strlen($password) < 6 ) {
		$error = true;
		$GLOBALS['password_err'] = '<div class="msg"><i class="material-icons">error_outline</i>The password does not meet security criteria!</div>';
	} else {
		unset($GLOBALS['password_err']);
	}
	if($password != $password_re) {
		$error = true;
		$GLOBALS['password_err_re'] = '<div class="msg"><i class="material-icons">error_outline</i> The passwords do not match!</div>';
	} else {
		unset($GLOBALS['password_err_re']);
	}
	if (!isset($error)) {
		if (isset($_SESSION['user_id'])) {
			$user_id = $_SESSION['user_id'];
			$q = "UPDATE `users` SET `first_name`='$first_name',`last_name`='$last_name',`email`='$email',`password`='" . md5($password) . "' WHERE `user_id`= '$user_id' AND `date_deleted` IS NULL";
		} else {
			$q= "INSERT INTO `users`(`first_name`, `last_name`, `email`, `password`) VALUES ('$first_name', '$last_name', '$email', '" . md5($password) . "')";
		}
		if (mysqli_query($conn, $q)) {
			if ($update == false) {
				$GLOBALS['regmsg'] = '<div class="msg"><i class="material-icons">info_outline</i>The registration is successful. Please Login.</div>';
			} else {
				$GLOBALS['regmsg'] = '<div class="msg"><i class="material-icons">info_outline</i>User Info is updated successful.</div>';
				$q = "SELECT * FROM `users` WHERE `user_id` = '$user_id'";
				$res = mysqli_query($conn, $q);
				$row = mysqli_fetch_assoc($res);
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['first_name'] = $row['first_name'];
				$_SESSION['last_name'] = $row['last_name'];
				$_SESSION['email'] = $row['email'];
			}
		}
	}
}

//User Log in
function login_user($email, $password, $conn){
	//check e-mail
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = true;
		$GLOBALS['login_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Invalid email format!</div>';
	} else {
		unset($GLOBALS['email_err']);
		$q = "SELECT `email` FROM `users` WHERE `email` = '$email'";
		$res = mysqli_query($conn, $q);
		if (mysqli_num_rows($res) == 0) {
			$GLOBALS['login_err'] = '<div class="msg"><i class="material-icons">error_outline</i> This e-mail address is not registered!</div>';
		} else {
			$q = "SELECT `user_id`, `first_name`, `last_name`, `email`, `password`, `picture`, `date_deleted` FROM `users` WHERE `email` = '$email' AND `password` = '" . md5($password) . "' AND `date_deleted`IS NULL";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) == 0) {
				$GLOBALS['login_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Wrong password!</div>';
			} else {
				$row = mysqli_fetch_assoc($res);
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['first_name'] = $row['first_name'];
				$_SESSION['last_name'] = $row['last_name'];
				$_SESSION['email'] = $row['email'];
				header('Location: http://localhost/project_itunes/');
			}
		}
	}
}
//RESIZE IMAGE
function resize_image($thumbSize, $target_file) {
//Get Mime 
	$info = getimagesize($target_file);
	$mime = $info['mime'];
	switch ($mime) {
		case 'image/jpeg':
		$image_create_func = 'imagecreatefromjpeg';
		$image_save_func = 'imagejpeg';
		$new_image_ext = 'jpg';
		break;

		case 'image/png':
		$image_create_func = 'imagecreatefrompng';
		$image_save_func = 'imagepng';
		$new_image_ext = 'png';
		break;

		case 'image/gif':
		$image_create_func = 'imagecreatefromgif';
		$image_save_func = 'imagegif';
		$new_image_ext = 'gif';
		break;

		default: 
		throw new Exception('Unknown image type.');
	}

//getting the image dimensions
	list($width, $height) = getimagesize($target_file);
//saving the image into memory (for manipulation with GD Library)
	$myImage = $image_create_func($target_file);
// calculating the part of the image to use for thumbnail
	if ($width > $height) {
		$y = 0;
		$x = ($width - $height) / 2;
		$smallestSide = $height;
	} else {
		$x = 0;
		$y = ($height - $width) / 2;
		$smallestSide = $width;
	}
// copying the part into thumbnail
	$thumb = imagecreatetruecolor($thumbSize, $thumbSize);
	imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);
	$image_save_func($thumb, $target_file);
	return true;
}
//END RESIZE IMAGE

//IMAGE UPLOAD
function upload_user_image($conn){
	$target_dir = "img/users/" . $_SESSION['user_id'] . "/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
	if(isset($_POST["submit"]) && isset($_FILES['file'])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			$GLOBALS['picture_err'] = '<div class="msg"><i class="material-icons">error_outline</i>File is not an image!</div>';
			$uploadOk = 0;
		}
	}
// Check file size
	if ($_FILES["fileToUpload"]["size"] > 61440000) {
		$GLOBALS['picture_err'] .= '<div class="msg"><i class="material-icons">error_outline</i>Sorry, your file is too large!</div>';
		$uploadOk = 0;
	}
// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
		$GLOBALS['picture_err'] = '<div class="msg"><i class="material-icons">error_outline</i>Sorry, only JPG, JPEG, PNG & GIF files are allowed.!</div>';
	$uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
	$GLOBALS['picture_err'] .= '<div class="msg"><i class="material-icons">error_outline</i>Sorry, your file was not uploaded!</div>';
// if everything is ok, try to upload file
} else {
	if (!file_exists($target_dir)) {
		mkdir($target_dir, 0700);
	}
	$target_file = $target_dir . date('YmdHis') . "." . $imageFileType;
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		if(resize_image(100, $target_file)) {
			$q = "UPDATE `users` SET `picture`='$target_file' WHERE `user_id`='" . $_SESSION['user_id'] . "'";
			mysqli_query($conn, $q);
			header('Location: http://localhost/project_itunes/edit_user.php');
		} else {
			$GLOBALS['picture_err'] = '<div class="msg"><i class="material-icons">error_outline</i>Error Resize Image!</div>';
		}

	} else {
		$GLOBALS['picture_err'] = '<div class="msg"><i class="material-icons">error_outline</i>Sorry, your file was not uploaded!</div>';
	}
}
}
//END IMAGE UPLOAD

// SHOW USER_IMAGE
function show_user_image($conn) {
	$q = "SELECT `picture` FROM `users` WHERE `user_id` ='" . $_SESSION['user_id'] . "'";
	$res = mysqli_query($conn, $q);
	$row = mysqli_fetch_assoc($res);
	echo $row['picture'];
}
//SET USER_IMAGE to DEFAULT
function default_user_image($conn){
	$q = "UPDATE `users` SET `picture`='img/user_default.png' WHERE `user_id`= '" . $_SESSION['user_id'] . "'";
	mysqli_query($conn, $q);
	unset($_GET);
	header('Location: edit_user.php');
}