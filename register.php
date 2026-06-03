<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">
    <h1>Gentlemen’s Bar</h1>
    <p class="tagline">Premium Grooming Experience</p>

    <h2>Create Account</h2>

    <form method="POST">
        <input type="text" placeholder="Full Name" required>
        <input type="email" placeholder="Email" required>
        <input type="text" placeholder="Phone Number" required>
        <input type="password" placeholder="Password" required>
        <button name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

<?php
if(isset($_POST['register'])){
    echo "<script>alert('Registration successful'); window.location='login.php';</script>";
}
?>

</body>
</html>