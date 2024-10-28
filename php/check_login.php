<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in via session or cookie
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Check if the cookie exists and log in the user if it does
    if (isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['logged_in'] = true;


    } else {
        // If neither session nor cookie indicates a logged-in state, redirect to login
        header("Location: /login.html");
        exit;
    }
}
?>