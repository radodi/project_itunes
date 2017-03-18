<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
}
include 'includes/db_connect.php';
include 'includes/functions.php';
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
		<!-- Songs Header -->
		<div class="row tracks">
		<?php 
		if (isset($_GET['order'])) {
			print_track_head($_GET['order'], $_GET['by']);
		} else {
			print_track_head('ASC', 'date');
		}
		?>
		</div>
		<!-- END Songs Header -->
		<!-- Song -->
		<div class="row track">
			<div class="box art">
				<div class="thumbnail">
					<img src="img/album_art.png" alt="Album Art">
				</div>
			</div>
			<div class="box">
				<div class="row">
					<div class="box b-r b-b song">Could I have</div>
					<div class="box b-r b-b artist"> Enrique</div>
					<div class="box b-r b-b date"> 2017-03-14</div>
					<div class="box b-r b-b user">radodi</div>
					<div class="box b-r b-b dw">325</div>
					<div class="box b-b rating">
						<?php show_rating(3, $conn) ?>
					</div>
				</div>
				<div class="row">
					<div class="box toggle">
						<i class="material-icons player" onclick="document.getElementById('player').src='audio/Whitney Houston - Could I Have This Kiss Forever audio.m4a';document.getElementById('player').load(); document.getElementById('player').play()">play_arrow</i>
						<i class="material-icons player" onclick="document.getElementById('player').pause();document.getElementById('player').currentTime = 0;">stop</i>
						<i class="material-icons player">cloud_download</i>
					</div>
					<div class="box toggle">
						<span class="inverse">
							<i class="material-icons player">star_rate</i>
							<i class="material-icons player">star_rate</i>
							<i class="material-icons player">star_rate</i>
							<i class="material-icons player">star_rate</i>
							<i class="material-icons player">star_rate</i>
						</span>
					</div>
				</div>
			</div>
		</div>
		<!-- END Song -->
	</div>
	<div class="container-fluid footer">
		&copy; 2017 - My Tunes 
	</div>
</body>
</html>