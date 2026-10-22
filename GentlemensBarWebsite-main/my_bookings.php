<?php
// my_bookings.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
$customer_id =$_SESSION['user_id'];
$customer_email =$_SESSION['user_email'] ?? '';
$customer_name =$_SESSION['user_name'] ?? 'Customer';
?>

<div class="dashboard-container" style="padding: 40px; max-width: 800px; margin: auto; font-family: 'Segoe UI', Arial, sans-serif;">
    
    <!-- Header Section -->
    <div style="background: #1a1a1a; color: #fff; padding: 25px; border-radius: 8px; margin-bottom: 25px; text-align: center;">
        <h2 style="margin: 0 0 5px 0;">My Appointments</h2>
        <p style="margin: 0; color: #aaa; font-size: 14px;">Viewing active bookings for <?php echo htmlspecialchars($customer_name); ?></p>
    </div>

    <!-- User Bookings List Container -->
    <div id="user-bookings-list">
        <p style="text-align: center; color: #666; padding: 30px;">Loading your bookings...</p>
    </div>

</div>

<!-- Firebase SDK Compat Scripts -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>

<!-- Firebase Config -->
<script src="firebase-config.js?v=27"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    loadUserBookings();
});

function loadUserBookings() {
    if (typeof window.db === "undefined") {
        setTimeout(loadUserBookings, 300);
        return;
    }

    const currentUserId = "<?php echo addslashes($customer_id); ?>";
    const currentUserEmail = "<?php echo addslashes(strtolower(trim($customer_email))); ?>";
    const container = document.getElementById("user-bookings-list");

    window.db.collection("bookings")
      .get()
      .then((querySnapshot) => {
          container.innerHTML = "";
          let foundCount = 0;

          if (querySnapshot.empty) {
              container.innerHTML = `
                  <div style="text-align: center; color: #666; padding: 40px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                      <p style="font-size: 16px; margin-bottom: 15px;">You have no upcoming appointments.</p>
                      <a href="booking_appointment.php" style="display: inline-block; padding: 10px 20px; background: #000; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold;">Book an Appointment</a>
                  </div>
              `;
              return;
          }

          querySnapshot.forEach((doc) => {
              const b = doc.data();
              
              const bId = (b.customer_id || b.customerId || "").toString().trim();
              const bEmail = (b.customer_email || b.email || "").toString().toLowerCase().trim();

              // Strict match: Must match either the exact session user_id or user_email
              const matchesUser = (currentUserId && bId === currentUserId) || 
                                  (currentUserEmail && bEmail === currentUserEmail);

              if (matchesUser) {
                  foundCount++;
                  const price = Number(b.price) || 0;

                  const card = document.createElement("div");
                  card.style.cssText = "background: #fff; color: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;";

                  card.innerHTML = `
                      <div>
                          <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">${b.date || 'Date'} @ ${b.time || '10:00'}</div>
                          <div style="color: #333; font-weight: bold; font-size: 15px; margin-top: 3px;">${b.serviceName || 'Service'} · R${price}</div>
                          <div style="color: #666; font-size: 13px; margin-top: 2px;">Barber: ${b.barber || 'Barber One'}</div>
                          <div style="color: #e67e22; font-weight: bold; font-size: 13px; margin-top: 5px; text-transform: capitalize;">Status: ${b.status || 'pending'}</div>
                      </div>
                      <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                          <span style="padding: 6px 12px; background: ${b.paymentStatus === 'paid' ? '#28a745' : '#f0ad4e'}; color: #fff; border-radius: 6px; font-weight: bold; font-size: 11px; text-transform: uppercase;">
                              ${b.paymentStatus || 'unpaid'}
                          </span>
                          <button onclick="cancelBooking('${doc.id}')" style="background: #d9534f; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-weight: bold; font-size: 12px; cursor: pointer;">Cancel / Remove</button>
                      </div>
                  `;
                  container.appendChild(card);
              }
          });

          if (foundCount === 0) {
              container.innerHTML = `
                  <div style="text-align: center; color: #666; padding: 40px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                      <p style="font-size: 16px; margin-bottom: 15px;">You have no upcoming appointments.</p>
                      <a href="booking_appointment.php" style="display: inline-block; padding: 10px 20px; background: #000; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold;">Book an Appointment</a>
                  </div>
              `;
          }
      })
      .catch((error) => {
          console.error("Error loading user bookings:", error);
          container.innerHTML = "<p style='color:red; text-align:center;'>Error loading your bookings: " + error.message + "</p>";
      });
}

function cancelBooking(bookingId) {
    if (!confirm("Are you sure you want to remove this booking?")) return;

    window.db.collection("bookings").doc(bookingId).delete()
        .then(() => {
            alert("Booking removed successfully.");
            location.reload();
        })
        .catch((error) => {
            alert("Error removing booking: " + error.message);
        });
}
</script>

<?php include 'includes/footer.php'; ?>