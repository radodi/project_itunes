<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
}
include 'includes/settings.php';
include 'includes/db_connect.php';
include 'includes/functions.php';
include 'includes/assets/header.php';
?>
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
	<?php include 'includes/assets/footer.php'; 