<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header('Location: login.php');
}
include 'includes/settings.php';
include 'includes/db_connect.php';
include 'includes/functions.php';
if (isset($_GET['ratesong'])) {
	rate_song($_GET['ratesong'], $_GET['song_id'], $_SESSION['user_id'] , $conn);
}
if (isset($_GET['dw'])) {
	download_file($_GET['dw'], $conn);
}
include 'includes/assets/header.php';
?>
	<div class="container">
		<!-- Songs Header -->
		<?php 
			if (isset($_GET['search'])) {
				search($_GET['search'], $conn);
			} else {
				?>
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
			<!-- Songs -->
		<?php 
			if (isset($_GET['order'])) {
				show_songs($_GET['order'], $_GET['by'], $conn);
			} else {
				show_songs('DESC', 'date', $conn);
			}
			}
		?>
		<!-- END Songs -->
	</div>
	<?php include 'includes/assets/footer.php'; 