<?php
session_start(); // Start session so we can destroy it

// ── Clear all session variables ──
$_SESSION = [];

// ── Destroy the session cookie (clean browser state) ──
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ── Destroy the session on the server ──
session_destroy();

// ── Redirect to login page ──
header('Location: login.php');
exit;
?>
