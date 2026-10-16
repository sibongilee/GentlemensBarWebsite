<?php
// bookings.php
session_start();
include 'includes/header.php';
$customer_id =$_SESSION['user_id'] ?? 'guest_user';
$customer_name =$_SESSION['user_name'] ?? 'Valued Customer';
?>

<div class="booking-container" style="padding: 40px; max-width: 600px; margin: auto; font-family: 'Segoe UI', Arial, sans-serif; background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-top: 40px; margin-bottom: 40px;">
    
    <!-- Dynamic Greeting Card -->
    <div id="greeting-card" style="background: #1a1a1a; color: #fff; padding: 15px; border-radius: 6px; text-align: center; margin-bottom: 25px; font-weight: bold; font-size: 18px;">
        Checking user session...
    </div>

    <!-- Admin / Staff Metrics View (Hidden by default, shown for Admin/Staff) -->
    <div id="dashboard-metrics" style="display: none; margin-bottom: 25px; background: #f4f4f4; padding: 15px; border-radius: 6px; text-align: center;">
        <p id="total-bookings-count" style="margin: 5px 0; font-size: 16px; font-weight: bold;">0 bookings</p>
        <p id="total-revenue-count" style="margin: 5px 0; font-size: 16px; font-weight: bold; color: green;">Collected R0</p>
    </div>
    
    <h2 style="margin-bottom: 20px; color: #1a1a1a; text-align: center;">Book an Appointment</h2>

    <form id="booking-form" onsubmit="submitBooking(event)">
        
        <!-- Select Service -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Select Service</label>
            <select id="service-select" required style="width: 100%; padding: 12px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
                <option value="">-- Choose a Service --</option>
            </select>
        </div>

        <!-- Select Barber / Staff -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Select Barber</label>
            <select id="staff-select" required style="width: 100%; padding: 12px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
                <option value="">-- Choose Your Barber --</option>
            </select>
        </div>

        <!-- Date Picker -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Date</label>
            <input type="date" id="booking-date" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 12px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
        </div>

        <!-- Time Picker -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #333;">Time</label>
            <input type="time" id="booking-time" value="10:00" required style="width: 100%; padding: 12px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
        </div>

        <!-- Submit Button -->
        <button type="submit" style="width: 100%; padding: 14px; background: #000; color: #fff; border: none; border-radius: 6px; font-weight: bold; font-size: 16px; cursor: pointer;">Confirm Booking</button>

    </form>
    
    <div id="booking-status" style="margin-top: 15px; text-align: center; font-weight: bold;"></div>

    <!-- Admin / Staff Bookings List Container -->
    <div id="bookings-list-container" style="margin-top: 30px;"></div>

</div>

<!-- Firebase SDK Compat Scripts -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-firestore-compat.js"></script>

<!-- Firebase Config -->
<script src="firebase-config.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    initApp();
});

function initApp() {
    if (typeof window.db === "undefined" || typeof firebase.auth === "undefined") {
        setTimeout(initApp, 300);
        return;
    }

    loadDropdowns();

    // Check Auth State & Handle Roles (Admin vs Staff vs Customer)
    firebase.auth().onAuthStateChanged((user) => {
        if (user) {
            window.db.collection("users").doc(user.uid).get().then((doc) => {
                if (doc.exists) {
                    const userData = doc.data();
                    const userName = userData.name || "User";
                    const userRole = userData.role || "customer";

                    // Update Greeting Card Dynamically
                    document.getElementById("greeting-card").textContent = `Hi ${userName} (${userRole})`;

                    // If Admin or Staff, reveal metrics and load data accordingly
                    if (userRole === "admin" || user.email === "xxx@gmail.com") {
                        document.getElementById("dashboard-metrics").style.display = "block";
                        loadAllBookingsForAdmin();
                    } else if (userRole === "staff") {
                        document.getElementById("dashboard-metrics").style.display = "block";
                        loadStaffBookings(userName);
                    }
                }
            }).catch((error) => {
                console.error("Error fetching user profile:", error);
            });
        }
    });
}

function loadDropdowns() {
    // Load Services from Firestore
    window.db.collection("services").get().then((snapshot) => {
        const serviceSelect = document.getElementById("service-select");
        snapshot.forEach((doc) => {
            const s = doc.data();
            const opt = document.createElement("option");
            opt.value = doc.id;
            opt.setAttribute("data-name", s.name || s.title || "Service");
            opt.setAttribute("data-price", s.price || 0);
            opt.textContent = `${s.name || s.title || 'Service'} - R${s.price || 0}`;
            serviceSelect.appendChild(opt);
        });
    });

    // Load Staff / Barbers from Firestore
    window.db.collection("staff").get().then((snapshot) => {
        const staffSelect = document.getElementById("staff-select");
        snapshot.forEach((doc) => {
            const st = doc.data();
            const opt = document.createElement("option");
            opt.value = doc.id;
            opt.setAttribute("data-name", st.name || st.staffName || "Barber");
            opt.textContent = st.name || st.staffName || "Barber";
            staffSelect.appendChild(opt);
        });
    });
}

