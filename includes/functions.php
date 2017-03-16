<?php
//User Registration
function register_user($first_name, $last_name, $email, $password, $password_re, $conn){
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
		$q = "SELECT `email` FROM `users` WHERE `email` = '$email'";
    	$res = mysqli_query($conn, $q);
    	if (mysqli_num_rows($res) !==0) {
    		$GLOBALS['email_err'] = '<div class="msg"><i class="material-icons">error_outline</i> This e-mail address is already registered!</div>';
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
    	$q= "INSERT INTO `users`(`first_name`, `last_name`, `email`, `password`) VALUES ('$first_name', '$last_name', '$email', '" . md5($password) . "')";
    	if (mysqli_query($conn, $q)) {
    		$GLOBALS['regmsg'] = '<div class="msg"><i class="material-icons">	info_outline</i>The registration is successful. Please Login.</div>';
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