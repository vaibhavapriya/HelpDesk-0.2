<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Optionally remove the JWT token cookie if set
if (isset($_COOKIE['jwt_token'])) {
    setcookie('jwt_token', '', time() - 3600, '/'); // delete the cookie
}

// Redirect to login or home page
header("Location: /HelpDesk-0.2/login"); // or "home" or wherever you want
exit();
?>
