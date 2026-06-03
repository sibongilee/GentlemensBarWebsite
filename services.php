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
        <a href="booking.php">Book Appointment</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="login.php">Login</a>
    </nav>
</header>
<div class="hero">
        <img src="assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="logo">
        
        <h2>Our Services</h2>

        <p>
            At The Gentlemen's Bar, we offer a range of premium grooming services including:
        </p>
        <p>
            Book your appointment today and experience the finest grooming services in town!
        </p>
    </div>
<?php include 'footer.php'; ?>
</body>
</html>