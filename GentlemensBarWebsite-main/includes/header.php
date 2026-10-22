<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <!-- Firebase App (Core SDK) & Firestore -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>

    <script>
      // Initialize Firebase using your actual project credentials
      if (!firebase.apps.length) {
          firebase.initializeApp({
              apiKey: "AIzaSyBceoCExF8pRVzOjWz0-WzyuduqbmdnSU0",
              authDomain: "thegentlemen-sbar.firebaseapp.com",
              projectId: "thegentlemen-sbar",
              storageBucket: "thegentlemen-sbar.firebasestorage.app"
          });
      }
      const db = firebase.firestore();
      const auth = firebase.auth();
      window.db = db;
    </script>
</head>
<body>
<header>
    <div class="logo-container">
        <img src="Assets/logo.jpeg" class="logo-small" alt="The Gentlemen's Bar Logo">
        <span class="brand-name">The Gentlemen's Bar</span>
    </div>
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
    <nav>
        <a href="index.php">Home</a>
        <a href="services.php">Services</a>
        <a href="booking_appointment.php">Book Appointment</a>
        <a href="shop.php">Shop</a>
        <?php if ($isLoggedIn): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="my_bookings.php">My Bookings</a>
            <a href="payment.php">Payments</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
        <a href="cart.php" class="cart-nav-link">
       &#x1F6D2;Cart 
        <span class="cart-counter" style="background: #ff4d4d; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px;">
            <?php 
                if (isset($_SESSION['cart'])) {
                    echo count($_SESSION['cart']);
                } else {
                    echo 0;
                }
            ?>
        </span>
    </a>
    </nav>
</header>

<script>
function toggleMobileMenu() {
    const nav = document.querySelector('header nav');
    const btn = document.querySelector('.mobile-menu-btn');
    nav.classList.toggle('show');
    
    if (nav.classList.contains('show')) {
        btn.innerHTML = '✕';
    } else {
        btn.innerHTML = '☰';
    }
}

document.querySelectorAll('header nav a').forEach(link => {
    link.addEventListener('click', () => {
        const nav = document.querySelector('header nav');
        const btn = document.querySelector('.mobile-menu-btn');
        if (nav.classList.contains('show')) {
            nav.classList.remove('show');
            btn.innerHTML = '☰';
        }
    });
});
</script>