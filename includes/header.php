<?php
// includes/header.php
session_start();

// services
$services = [
    1 => ['id' => 1, 'name' => 'Classic Haircut', 'description' => 'Traditional scissor and clipper cut with hot towel finish', 'duration' => 30, 'price' => 665.00],
    2 => ['id' => 2, 'name' => 'Beard Trim & Shape', 'description' => 'Expert beard shaping and trimming with oil application', 'duration' => 20, 'price' => 475.00],
    3 => ['id' => 3, 'name' => 'Hot Towel Shave', 'description' => 'Traditional straight razor shave with hot towels', 'duration' => 25, 'price' => 570.00],
    4 => ['id' => 4, 'name' => 'Executive Package', 'description' => 'Haircut + Beard Trim + Hot Towel Shave', 'duration' => 60, 'price' => 1425.00],
    5 => ['id' => 5, 'name' => 'Hair Styling', 'description' => 'Professional styling with premium products', 'duration' => 20, 'price' => 380.00],
    6 => ['id' => 6, 'name' => 'Head Massage', 'description' => 'Relaxing head and scalp massage', 'duration' => 15, 'price' => 285.00],
    ];

// Initialize user bookings array in session if not exists
if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [];
}

// Initialize payments array in session if not exists
if (!isset($_SESSION['payments'])) {
    $_SESSION['payments'] = [];
}

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Gentlemen's Bar</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
<header>
    <div class="logo-container">
        <img src="assets/logo.jpeg" class="logo-small" alt="The Gentlemen's Bar Logo">
        <span class="brand-name">The Gentlemen's Bar</span>
    </div>
    <nav>
        <a href="index.php">Home</a>
        <a href="services.php">Services</a>
        <a href="booking_appointment.php">Book Appointment</a>
        <?php if ($isLoggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="bookings.php">My Bookings</a>
            <a href="payment.php">Payments</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>