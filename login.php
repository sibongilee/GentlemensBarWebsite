<?php
// PHP Redirect Logic at the very top prevents the "headers already sent" error
if(isset($_POST['login'])){
    header("Location: dashboard.php");
    exit();
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
        <a href="booking.php">Book Appointment</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="login.php">Login</a>
    </nav>
</header>
<div class="card">
        <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="logo">
        
        <h2>Customer Login</h2>

        <form method="POST">
            <input type="email" placeholder="Email Address" required>
            <input type="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>

        <p style="margin-top: 15px; font-size: 14px;">
            Don't have an account? 
            <a href="register.php" style="font-weight: bold;">Register Here</a>
        </p>
    </div>
</body>
<?php include 'footer.php'; ?>
</html>

