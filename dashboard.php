<!DOCTYPE html>
<html>

<head>
    <title>Dashboard | The Gentlemen's Bar</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="dashboard">
        <h1>Welcome to Your Dashboard</h1>
        <p>
            Manage your appointments, view services, and handle payments all in one place.
        </p>
        </div>
        
        <div class="cards">

            <a href="booking.php" class="card-box">
                <h3>Book Appointment</h3>
                <p>Schedule a new appointment.</p>
            </a>

            <a href="bookings.php" class="card-box">
                <h3>My Bookings</h3>
                <p>View upcoming appointments.</p>
            </a>

            <a href="services.php" class="card-box">
                <h3>Services</h3>
                <p>Browse available services.</p>
            </a>

            <a href="payment.php" class="card-box">
                <h3>Payments</h3>
                <p>Manage payment options.</p>
            </a>

        </div>

    </div>

   