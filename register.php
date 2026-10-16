<?php
// register.php
session_start();
include 'includes/header.php';
?>

<div class="card" style="max-width: 400px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <img src="Assets/logo.jpeg" alt="The Gentlemen's Bar Logo" class="logo" style="width: 80px; display: block; margin: 0 auto 20px;">
    <h2>Create Account</h2>
    <p class="tagline" style="text-align: center; color: #666; margin-bottom: 20px;">First-class grooming starts here.</p>
    
    <div id="register-error" class="alert alert-error" style="color: red; margin-bottom: 15px; display: none;"></div>
    
    <form id="register-form" onsubmit="handleRegister(event)">
        <div style="margin-bottom: 15px;">
            <input type="text" id="register-name" placeholder="Full Name" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 15px;">
            <input type="email" id="register-email" placeholder="Email Address" autocomplete="email" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 15px;">
            <input type="text" id="register-phone" placeholder="Phone Number" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 15px;">
            <input type="password" id="register-password" placeholder="Password (at least 6 characters)" autocomplete="new-password" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 20px;">
            <input type="password" id="register-confirm-password" placeholder="Confirm Password" autocomplete="new-password" required style="width: 100%; padding: 10px; box-sizing: border-box;">
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #000; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Register</button>
    </form>

    <p style="margin-top: 15px; font-size: 14px; text-align: center;">
        Already have an account? 
        <a href="login.php" style="font-weight: bold; color: #000;">Login Here</a>
    </p>
</div>

<script>
function handleRegister(event) {
    event.preventDefault();
    
    const name = document.getElementById("register-name").value.trim();
    const email = document.getElementById("register-email").value.trim().toLowerCase();
    const phone = document.getElementById("register-phone").value.trim();
    const password = document.getElementById("register-password").value;
    const confirmPassword = document.getElementById("register-confirm-password").value;
    const errorDiv = document.getElementById("register-error");

    errorDiv.style.display = "none";

    if (password !== confirmPassword) {
        errorDiv.textContent = "Passwords do not match.";
        errorDiv.style.display = "block";
        return;
    }

    if (password.length < 6) {
        errorDiv.textContent = "Password must be at least 6 characters long.";
        errorDiv.style.display = "block";
        return;
    }

    if (typeof firebase === "undefined" || !firebase.auth) {
        errorDiv.textContent = "Firebase configuration error. Please refresh and try again.";
        errorDiv.style.display = "block";
        return;
    }

    // 1. Create user securely in Firebase Auth via JavaScript
    firebase.auth().createUserWithEmailAndPassword(email, password)
        .then((userCredential) => {
            const user = userCredential.user;

            // 2. Save user profile into Firestore using the exact Auth UID
            return window.db.collection("users").doc(user.uid).set({
                uid: user.uid,
                name: name,
                email: email,
                phone: phone,
                role: "customer",
                source: "Website",
                createdAt: firebase.firestore.FieldValue.serverTimestamp()
            }).then(() => {
                // 3. Sync to PHP session bridge
                return fetch('set_session.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        uid: user.uid,
                        email: email,
                        name: name,
                        role: 'customer'
                    })
                });
            }).then(() => {
                // 4. Redirect straight to dashboard
                window.location.href = "dashboard.php";
            });
        })
        .catch((error) => {
            console.error("Registration error:", error.code, error.message);
            errorDiv.textContent = error.message;
            errorDiv.style.display = "block";
        });
}
</script>

<?php include 'includes/footer.php'; ?>