// ADMIN FUNCTION: Loads EVERY booking in the system without filters
function loadAllBookingsForAdmin() {
    window.db.collection("bookings").get().then((querySnapshot) => {
        let allBookingsHTML = "<h3 style='margin-bottom:15px; border-bottom:2px solid #000; padding-bottom:5px;'>All Shop Bookings (Admin View)</h3>";
        let totalRevenue = 0;
        let bookingCount = 0;

        querySnapshot.forEach((doc) => {
            const booking = doc.data();
            bookingCount++;
            totalRevenue += Number(booking.price || 0);

            allBookingsHTML += `
                <div style="background: #f9f9f9; padding: 12px; margin-bottom: 10px; border-radius: 6px; border-left: 4px solid #000; font-size: 14px;">
                    <p style="margin: 4px 0;"><strong>Customer:</strong> ${booking.customerName || 'N/A'}</p>
                    <p style="margin: 4px 0;"><strong>Service:</strong> ${booking.serviceName || 'N/A'} (R${booking.price || 0})</p>
                    <p style="margin: 4px 0;"><strong>Barber:</strong> ${booking.barber || 'N/A'}</p>
                    <p style="margin: 4px 0;"><strong>Date & Time:</strong> ${booking.date || 'N/A'} at ${booking.time || 'N/A'}</p>
                    <p style="margin: 4px 0;"><strong>Status:</strong> <span style="color: orange; font-weight: bold;">${booking.status || 'pending'}</span></p>
                </div>
            `;
        });

        document.getElementById("total-bookings-count").textContent = `${bookingCount} total bookings`;
        document.getElementById("total-revenue-count").textContent = `Collected R${totalRevenue}`;
        document.getElementById("bookings-list-container").innerHTML = allBookingsHTML || "<p>No bookings found in the database.</p>";
    });
}

// STAFF FUNCTION: Loads only specific staff member bookings
function loadStaffBookings(staffName) {
    window.db.collection("bookings").where("barber", "==", staffName).get().then((querySnapshot) => {
        let staffBookingsHTML = `<h3 style='margin-bottom:15px; border-bottom:2px solid #000; padding-bottom:5px;'>Schedule for ${staffName}</h3>`;
        let revenue = 0;
        let count = 0;

        querySnapshot.forEach((doc) => {
            const booking = doc.data();
            count++;
            revenue += Number(booking.price || 0);

            staffBookingsHTML += `
                <div style="background: #f9f9f9; padding: 12px; margin-bottom: 10px; border-radius: 6px; border-left: 4px solid #333; font-size: 14px;">
                    <p style="margin: 4px 0;"><strong>Customer:</strong> ${booking.customerName || 'N/A'}</p>
                    <p style="margin: 4px 0;"><strong>Service:</strong> ${booking.serviceName || 'N/A'}</p>
                    <p style="margin: 4px 0;"><strong>Date & Time:</strong> ${booking.date || 'N/A'} at ${booking.time || 'N/A'}</p>
                    <p style="margin: 4px 0;"><strong>Status:</strong> ${booking.status || 'pending'}</p>
                </div>
            `;
        });

        document.getElementById("total-bookings-count").textContent = `${count} assigned appointments`;
        document.getElementById("total-revenue-count").textContent = `Expected R${revenue}`;
        document.getElementById("bookings-list-container").innerHTML = staffBookingsHTML || "<p>No bookings assigned to you yet.</p>";
    });
}

function submitBooking(event) {
    event.preventDefault();

    const serviceOpt = document.getElementById("service-select").selectedOptions[0];
    const staffOpt = document.getElementById("staff-select").selectedOptions[0];
    const bookingDate = document.getElementById("booking-date").value;
    const bookingTime = document.getElementById("booking-time").value;
    const statusDiv = document.getElementById("booking-status");

    const currentUser = firebase.auth().currentUser;

    const payload = {
        customer_id: currentUser ? currentUser.uid : "<?php echo $customer_id; ?>",
        customer_name: currentUser ? currentUser.email : "<?php echo htmlspecialchars($customer_name); ?>",
        service_id: document.getElementById("service-select").value,
        serviceName: serviceOpt.getAttribute("data-name"),
        price: Number(serviceOpt.getAttribute("data-price")) || 0,
        staff_id: document.getElementById("staff-select").value,
        barber: staffOpt.getAttribute("data-name"),
        date: bookingDate,
        time: bookingTime,
        status: "pending",
        paymentStatus: "unpaid",
        source: "Website",
        createdAt: firebase.firestore.FieldValue.serverTimestamp()
    };

    statusDiv.style.color = "#000";
    statusDiv.textContent = "Saving booking...";

    window.db.collection("bookings").add(payload)
        .then(() => {
            statusDiv.style.color = "green";
            statusDiv.textContent = "Booking confirmed successfully!";
            document.getElementById("booking-form").reset();
        })
        .catch((error) => {
            statusDiv.style.color = "red";
            statusDiv.textContent = "Error: " + error.message;
        });
}
</script>

<?php include 'includes/footer.php'; ?>