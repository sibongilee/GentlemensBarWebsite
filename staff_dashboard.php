<?php
// staff_dashboard.php
session_start();

// Redirect Admin to their correct dashboard
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === 'S6XWLYukAaanstSPnIM8icQYEn53') {
    header("Location: admin_dashboard.php");
    exit();
}

$timeout_duration = 900; // 15 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset(); session_destroy();
    header("Location: login.php?msg=session_expired"); exit();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'includes/header.php';
$staff_name = $_SESSION['user_name'] ?? 'Staff';
?>

<div class="dashboard-container" style="padding: 30px; max-width: 900px; margin: auto; font-family: 'Segoe UI', Arial, sans-serif; position: relative;">
    
    <!-- Header Section -->
    <div class="staff-header" style="background: #1a1a1a; color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 id="user-greeting-title" style="margin: 0 0 5px 0; color: #D2B48C;">Hi <?php echo htmlspecialchars($staff_name); ?> (Staff)</h2>
        <p id="stats-summary" style="margin: 0; color: #aaa; font-size: 14px;">Loading your stats...</p>
    </div>

    <!-- Action Controls Toolbar -->
    <div class="controls-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
        <div>
            <button onclick="openWalkInModal()" style="width: 100%; padding: 12px; background: #1a1a1a; color: #D2B48C; border: 1px solid #D2B48C; border-radius: 6px; font-weight: bold; cursor: pointer;">WALK-IN</button>
        </div>
        <div>
            <a href="logout.php" style="display: block; text-align: center; text-decoration: none; width: 100%; padding: 12px; background: #d9534f; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">LOG OUT</a>
        </div>
    </div>

    <!-- Calendar Date Selector -->
    <div class="date-picker-bar" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee; margin-bottom: 20px; text-align: center;">
        <label for="date-filter" style="color: #000; font-weight: bold; margin-right: 10px;">Select Date to View:</label>
        <input type="date" id="date-filter" style="padding: 10px; border: 1px solid #000; border-radius: 4px; font-size: 1em;">
        <button onclick="filterByDate()" style="background-color: #000; color: #D2B48C; padding: 11px 25px; border: none; font-weight: bold; cursor: pointer; border-radius: 4px; margin-left: 10px;">Load Schedule</button>
        <button onclick="clearFilter()" style="background-color: #eee; color: #333; padding: 11px 25px; border: 1px solid #ccc; font-weight: bold; cursor: pointer; border-radius: 4px; margin-left: 10px;">View All</button>
    </div>

    <!-- Bookings Timeline Container -->
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

<!-- Walk-In Modal Popup -->
<div id="walkin-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; width: 90%; max-width: 420px; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); color: #1a1a1a;">
        <h3 style="margin-top: 0; font-size: 18px; border-bottom: 1px solid #eee; padding-bottom: 12px; color: #1a1a1a;">New Walk-In Appointment</h3>
        
        <form id="walkin-form" onsubmit="submitWalkIn(event)" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
            <div>
                <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Client Name</label>
                <input type="text" id="walkin-client-name" placeholder="Enter client full name" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Select Service</label>
                <select id="walkin-service" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
                    <option value="">-- Choose a service --</option>
                    <option value="Classic Haircut|150">Classic Haircut - R150.00</option>
                    <option value="Beard Trim|90">Beard Trim - R90.00</option>
                    <option value="Hot Towel Shave|120">Hot Towel Shave - R120.00</option>
                    <option value="Haircut & Beard Combo|220">Haircut & Beard Combo - R220.00</option>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Assigned Barber</label>
                <input type="text" id="walkin-barber" readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 6px; font-weight: bold; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Date</label>
                    <input type="date" id="walkin-date" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: bold; margin-bottom: 4px;">Time</label>
                    <input type="time" id="walkin-time" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;">
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" style="flex: 1; padding: 12px; background: #000; color: #D2B48C; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Add Walk-In</button>
                <button type="button" onclick="closeWalkInModal()" style="flex: 1; padding: 12px; background: #eee; color: #333; border: 1px solid #ccc; border-radius: 6px; font-weight: bold; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>
<script src="firebase-config.js?v=18"></script>

<script>
let selectedBookingId = null;
let allBookingsCache = [];
let currentUserName = "<?php echo htmlspecialchars($staff_name); ?>";
const sessionUserId = "<?php echo htmlspecialchars($_SESSION['user_id']); ?>";

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("date-filter").valueAsDate = new Date();
    fetchUserDataAndBookings();
});

function fetchUserDataAndBookings() {
    if (typeof window.db === "undefined") {
        setTimeout(fetchUserDataAndBookings, 300);
        return;
    }

    if (sessionUserId === "ER9szZ3C2CcYqneC4L5sEIOx3Bf2") {
        currentUserName = "Randy";
    } else if (sessionUserId === "SpdzcLf3ZWYpWI0yWB2aO6qAkBp2") {
        currentUserName = "William";
    }
    
    document.getElementById("user-greeting-title").innerText = `Hi ${currentUserName} (Staff)`;

    window.db.collection("bookings")
      .get()
      .then((querySnapshot) => {
          allBookingsCache = [];
          querySnapshot.forEach((doc) => {
              allBookingsCache.push({ id: doc.id, ...doc.data() });
          });
          filterByDate();
      })
      .catch((error) => {
          console.error("Error loading bookings:", error);
          document.getElementById("bookings-list").innerHTML = "<p style='color:red; text-align:center;'>Error loading bookings: " + error.message + "</p>";
      });
}

