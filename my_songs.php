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
if (isset($_FILES['art'])) { upload_art($_POST['song_id'], $conn); }
include 'includes/assets/header.php';
?>
	<div class="container">
	<?php
	if (isset($GLOBALS['picture_err'])) { echo $GLOBALS['picture_err']; unset($GLOBALS['picture_err']); }
	show_my_songs($conn);
	?>
	</div>
	<?php include 'includes/assets/footer.php'; 