<?php
// login.php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        // Store user info in session
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = explode('@', $email)[0];
        $_SESSION['user_email'] = $email;
        $_SESSION['user_phone'] = '+27 71 234 5678';
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Please enter both email and password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | The Gentlemen's Bar</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="assets/logo.jpeg" class="logo-small" alt="The Gentlemen's Bar Logo">
            <span class="brand-name">The Gentlemen's Bar</span>
        </div>
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
        <nav>
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="booking_appointment.php">Book Appointment</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <div class="card">
        <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="logo">
        <h2>Customer Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <p style="margin-top: 15px; font-size: 14px; color: #888;">
            Don't have an account? 
            <a href="register.php" style="font-weight: bold;">Register Here</a>
        </p>
    </div>

    <script>
    function toggleMobileMenu() {
        const nav = document.querySelector('header nav');
        nav.classList.toggle('show');
    }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
