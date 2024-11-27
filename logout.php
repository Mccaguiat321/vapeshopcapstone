<?php
session_start();

// Set the logout message
$_SESSION['invemessage'] = "Logout Successfully";

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the homepage (or another page)
header("Location: index.php");
exit();
