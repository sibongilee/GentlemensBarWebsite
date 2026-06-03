<!DOCTYPE html>
<html>
<head>
    <title>The Gentlemen's Bar</title>
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

<div class="hero">
    <img src="assets/logo.jpeg" class="hero-logo" alt="The Gentlemen's Bar Hero Image">
<h1>Premium Grooming Experience</h1>

<p>
    Book appointments, manage your bookings and enjoy professional grooming services.
</p>

<div class="hero-buttons">
    <a href="login.php" class="card-box">Login</a>
    <a href="register.php" class="card-box">Register</a>
    <a href="booking.php" class="card-box">Book Appointment</a>
</div>


</div>
<?php include 'footer.php'; ?>
</body>
</html>
