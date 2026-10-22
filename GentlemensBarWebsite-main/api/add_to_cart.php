<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $product_name = $_POST['product_name'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $product_image = $_POST['product_image'] ?? ''; // Capture the image path

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add item including the image to the session cart
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'image' => $product_image // Save image path
    ];

    header("Location: ../shop.php?added=" . urlencode($product_name));
    exit();
}
?>