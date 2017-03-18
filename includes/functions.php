<?php
//User Registration
function update_user_info($first_name, $last_name, $email, $password, $password_re, $username, $conn, $update = false){
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
			$q = "SELECT `email` FROM `users` WHERE `email` = '$email' AND `date_deleted` IS NULL";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) !==0) {
				$error = true;
				$GLOBALS['email_err'] = '<div class="msg"><i class="material-icons">error_outline</i> E-mail address: ' . $email . ' is already registered!</div>';
			}
		} else {
			$q = "SELECT `email` FROM `users` WHERE `email` = '$email' AND `user_id` != " . $_SESSION['user_id'] . " AND `date_deleted` IS NULL";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) !==0) {
				$error = true;
				$GLOBALS['email_err'] = '<div class="msg"><i class="material-icons">error_outline</i> E-mail address: ' . $email . ' is already registered!</div>';
			}
		}
	}
	if(!preg_match('/^[a-zA-Z0-9]+$/', $username) || strlen($username) < 4 ) {
		$error = true;
		$GLOBALS['username_err'] = '<div class="msg"><i class="material-icons">error_outline</i>The username should be at least 4 characters, can contain letters and numbers</div>';
	} else {
		unset($GLOBALS['username_err']);
		if ($update == true) {
			$q = "SELECT `user_name` FROM `users` WHERE `user_name` = '$username' AND `user_id` != " . $_SESSION['user_id'] . "";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) !==0) {
				$error = true;
				$GLOBALS['username_err'] = '<div class="msg"><i class="material-icons">error_outline</i> The username ' . $username . ' is not available!</div>';
			}
		 } else {
			$q = "SELECT `user_name` FROM `users` WHERE `user_name` = '$username'";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) !==0) {
				$error = true;
				$GLOBALS['username_err'] = '<div class="msg"><i class="material-icons">error_outline</i> The username ' . $username . ' is not available!</div>';
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
			$q = "UPDATE `users` SET `first_name`='$first_name',`last_name`='$last_name',`user_name`='$username',`email`='$email',`password`='" . md5($password) . "' WHERE `user_id`= '$user_id' AND `date_deleted` IS NULL";
		} else {
			$q= "INSERT INTO `users`(`first_name`, `last_name`, `user_name`, `email`, `password`) VALUES ('$first_name', '$last_name', '$username', '$email', '" . md5($password) . "')";
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
				$_SESSION['user_name'] = $row['user_name'];
				$_SESSION['email'] = $row['email'];
			}
		} else {
			$GLOBALS['regmsg'] = '<div class="msg"><i class="material-icons">info_outline</i>ERROR: Contact the system administrator!</div>';
		}
	}
}

