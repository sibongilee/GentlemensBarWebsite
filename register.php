<!DOCTYPE html>
<html>
<head>
    <title>Register | The Gentlemen's Bar</title>
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
<div class="page-top-brand">
    <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="hero-logo">
</div>

<div class="card">
    <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="logo">
    
    <h2>Create Account</h2>
    <p class="tagline">First-class grooming starts here.</p>
        
    <form method="POST">
        <input type="text" placeholder="Full Name" required>
        <input type="email" placeholder="Email Address" required>
        <input type="text" placeholder="Phone Number" required>
        <input type="password" placeholder="Password" required>
        
        <button type="submit" name="register">Register</button>
    </form>

    <p style="margin-top: 15px; font-size: 14px;">
        Already have an account? 
        <a href="login.php" style="font-weight: bold;">Login Here</a>
    </p>
</div>
<?php
if(isset($_POST['register'])){
    echo "<script>alert('Registration successful'); window.location='login.php';</script>";
}
?>

</body>
<?php include 'footer.php'; ?>
</html>

