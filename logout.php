<?php
session_start();
session_unset();
session_destroy();
include 'includes/settings.php';
header('Location: ' . HOST_NAME . '');