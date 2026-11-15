<?php
// admin_dashboard.php
session_start();

// Security Timeout
$timeout_duration = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset(); session_destroy();
    header("Location: login.php?msg=session_expired"); exit();
}
$_SESSION['last_activity'] = time();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'includes/header.php';
?>

<div class="dashboard-container" style="padding: 30px; max-width: 900px; margin: auto; font-family: 'Segoe UI', Arial, sans-serif; position: relative;">
    
    <div class="admin-header" style="background: #1a1a1a; color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0 0 5px 0; color: #D2B48C;">Hi Admin </h2>
            <p id="stats-summary" style="margin: 0; color: #aaa; font-size: 14px;">Loading shop stats...</p>
        </div>
        <a href="logout.php" style="text-decoration: none; padding: 10px 20px; background: #d9534f; color: #fff; border-radius: 6px; font-weight: bold;">LOG OUT</a>
    </div>

    <div class="date-picker-bar" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 20px; text-align: center;">
        <label for="date-filter" style="color: #000; font-weight: bold; margin-right: 10px;">Select Date to View:</label>
        <input type="date" id="date-filter" style="padding: 10px; border: 1px solid #000; border-radius: 4px; font-size: 1em;">
        <button onclick="filterByDate()" style="background-color: #000; color: #D2B48C; padding: 11px 25px; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; margin-left: 10px;">Load Schedule</button>
        <button onclick="clearFilter()" style="background-color: #eee; color: #333; padding: 11px 25px; border: 1px solid #ccc; font-weight: bold; cursor: pointer; border-radius: 4px; margin-left: 10px;">View All</button>
    </div>

    <div id="bookings-list">
        <p style="text-align: center; color: #666; padding: 20px;">Loading bookings from Firestore...</p>
    </div>
</div>

<!-- Custom Manage Modal Popup -->
<div id="manage-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; width: 90%; max-width: 380px; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); color: #1a1a1a;">
        <h3 id="modal-booking-title" style="margin-top: 0; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 12px; color: #1a1a1a;">Manage Booking</h3>
        
        <div id="modal-main-actions" style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;">
            <button onclick="updateStatus('confirmed', 'unpaid', '')" style="padding: 14px; background: #f2f2f2; color: #1a1a1a; border: 1px solid #ddd; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">Confirm</button>
            <button onclick="showPaymentSubmenu()" style="padding: 14px; background: #f2f2f2; color: #1a1a1a; border: 1px solid #ddd; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">Completed & Paid</button>
            <button onclick="updateStatus('no-show', 'unpaid', '')" style="padding: 14px; background: #f2f2f2; color: #1a1a1a; border: 1px solid #ddd; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">No-show</button>
            <button onclick="updateStatus('cancelled', 'unpaid', '')" style="padding: 14px; background: #fcf0f0; color: #d9534f; border: 1px solid #f5c6cb; border-radius: 6px; font-weight: bold; text-align: left; cursor: pointer; font-size: 15px;">Cancel booking</button>
        </div>

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

<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
<script src="firebase-config.js?v=18"></script>

<script>
let selectedBookingId = null;
let allBookingsCache = [];

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("date-filter").valueAsDate = new Date();
    fetchBookings();
});

function fetchBookings() {
    if (typeof window.db === "undefined") {
        setTimeout(fetchBookings, 300); return;
    }

    // Force Admin View: Load EVERYTHING
    window.db.collection("bookings").get().then((querySnapshot) => {
          allBookingsCache = [];
          querySnapshot.forEach((doc) => {
              allBookingsCache.push({ id: doc.id, ...doc.data() });
          });
          filterByDate(); 
      }).catch((error) => {
          console.error("Error loading bookings:", error);
      });
}

function filterByDate() {
    const selectedDate = document.getElementById("date-filter").value;
    const filteredBookings = allBookingsCache.filter(b => b.date === selectedDate);
    filteredBookings.sort((a, b) => (a.time || "00:00").localeCompare(b.time || "00:00"));
    renderBookings(filteredBookings, selectedDate);
}

function clearFilter() {
    document.getElementById("date-filter").value = "";
    const sortedAll = [...allBookingsCache].sort((a, b) => {
        const dateA = a.date || "0000-00-00";
        const dateB = b.date || "0000-00-00";
        if(dateA !== dateB) return dateA.localeCompare(dateB);
        return (a.time || "00:00").localeCompare(b.time || "00:00");
    });
    renderBookings(sortedAll, "All Time");
}

function renderBookings(bookingsArray, displayDate) {
    const container = document.getElementById("bookings-list");
    container.innerHTML = "";

    let expectedRevenue = 0; let collectedRevenue = 0;

    if (bookingsArray.length === 0) {
        container.innerHTML = `<p style='text-align: center; color: #888; padding: 30px; background: #fff; border-radius: 8px;'>No bookings found for ${displayDate}.</p>`;
    } else {
        bookingsArray.forEach((b) => {
            const price = Number(b.price) || 0;
            expectedRevenue += price;
            if (b.paymentStatus === 'paid') collectedRevenue += price;

            const clientName = b.customer_name || b.userName || b.name || b.customerName || 'Client';
            
            const card = document.createElement("div");
            card.style.cssText = "background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-left: 8px solid #D2B48C; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;";
            card.innerHTML = `
                <div>
                    <div style="font-size: 15px; font-weight: bold; color: #333; margin-bottom: 4px;">Time: ${b.time || '10:00'} &nbsp;&nbsp;|&nbsp;&nbsp; Date: ${b.date || 'Unscheduled'}</div>
                    <div style="font-weight: bold; text-transform: capitalize; font-size: 16px; color: #000;">Client: ${clientName}</div>
                    <div style="color: #555; font-size: 14px; margin-top: 3px;">${b.serviceName || 'Service'} · R${price} · Barber: <span style="color:#e67e22; font-weight:bold;">${b.barber || 'N/A'}</span></div>
                    <div style="color: #333; font-weight: bold; font-size: 13px; margin-top: 5px;">Status: ${b.status || 'pending'} ${b.paymentStatus === 'paid' ? '· Paid' : ''}</div>
                </div>
                <div>
                    <button onclick="openModal('${b.id}', '${clientName}', '${b.serviceName || 'Service'}')" style="background: #000; color: #D2B48C; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">MANAGE</button>
                </div>
            `;
            container.appendChild(card);
        });
    }
    document.getElementById("stats-summary").innerText = `${bookingsArray.length} bookings · Expected R${expectedRevenue} · Collected R${collectedRevenue}`;
}

// Modal Control Functions
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
            .then(() => { closeModal(); fetchBookings(); })
            .catch((error) => { alert("Error deleting booking: " + error.message); });
        return;
    }

    const updateData = { status: status, paymentStatus: paymentStatus };
    if (paymentMethod) { updateData.paymentMethod = paymentMethod; }

    window.db.collection("bookings").doc(selectedBookingId).update(updateData)
        .then(() => { closeModal(); fetchBookings(); })
        .catch((error) => { alert("Error updating booking: " + error.message); });
}
</script>

<?php include 'includes/footer.php'; ?>