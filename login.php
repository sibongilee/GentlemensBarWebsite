<?php

session_start();

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    
    if (!empty($email) && !empty($password)) {
        // Store user info in session
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = $full_name;
        $_SESSION['user_email'] = $email;
        
        // Redirect to dashboard
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
    </div>
        <span class="brand-name">The Gentlemen's Bar</span>
    <nav>
        <a href="index.php">Home</a>
        <a href="services.php">Services</a>
        <a href="booking_appointment.php">Book Appointment</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="login.php">Login</a>
    </nav>
</header>
<div class="card">
        <h2>Customer Login</h2>

        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="login">Login</button>
        </form>

        <p style="margin-top: 15px; font-size: 14px; color: #888;">
            Don't have an account? 
            <a href="register.php" style="font-weight: bold;">Register Here</a>
        </p>
    </div>
</body>
<?php include 'footer.php'; ?>
</html>