//USER LOG IN
function login_user($email, $password, $conn){
	//check e-mail
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = true;
		$GLOBALS['login_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Invalid email format!</div>';
	} else {
		unset($GLOBALS['email_err']);
		$q = "SELECT `email` FROM `users` WHERE `email` = '$email' AND `date_deleted` IS NULL";
		$res = mysqli_query($conn, $q);
		if (mysqli_num_rows($res) == 0) {
			$GLOBALS['login_err'] = '<div class="msg"><i class="material-icons">error_outline</i> This e-mail address is not registered!</div>';
		} else {
			$q = "SELECT `user_id`, `first_name`, `last_name`, `user_name`, `email`, `password`, `picture`, `date_deleted` FROM `users` WHERE `email` = '$email' AND `password` = '" . md5($password) . "' AND `date_deleted`IS NULL";
			$res = mysqli_query($conn, $q);
			if (mysqli_num_rows($res) == 0) {
				$GLOBALS['login_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Wrong password!</div>';
			} else {
				$row = mysqli_fetch_assoc($res);
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['first_name'] = $row['first_name'];
				$_SESSION['last_name'] = $row['last_name'];
				$_SESSION['user_name'] = $row['user_name'];
				$_SESSION['email'] = $row['email'];
				header('Location: http://localhost/project_itunes/');
			}
		}
	}
}
//DELETE USER
function delete_user($user_name, $password, $conn){
	//check empty username and pass 
	if (empty($user_name)) {
		$error = true;
		$GLOBALS['uname_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Enter Username!</div>';
	} 
	if (empty($password)) {
		$error = true;
		$GLOBALS['pwd_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Enter Password!</div>';
	}
	//check username and pass match and delete accoumt
	if (!isset($error)) {
		$q = "SELECT * FROM `users` WHERE `user_name` = '$user_name' AND `password` = '". md5($password) . "' AND `user_id` = '" . $_SESSION['user_id'] . "' AND `date_deleted` is NULL";
		$res = mysqli_query($conn, $q);
		if (mysqli_num_rows($res) !== 0) {
			$today = date('Y-m-d');
			$q = "UPDATE `users` SET `date_deleted`= '$today' WHERE `user_id` = '" . $_SESSION['user_id'] . "' AND `user_name` = '$user_name' AND `password`='" . md5($password) . "' AND `date_deleted` IS NULL";
			$res = mysqli_query($conn, $q);
			header('Location: logout.php');
		} else {
			$GLOBALS['del_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Username and password not match!</div>';
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

//ADD ARTIST
function add_artist($artist_name, $conn) {
	//check lenght
	if (strlen($artist_name) > 60) {
		$GLOBALS['artist_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Artist Name can be max <strong>60</strong> characters!</div>';
		$error = true;
	}
	//check if exists
	$q = "SELECT * FROM `artists` WHERE `artist_name`='$artist_name' AND `date_deleted` IS NULL";
	$res = mysqli_query($conn, $q);
	if (mysqli_num_rows($res) !== 0) {
		$GLOBALS['artist_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Artist <strong>' . $artist_name . '</strong> already exists!</div>';
	} else {
		if (!isset($error)) {
			$q = "INSERT INTO `artists` (`artist_name`) VALUES ('$artist_name')";
			if (mysqli_query($conn, $q)) {
				$GLOBALS['artist_msg'] = '<div class="msg"><i class="material-icons">error_outline</i> Artist <strong>' . $artist_name . '</strong> added successful!</div>';
			}
		}
	}
}
//ADD SONG
function upload_song($conn) {
	$target_dir = "audio/";
	$target_file = $target_dir . basename($_FILES["mp3"]["name"]);
	$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
	if (empty($_POST['song_name'])) {
		$error = true;
		$GLOBALS['song_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Enter song title!</div>';
	}
	if ($_POST['artist'] == "") {
		$error = true;
		$GLOBALS['artist_err'] = '<div class="msg"><i class="material-icons">error_outline</i> Select an artist!</div>';
	}
	// Check file size
	if ($_FILES["mp3"]["size"] > 102400000) {
		$GLOBALS['file_err'] = '<div class="msg"><i class="material-icons">error_outline</i>Sorry, your file is too large!</div>';
		$error = true;
	}
	// Allow certain file formats
	if($fileType != "mp3") {
		$GLOBALS['file_err'] = '<div class="msg"><i class="material-icons">error_outline</i>Sorry, only MP3 files are allowed.!</div>';
		$error = true;
	}
	if (isset($error)) {
	$GLOBALS['song_msg'] = '<div class="msg"><i class="material-icons">error_outline</i>Sorry, your file was not uploaded!</div>';
	// if everything is ok, try to upload file
	} else {
		$target_file = $target_dir . date('YmdHis') . "." . $fileType;
		if (move_uploaded_file($_FILES["mp3"]["tmp_name"], $target_file)) {
			$today = date('Y-m-d');
			$q = "INSERT INTO `songs`(`song_name`, `artist_id`, `upload_date`, `user_id`, `song_url`) VALUES ('" . $_POST['song_name'] . "', '" . $_POST['artist'] . "', '$today', '" . $_SESSION['user_id'] . "', '$target_file')";
			mysqli_query($conn, $q);
			$GLOBALS['song_msg'] = '<div class="msg"><i class="material-icons">error_outline</i> The file has been uploaded successfully.</div>';
		} else {
			$GLOBALS['song_msg'] = '<div class="msg"><i class="material-icons">error_outline</i> Sorry, your file was not uploaded!</div>';
		}
	}
}

//Print SONGS HEADER
function print_track_head($order, $by){
	if ($order == 'ASC') {
		switch ($by) {
			case 'song':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'artist':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESCesc&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'user':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'downloads':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'rating':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			default:
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
		}
	} else {
		switch ($by) {
			case 'song':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'artist':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'user':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'downloads':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			case 'rating':
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
			default:
			echo '
			<div class="box b-r art">
				<i class="material-icons">blur_on</i> Album art
			</div>
			<div class="box b-r song">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=song"><i class="material-icons">headset</i> Song</a>
			</div>
			<div class="box b-r artist">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=artist"><i class="material-icons">album</i> Artist</a>
			</div>
			<div class="box b-r date">
				<a class="sort_dl" href="http://localhost/project_itunes/?order=ASC&by=date"><i class="material-icons">event</i> Date</a>
			</div>
			<div class="box b-r user">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=user"><i class="material-icons">person_pin</i> User</a>
			</div>
			<div class="box b-r dw">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=downloads"><i class="material-icons">cloud_download</i> Downloads</a>
			</div>
			<div class="box rating">
				<a class="sort_up" href="http://localhost/project_itunes/?order=DESC&by=rating"><i class="material-icons">favorite_border</i> Rating</a>
			</div>
			';
			break;
		}
	}
}
//Print RATING FUNCTION
function show_rating($song_id, $conn){
	$q = "SELECT `average_rate` FROM `songs` WHERE `song_id` = '$song_id'";
	$res = mysqli_query($conn, $q);
	$row = mysqli_fetch_assoc($res);
	$rate = $row['average_rate'];
	$print_rate = '';
	for ($i=0; $i < 5; $i++) { 
		if ($rate >= $i+1) {
			$print_rate .='<i class="material-icons">star_rate</i>';
		} else {
			$print_rate .='<i class="material-icons grey">star_rate</i>';
		}
	}
	$GLOBALS['print_rate'] = $print_rate;
}
//Print SONGS FUNCTION
function show_songs($order, $by, $conn){
	switch ($order) {
		case 'DESC':
			switch ($by) {
				case 'song':
				$q = "SELECT * FROM songs
				JOIN artists ON songs.artist_id = artists.artist_id
				JOIN users ON songs.user_id = users.user_id
				WHERE songs.date_deleted IS NULL ORDER BY `song_name` DESC";
				$res = mysqli_query($conn, $q);
				if (mysqli_num_rows($res) !== 0) {
					while ($row = mysqli_fetch_assoc($res)) {
						echo '<div class="row track">
			<div class="box art">
				<div class="thumbnail">
					<img src="' . $row['album_art'] . '" alt="Album Art">
				</div>
			</div>
			<div class="box">
				<div class="row">
					<div class="box b-r b-b song">' . $row['song_name'] . '</div>
					<div class="box b-r b-b artist">' . $row['artist_name'] . '</div>
					<div class="box b-r b-b date">' . $row['upload_date'] . '</div>
					<div class="box b-r b-b user">' . $row['user_name'] . '</div>
					<div class="box b-r b-b dw">' . $row['downloads'] . '</div>
					<div class="box b-b rating">' . show_rating($row['song_id'], $conn) . $GLOBALS['print_rate'] . '</div>
				</div>
				<div class="row">
					<div class="box toggle">
						<i class="material-icons player" onclick="document.getElementById(\'player\').src=\'' .$row['song_url'] . '\';document.getElementById(\'player\').load(); document.getElementById(\'player\').play()">play_arrow</i>
						<i class="material-icons player" onclick="document.getElementById(\'player\').pause();document.getElementById(\'player\').currentTime = 0;">stop</i>
						<i class="material-icons player">cloud_download</i>
					</div>
					<div class="box toggle">
						<span class="inverse">
							<a href="http://localhost/project_itunes/?ratesong=5"><i class="material-icons player">star_rate</i></a>
							<a href="http://localhost/project_itunes/?ratesong=4"><i class="material-icons player">star_rate</i></a>
							<a href="http://localhost/project_itunes/?ratesong=3"><i class="material-icons player">star_rate</i></a>
							<a href="http://localhost/project_itunes/?ratesong=2"><i class="material-icons player">star_rate</i></a>
							<a href="http://localhost/project_itunes/?ratesong=1"><i class="material-icons player">star_rate</i></a>
						</span>
					</div>
				</div>
			</div>
		</div>';
					}
				}
					break;
				case 'artist':
				$q = "SELECT * FROM songs
				JOIN artists ON songs.artist_id = artists.artist_id
				JOIN users ON songs.user_id = users.user_id
				WHERE songs.date_deleted IS NULL ORDER BY `artist_name` DESC";
					break;
				case 'user':
				$q = "SELECT * FROM songs
				JOIN artists ON songs.artist_id = artists.artist_id
				JOIN users ON songs.user_id = users.user_id
				WHERE songs.date_deleted IS NULL ORDER BY `user_name` DESC";
					break;
				case 'downloads':
				$q = "SELECT * FROM songs
				JOIN artists ON songs.artist_id = artists.artist_id
				JOIN users ON songs.user_id = users.user_id
				WHERE songs.date_deleted IS NULL ORDER BY `downloads` DESC";
					break;
				case 'rating':
				$q = "SELECT * FROM songs
				JOIN artists ON songs.artist_id = artists.artist_id
				JOIN users ON songs.user_id = users.user_id
				WHERE songs.date_deleted IS NULL ORDER BY `average_rate` DESC";
					break;
				default:
				$q = "SELECT * FROM songs
				JOIN artists ON songs.artist_id = artists.artist_id
				JOIN users ON songs.user_id = users.user_id
				WHERE songs.date_deleted IS NULL ORDER BY `upload_date` DESC";
					break;
			}
			break;
		default:
			
			break;
	}
}