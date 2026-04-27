<?php
// admin-logout.php
// Handle admin logout

session_start();
session_destroy();

header('Location: admin-login.php');
exit;
?>
