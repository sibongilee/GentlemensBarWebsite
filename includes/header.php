<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['bookings'])) { $_SESSION['bookings'] = []; }
if (!isset($_SESSION['payments'])) { $_SESSION['payments'] = []; }

$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? '';

// Identify the user's role using the presentation UIDs
$isAdmin = ($userId === 'S6XWLYukAaanstSPnIM8icQYEn53');
$isStaff = ($userId === 'ER9szZ3C2CcYqneC4L5sEIOx3Bf2' || $userId === 'SpdzcLf3ZWYpWI0yWB2aO6qAkBp2');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Gentlemen's Bar</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <!-- Firebase Scripts -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
    <script>
      if (!firebase.apps.length) {
          firebase.initializeApp({
              apiKey: "AIzaSyBceoCExF8pRVzOjWz0-WzyuduqbmdnSU0",
              authDomain: "thegentlemen-sbar.firebaseapp.com",
              projectId: "thegentlemen-sbar",
              storageBucket: "thegentlemen-sbar.firebasestorage.app"
          });
      }
      window.db = firebase.firestore();
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
        <?php if ($isAdmin): ?>
            <!-- ONLY ADMIN SEES THIS -->
            <a href="admin_dashboard.php">Master Schedule</a>
            <a href="logout.php">Logout</a>

        <?php elseif ($isStaff): ?>
            <!-- ONLY STAFF SEES THIS -->
            <a href="staff_dashboard.php">My Schedule</a>
            <a href="logout.php">Logout</a>

        <?php else: ?>
            <!-- ONLY CUSTOMERS (LOGGED IN OR OUT) SEE THIS -->
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
            🛒 Cart 
            <span class="cart-counter" style="background: #ff4d4d; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px;">
                <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
            </span>
            </a>
        <?php endif; ?>
    </nav>
</header>

<script>
function toggleMobileMenu() {
    const nav = document.querySelector('header nav');
    const btn = document.querySelector('.mobile-menu-btn');
    nav.classList.toggle('show');
    btn.innerHTML = nav.classList.contains('show') ? '✕' : '☰';
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