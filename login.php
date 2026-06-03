<!DOCTYPE html>
<html>
<head>
    <title>Login | The Gentlemen's Bar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">

    <form method="POST">
        <input type="email" placeholder="Email Address" required>
        <input type="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>
    </form>

    <p>
        Don't have an account?
        <a href="register.php">Register Here</a>
    </p>

</div>

<?php
if(isset($_POST['login'])){
    header("Location: dashboard.php");
}
?>

</body>
</html>

