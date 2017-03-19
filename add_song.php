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
		<div class="col-md-6">
			<?php if (isset($_POST['add_artist'])) { add_artist($_POST['artist_name'], $conn); } ?>
			<form action="add_song.php" method="post" class="form white-form">
				<h4>Add Artist</h4>
				<?php if (isset($artist_msg)) { echo $artist_msg; } ?>
				<div class="form-group">
					<label>Artist Name:
					<input type="text" name="artist_name" class="form-control">
					</label>
				<?php if (isset($artist_err)) { echo $artist_err; } ?>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-default" name="add_artist"><i class="material-icons">add_circle_outline</i> Add</button>
				</div>
			</form>
		</div>
		<div class="col-md-6">
		<?php if (isset($_POST['add_song'])) { upload_song($conn); } ?>
			<form action="add_song.php" method="post" enctype="multipart/form-data" class="form white-form">
				<h4>Add Song</h4>
				<?php if (isset($song_msg)) { echo $song_msg; } ?>
				<div class="form-group">
					<label>Song Name:
					<input type="text" name="song_name" class="form-control">
					</label>
					<?php if (isset($song_err)) { echo $song_err; } ?>
				</div>
				<div class="form-group">
					<label>Artist:
					<select name="artist" class="form-control">
						<option value=""> --Select-- </option>
						<?php
						$q = "SELECT * FROM `artists` WHERE 1 ORDER BY `artist_name` ASC";
						$res = mysqli_query($conn, $q);
						if (mysqli_num_rows($res) > 0) {
							while ($row = mysqli_fetch_assoc($res)) {
								echo '<option value="' . $row['artist_id'] . '">' . $row['artist_name'] . '</option>'. "\n\t\t\t\t\t\t";
							}					
						}
						?>	
					</select>
					</label>
					<?php if (isset($artist_err)) { echo $artist_err; } ?>
				</div>
				<div class="form-group">
					<label>MP3 file:
						<input type="file" name="mp3" id="mp3" class="form-control-file">
					</label>
					<?php if (isset($file_err)) { echo $file_err; } ?>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-default" name="add_song"><i class="material-icons">add_circle_outline</i> Add</button>
				</div>
			</form>
		</div>
	</div>
	<div class="container-fluid footer">
		&copy; 2017 - My Tunes
	</div>
</body>
</html>