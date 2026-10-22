<?php
// api/remove_from_cart.php
session_start();

if (isset($_GET['index'])) {
    $index = $_GET['index'];
    
    // Check if the cart session exists and the item index is valid
    if (isset($_SESSION['cart'][$index])) {
        // Remove the item from the array
        unset($_SESSION['cart'][$index]);
        
        // Re-index the array so the indices stay clean
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Redirect back to the cart page
header("Location: ../cart.php");
exit();
?>