function filterByDate() {
    const selectedDate = document.getElementById("date-filter").value;
    
    const filteredBookings = allBookingsCache.filter(b => {
        const matchesDate = b.date === selectedDate;
        const assignedBarber = (b.barber || "").toLowerCase();
        const matchesName = assignedBarber.includes(currentUserName.toLowerCase());
        const matchesAlias = (currentUserName === "Randy" && assignedBarber.includes("barber 1")) ||
                             (currentUserName === "William" && assignedBarber.includes("barber 2"));

        return matchesDate && (matchesName || matchesAlias);
    });
    
    filteredBookings.sort((a, b) => (a.time || "00:00").localeCompare(b.time || "00:00"));
    renderBookings(filteredBookings, selectedDate);
}

function clearFilter() {
    document.getElementById("date-filter").value = "";
    const filteredBookings = allBookingsCache.filter(b => {
        const assignedBarber = (b.barber || "").toLowerCase();
        const matchesName = assignedBarber.includes(currentUserName.toLowerCase());
        const matchesAlias = (currentUserName === "Randy" && assignedBarber.includes("barber 1")) ||
                             (currentUserName === "William" && assignedBarber.includes("barber 2"));
        return matchesName || matchesAlias;
    });

    filteredBookings.sort((a, b) => {
        const dateA = a.date || "0000-00-00";
        const dateB = b.date || "0000-00-00";
        if(dateA !== dateB) return dateA.localeCompare(dateB);
        return (a.time || "00:00").localeCompare(b.time || "00:00");
    });
    renderBookings(filteredBookings, "All Time");
}

function renderBookings(bookingsArray, displayDate) {
    const container = document.getElementById("bookings-list");
    container.innerHTML = "";

    let expectedRevenue = 0;
    let collectedRevenue = 0;

    if (bookingsArray.length === 0) {
        container.innerHTML = `<p style='text-align: center; color: #888; padding: 30px; background: #fff; border-radius: 8px;'>No bookings found for you on ${displayDate}.</p>`;
    } else {
        bookingsArray.forEach((b) => {
            const price = Number(b.price) || 0;
            expectedRevenue += price;
            if (b.paymentStatus === 'paid') { collectedRevenue += price; }

            const clientName = b.customer_name || b.userName || b.name || b.customerName || 'Walk-in Client';
            const assignedBarber = b.barber || currentUserName;
            const bookingDate = b.date || 'Unscheduled';
            const bookingTime = b.time || '10:00';

            const card = document.createElement("div");
            card.style.cssText = "background: #fff; color: #1a1a1a; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-left: 8px solid #D2B48C; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;";

            card.innerHTML = `
                <div>
                    <div style="font-size: 15px; font-weight: bold; color: #333; margin-bottom: 4px;">Time: ${bookingTime} &nbsp;&nbsp;|&nbsp;&nbsp; Date: ${bookingDate}</div>
                    <div style="font-weight: bold; font-size: 16px; color: #000;">Customer: ${clientName}</div>
                    <div style="color: #555; font-size: 14px; margin-top: 3px;">Service: ${b.serviceName || 'Service'} · R${price}</div>
                    <div style="color: #e67e22; font-weight: bold; font-size: 13px; margin-top: 3px;">Assigned Staff: ${assignedBarber}</div>
                    <div style="color: #333; font-weight: bold; font-size: 13px; margin-top: 3px; text-transform: capitalize;">Status: ${b.status || 'pending'} ${b.paymentStatus === 'paid' ? '· Paid (' + (b.paymentMethod || '') + ')' : ''}</div>
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
            .then(() => { closeModal(); fetchUserDataAndBookings(); })
            .catch((error) => { alert("Error deleting booking: " + error.message); });
        return;
    }

    const updateData = { status: status, paymentStatus: paymentStatus };
    if (paymentMethod) { updateData.paymentMethod = paymentMethod; }

    window.db.collection("bookings").doc(selectedBookingId).update(updateData)
        .then(() => { closeModal(); fetchUserDataAndBookings(); })
        .catch((error) => { alert("Error updating booking: " + error.message); });
}

// Walk-In Functions
function openWalkInModal() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    document.getElementById("walkin-date").value = `${year}-${month}-${day}`;

    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById("walkin-time").value = `${hours}:${minutes}`;

    document.getElementById("walkin-barber").value = currentUserName;
    document.getElementById("walkin-modal").style.display = "flex";
}

function closeWalkInModal() {
    document.getElementById("walkin-modal").style.display = "none";
    document.getElementById("walkin-form").reset();
}

function submitWalkIn(event) {
    event.preventDefault();

    const clientName = document.getElementById("walkin-client-name").value.trim();
    const serviceVal = document.getElementById("walkin-service").value.split('|');
    const serviceName = serviceVal[0];
    const servicePrice = parseFloat(serviceVal[1]);
    const barber = document.getElementById("walkin-barber").value;
    const date = document.getElementById("walkin-date").value;
    const time = document.getElementById("walkin-time").value;

    if (!clientName || !serviceName || !date || !time) {
        alert("Please fill in all fields.");
        return;
    }

    window.db.collection("bookings").add({
        customerName: clientName,
        customer_name: clientName,
        serviceName: serviceName,
        barber: barber,
        date: date,
        time: time,
        price: servicePrice,
        status: "confirmed",
        paymentStatus: "unpaid",
        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
        source: "Walk-In"
    })
    .then((docRef) => {
        closeWalkInModal();
        fetchUserDataAndBookings();
    })
    .catch((error) => {
        alert("Error saving walk-in: " + error.message);
    });
}
</script>

<?php include 'includes/footer.php'; ?>