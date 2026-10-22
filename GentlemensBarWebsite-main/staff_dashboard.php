<?php
// staff_dashboard.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
$staff_name =$_SESSION['user_name'] ?? 'Staff';
?>

<div class="dashboard-container" style="padding: 30px; max-width: 800px; margin: auto; font-family: 'Segoe UI', Arial, sans-serif; position: relative;">
    
    <!-- Header Section -->
    <div class="staff-header" style="background: #1a1a1a; color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 id="user-greeting-title" style="margin: 0 0 5px 0;">Hi <?php echo htmlspecialchars($staff_name); ?> (staff)</h2>
        <p id="stats-summary" style="margin: 0; color: #aaa; font-size: 14px;">Loading stats...</p>
    </div>

    <!-- Action Controls Toolbar -->
    <div class="controls-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
        <div>
            <button onclick="alert('Walk-in booking modal coming soon!')" style="width: 100%; padding: 12px; background: #1a1a1a; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">WALK-IN</button>
        </div>
        <div>
            <a href="logout.php" style="display: block; text-align: center; text-decoration: none; width: 100%; padding: 12px; background: #d9534f; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">LOG OUT</a>
        </div>
    </div>

    <!-- Bookings List Container -->
    <div id="bookings-list">
        <p style="text-align: center; color: #666; padding: 20px;">Loading bookings from Firestore...</p>
    </div>

</div>

<!-- Custom Manage Modal Popup -->
<div id="manage-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; width: 90%; max-width: 380px; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); color: #1a1a1a;">
        <h3 id="modal-booking-title" style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 12px; color: #1a1a1a;">Manage Booking</h3>
        
        <!-- Main Actions Menu -->
        <div id="modal-main-actions" style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
            <button onclick="updateStatus('confirmed', 'unpaid', '')" style="padding: 14px; background: #f2f2f2; color: #1a1a1a; border: 1px solid #ddd; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">Confirm</button>
            <button onclick="showPaymentSubmenu()" style="padding: 14px; background: #f2f2f2; color: #1a1a1a; border: 1px solid #ddd; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">Completed & Paid</button>
            <button onclick="updateStatus('no-show', 'unpaid', '')" style="padding: 14px; background: #f2f2f2; color: #1a1a1a; border: 1px solid #ddd; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">No-show</button>
            <button onclick="updateStatus('cancelled', 'unpaid', '')" style="padding: 14px; background: #fcf0f0; color: #d9534f; border: 1px solid #f5c6cb; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">Cancel booking</button>
        </div>

        <!-- Payment Method Submenu -->
        <div id="modal-payment-submenu" style="display: none; flex-direction: column; gap: 10px; margin-top: 15px;">
            <p style="margin: 0 0 5px 0; font-weight: bold; font-size: 15px; color: #333;">How did they pay?</p>
            <button onclick="updateStatus('completed & paid', 'paid', 'Cash')" style="padding: 14px; background: #28a745; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">Cash</button>
            <button onclick="updateStatus('completed & paid', 'paid', 'Card')" style="padding: 14px; background: #007bff; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">Card</button>
            <button onclick="updateStatus('completed & paid', 'paid', 'EFT')" style="padding: 14px; background: #6f42c1; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">EFT</button>
            <button onclick="hidePaymentSubmenu()" style="padding: 10px; background: transparent; border: none; color: #666; cursor: pointer; text-decoration: underline; font-weight: bold; margin-top: 5px;">Back</button>
        </div>

        <button onclick="closeModal()" style="margin-top: 20px; width: 100%; padding: 12px; background: #1a1a1a; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px;">Close</button>
    </div>
</div>

<!-- Firebase SDK Compat Scripts -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>

<!-- Firebase Config with cache-buster -->
<script src="firebase-config.js?v=18"></script>

<script>
let selectedBookingId = null;
let currentUserName = "<?php echo htmlspecialchars($staff_name); ?>";
let currentUserRole = "staff";
const sessionUserId = "<?php echo htmlspecialchars($_SESSION['user_id']); ?>";

document.addEventListener("DOMContentLoaded", function() {
    fetchUserDataAndBookings();
});

function fetchUserDataAndBookings() {
    if (typeof window.db === "undefined") {
        setTimeout(fetchUserDataAndBookings, 300);
        return;
    }

    // Direct UID Mapping for Randy, William, and Admin
    if (sessionUserId === "ER9szZ3C2CcYqneC4L5sEIOx3Bf2") {
        currentUserName = "Randy";
        currentUserRole = "staff";
        document.getElementById("user-greeting-title").innerText = `Hi Randy (staff)`;
        loadAllBookings();
        return;
    } else if (sessionUserId === "SpdzcLf3ZWYpWI0yWB2aO6qAkBp2") {
        currentUserName = "William";
        currentUserRole = "staff";
        document.getElementById("user-greeting-title").innerText = `Hi William (staff)`;
        loadAllBookings();
        return;
    } else if (sessionUserId === "S6XWLYukAaanstSPnIM8icQYEn53") {
        currentUserName = "Admin";
        currentUserRole = "admin";
        document.getElementById("user-greeting-title").innerText = `Hi Admin (admin)`;
        loadAllBookings();
        return;
    }

    // Fallback lookup from Firestore users collection if UID is anything else
    window.db.collection("users").doc(sessionUserId).get()
        .then((doc) => {
            if (doc.exists) {
                const data = doc.data();
                currentUserName = data.name || data.fullName || currentUserName;
                currentUserRole = data.role || "staff";

                document.getElementById("user-greeting-title").innerText = `Hi ${currentUserName} (${currentUserRole})`;
            }
            loadAllBookings();
        })
        .catch(() => {
            loadAllBookings();
        });
}

