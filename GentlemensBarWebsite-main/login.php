<?php
// login.php
session_start();
include 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <img src="Assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="logo" style="width: 80px; display: block; margin: 0 auto 20px;">
    <h2>Login</h2>
    
    <div id="login-error" class="alert alert-error" style="color: red; margin-bottom: 15px; display: none;"></div>
    
    <form id="login-form" onsubmit="handleLogin(event)">
        <div style="margin-bottom: 15px;">
            <input type="email" id="login-email" placeholder="Email Address" autocomplete="email" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <input type="password" id="login-password" placeholder="Password" autocomplete="current-password" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #000; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Login</button>
    </form>

    <p style="margin-top: 15px; font-size: 14px; text-align: center;">
        Don't have an account? 
        <a href="register.php" style="font-weight: bold; color: #000;">Register Here</a>
    </p>
</div>

<script>
function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById("login-email").value.trim().toLowerCase();
    const password = document.getElementById("login-password").value;
    const errorDiv = document.getElementById("login-error");
    
    errorDiv.style.display = "none";

    if (typeof firebase === "undefined" || !firebase.auth) {
        errorDiv.textContent = "Firebase configuration error. Please refresh and try again.";
        errorDiv.style.display = "block";
        return;
    }

    // 1. Authenticate securely via Firebase Auth
    firebase.auth().signInWithEmailAndPassword(email, password)
        .then((userCredential) => {
            const user = userCredential.user;
            
            // 2. Fetch role from Firestore users collection
            window.db.collection("users").doc(user.uid).get()
                .then((doc) => {
                    let role = "customer";
                    let userName = "Customer";
                    if (doc.exists) {
                        const data = doc.data();
                        role = (data.role || "customer").toLowerCase();
                        userName = data.name || "Customer";
                    }

                    // 3. Send session data to PHP handler backend
                    fetch('set_session.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            uid: user.uid,
                            email: user.email,
                            name: userName,
                            role: role
                        })
                    }).then(() => {
                        // 4. Route to correct dashboard based on role or whitelisted staff emails
                        if (role === "staff" || role === "admin" || email === "william@gmail.com" || email === "randy@gmail.com" || email === "xxx@gmail.com" || email === "admin@gmail.com") {
                            window.location.href = "staff_dashboard.php";
                        } else {
                            window.location.href = "dashboard.php";
                        }
                    });
                })
                .catch((err) => {
                    console.warn("Firestore error, defaulting to customer dashboard:", err);
                    window.location.href = "dashboard.php";
                });
        })
        .catch((error) => {
            console.error("Login error:", error.code, error.message);
            errorDiv.textContent = "Invalid email or password. Please try again.";
            errorDiv.style.display = "block";
        });
}
</script>

<?php include 'includes/footer.php'; ?>