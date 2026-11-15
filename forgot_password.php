<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - The Gentlemen's Bar</title>
    <link rel="stylesheet" href="style.css">
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
    <script src="firebase-config.js"></script>
</head>
<body style="background-color: #1a1a1a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">

    <div style="background: #fff; padding: 40px; border-radius: 8px; width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
        <h2 style="text-align: center; color: #000; margin-bottom: 10px;">Reset Password</h2>
        <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 20px;">Enter your email and we'll send you a password reset link.</p>
        
        <div id="message" style="margin-bottom: 15px; text-align: center; font-size: 14px; font-weight: bold;"></div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #333;">Email Address</label>
            <input type="email" id="reset-email" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
        </div>
        
        <button onclick="sendPasswordReset()" style="width: 100%; background: #000; color: #D2B48C; padding: 12px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px;">Send Reset Link</button>
        
        <p style="text-align: center; margin-top: 20px; font-size: 13px;"><a href="login.php" style="color: #666; text-decoration: none;">← Back to Login</a></p>
    </div>

<script>
function sendPasswordReset() {
    const email = document.getElementById('reset-email').value;
    const msgBox = document.getElementById('message');

    if (!email) {
        msgBox.style.color = "red";
        msgBox.innerText = "Please enter your email address.";
        return;
    }

    firebase.auth().sendPasswordResetEmail(email)
        .then(() => {
            msgBox.style.color = "green";
            msgBox.innerText = "Password reset email sent! Check your inbox.";
        })
        .catch((error) => {
            msgBox.style.color = "red";
            msgBox.innerText = "Error: " + error.message;
        });
}
</script>
</body>
</html>