function loadAllBookings() {
    // Pulls all bookings so dates are explicitly visible on cards without hiding anything
    window.db.collection("bookings")
      .get()
      .then((querySnapshot) => {
          const container = document.getElementById("bookings-list");
          container.innerHTML = "";

          let bookingCount = 0;
          let expectedRevenue = 0;
          let collectedRevenue = 0;

          querySnapshot.forEach((doc) => {
              const b = doc.data();

              // Filter in-memory for staff members (Randy or William)
              if (currentUserRole !== "admin" && currentUserName.toLowerCase() !== "admin") {
                  const assignedBarber = (b.barber || "").toLowerCase();
                  const matchesName = assignedBarber.includes(currentUserName.toLowerCase());
                  // Also cross-reference barber1/barber2 mappings if needed
                  const matchesAlias = (currentUserName === "Randy" && assignedBarber.includes("barber 1")) ||
                                       (currentUserName === "William" && assignedBarber.includes("barber 2"));

                  if (!matchesName && !matchesAlias) {
                      return; 
                  }
              }

              bookingCount++;
              const price = Number(b.price) || 0;
              expectedRevenue += price;
              if (b.paymentStatus === 'paid') {
                  collectedRevenue += price;
              }

              const clientName = b.customer_name || b.userName || b.name || b.customerName || 'Client';
              const bookingDate = b.date || 'Unscheduled date';
              const bookingTime = b.time || '10:00';

              const card = document.createElement("div");
              card.style.cssText = "background: #fff; color: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;";

              card.innerHTML = `
                  <div>
                      <div style="font-size: 15px; font-weight: bold; color: #e67e22; margin-bottom: 4px;">📅 Date: ${bookingDate} | Time: ${bookingTime}</div>
                      <div style="font-weight: bold; text-transform: capitalize; font-size: 16px; color: #000;">${clientName}</div>
                      <div style="color: #555; font-size: 14px; margin-top: 3px;">${b.serviceName || 'Service'} · R${price} · Barber: ${b.barber || 'N/A'}</div>
                      <div style="color: #333; font-weight: bold; font-size: 13px; margin-top: 5px; text-transform: capitalize;">Status: ${b.status || 'pending'} ${b.paymentStatus === 'paid' ? '· Paid (' + (b.paymentMethod || '') + ')' : ''}</div>
                  </div>
                  <div>
                      <button onclick="openModal('${doc.id}', '${clientName}', '${b.serviceName || 'Service'}')" style="background: #000; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">MANAGE</button>
                  </div>
              `;
              container.appendChild(card);
          });

          if (bookingCount === 0) {
              container.innerHTML = "<p style='text-align: center; color: #888; padding: 30px; background: #fff; border-radius: 8px;'>No bookings found in the system.</p>";
          }

          document.getElementById("stats-summary").innerText = `${bookingCount} bookings · Expected R${expectedRevenue} · Collected R${collectedRevenue}`;
      })
      .catch((error) => {
          console.error("Error loading bookings:", error);
          document.getElementById("bookings-list").innerHTML = "<p style='color:red; text-align:center;'>Error loading bookings: " + error.message + "</p>";
      });
}

function openModal(bookingId, clientName, serviceName) {
    selectedBookingId = bookingId;
    document.getElementById("modal-booking-title").innerText = `${clientName} · ${serviceName}`;
    document.getElementById("modal-main-actions").style.display = "flex";
    document.getElementById("modal-payment-submenu").style.display = "none";
    document.getElementById("manage-modal").style.display = "flex";
}

function closeModal() {
    document.getElementById("manage-modal").style.display = "none";
    selectedBookingId = null;
}

function showPaymentSubmenu() {
    document.getElementById("modal-main-actions").style.display = "none";
    document.getElementById("modal-payment-submenu").style.display = "flex";
}

function hidePaymentSubmenu() {
    document.getElementById("modal-main-actions").style.display = "flex";
    document.getElementById("modal-payment-submenu").style.display = "none";
}

function updateStatus(status, paymentStatus, paymentMethod) {
    if (!selectedBookingId) return;

    if (status === 'cancelled') {
        if (!confirm("Are you sure you want to cancel and remove this booking?")) return;

        window.db.collection("bookings").doc(selectedBookingId).delete()
            .then(() => {
                closeModal();
                location.reload();
            })
            .catch((error) => {
                alert("Error deleting booking: " + error.message);
            });
        return;
    }

    const updateData = {
        status: status,
        paymentStatus: paymentStatus
    };
    if (paymentMethod) {
        updateData.paymentMethod = paymentMethod;
    }

    window.db.collection("bookings").doc(selectedBookingId).update(updateData)
        .then(() => {
            closeModal();
            location.reload();
        })
        .catch((error) => {
            alert("Error updating booking: " + error.message);
        });
}
</script>

<?php include 'includes/footer.php'; ?>