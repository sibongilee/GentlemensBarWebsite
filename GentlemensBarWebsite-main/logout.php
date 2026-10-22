<?php
// logout.php
session_start();

// Save the cart temporarily before wiping the session
$saved_cart = $_SESSION['cart'] ?? [];

// Clear user login session variables instead of destroying everything
unset($_SESSION['user_id']);
unset($_SESSION['user_email']);
unset($_SESSION['role']);

// Restore the cart so it doesn't get lost on logout
$_SESSION['cart'] = $saved_cart;

// Redirect to home or login page
header("Location: index.php");
exit();
?>