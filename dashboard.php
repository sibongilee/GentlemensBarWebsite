<?php
// dashboard.php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
$customer_id = $_SESSION['user_id'];
$customer_name = $_SESSION['user_name'] ?? '';
?>
<div class="dashboard">
    <h1>Welcome, <?php echo htmlspecialchars($customer_name); ?>!</h1>
    <p class="tagline">Manage your appointments, view services, and handle payments all in one place.</p>
    
    <div class="stats-container">
        <div class="stat-card">
            <div id="upcoming-count" class="stat-number">...</div>
            <div class="stat-label">Upcoming Appointments</div>
        </div>
        <div class="stat-card">
            <div id="total-spent" class="stat-number">R0.00</div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>
</div>

<div class="cards">
    <a href="booking_appointment.php" class="card-box">
        <h3>Book Appointment</h3>
        <p>Schedule a new appointment.</p>
    </a>
    <a href="my_bookings.php" class="card-box">
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

<!-- Firebase SDK Compat Scripts -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>

<!-- Firebase Config -->
<script src="firebase-config.js?v=24"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    loadDashboardStats();
});

function loadDashboardStats() {
    if (typeof window.db === "undefined") {
        setTimeout(loadDashboardStats, 300);
        return;
    }

    const currentUserId = "<?php echo addslashes($customer_id); ?>";
    const currentUserName = "<?php echo strtolower(trim($customer_name)); ?>";

    window.db.collection("bookings")
      .get()
      .then((querySnapshot) => {
          let upcomingCount = 0;
          let totalSpent = 0;
          const today = new Date().toISOString().split('T')[0];

          querySnapshot.forEach((doc) => {
              const b = doc.data();
              
              const bookingName = (b.customer_name || b.userName || b.name || "").toLowerCase().trim();
              const matchesId = (b.customer_id === currentUserId) || (b.userId === currentUserId);
              const matchesName = currentUserName && bookingName.includes(currentUserName);

              if (matchesId || matchesName) {
                  const status = (b.status || "").toLowerCase();
                  const paymentStatus = (b.paymentStatus || "").toLowerCase();
                  const bookingDate = b.date || b.booking_date || '';

                  // Check if upcoming (not cancelled and date is today or in the future)
                  if (status !== 'cancelled' && bookingDate >= today) {
                      upcomingCount++;
                  }

                  // Calculate total spent if paid or completed
                  if (paymentStatus === 'paid' || status.includes('completed') || status.includes('paid')) {
                      totalSpent += Number(b.price) || 0;
                  }
              }
          });

          document.getElementById("upcoming-count").innerText = upcomingCount;
          document.getElementById("total-spent").innerText = "R" + totalSpent.toFixed(2);
      })
      .catch((error) => {
          console.error("Error loading dashboard stats:", error);
          document.getElementById("upcoming-count").innerText = "0";
      });
}
</script>

<?php include 'includes/footer.php'; ?>