<?php
// booking_appointment.php
session_start();
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$selected_service = isset($_GET['service_id']) ? (int)$_GET['service_id'] : '';

// Define your services locally so the dropdown never displays blank
$services = [
    1 => ['id' => 1, 'name' => 'Classic Haircut', 'price' => 150.00, 'duration' => 30],
    2 => ['id' => 2, 'name' => 'Beard Trim', 'price' => 90.00, 'duration' => 20],
    3 => ['id' => 3, 'name' => 'Hot Towel Shave', 'price' => 120.00, 'duration' => 30],
    4 => ['id' => 4, 'name' => 'Haircut & Beard Combo', 'price' => 220.00, 'duration' => 45]
];

$time_slots = [
    '09:00' => '9:00 AM',
    '09:30' => '9:30 AM',
    '10:00' => '10:00 AM',
    '10:30' => '10:30 AM',
    '11:00' => '11:00 AM',
    '11:30' => '11:30 AM',
    '12:00' => '12:00 PM',
    '12:30' => '12:30 PM',
    '13:00' => '1:00 PM',
    '13:30' => '1:30 PM',
    '14:00' => '2:00 PM',
    '14:30' => '2:30 PM',
    '15:00' => '3:00 PM',
    '15:30' => '3:30 PM',
    '16:00' => '4:00 PM',
    '16:30' => '4:30 PM',
    '17:00' => '5:00 PM',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $service_id = (int)$_POST['service_id'];
    $barber = trim($_POST['barber'] ?? ''); 
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    
    if (empty($service_id) || empty($barber) || empty($appointment_date) || empty($appointment_time)) {
        $error = 'Please select a service, barber, date, and time.';
    } elseif (!isset($services[$service_id])) {
        $error = 'Invalid service selected.';
    } else {
        $service = $services[$service_id];
        
        if (!isset($_SESSION['bookings'])) {
            $_SESSION['bookings'] = [];
        }
        if (!isset($_SESSION['payments'])) {
            $_SESSION['payments'] = [];
        }

        $booking_id = count($_SESSION['bookings']) + 1;
        $new_booking = [
            'id' => $booking_id,
            'service_id' => $service_id,
            'service_name' => $service['name'],
            'barber' => $barber,
            'date' => $appointment_date,
            'time' => $appointment_time,
            'duration' => $service['duration'],
            'price' => $service['price'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_id' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $_SESSION['bookings'][] = $new_booking;
        
        $payment_id = count($_SESSION['payments']) + 1;
        $_SESSION['payments'][] = [
            'id' => $payment_id,
            'booking_id' => $booking_id,
            'amount' => $service['price'],
            'method' => null,
            'status' => 'pending',
            'transaction_id' => null
        ];
        
        foreach ($_SESSION['bookings'] as &$b) {
            if ($b['id'] == $booking_id) {
                $b['payment_id'] = $payment_id;
                break;
            }
        }
        
        $success = 'Appointment booked successfully with ' . htmlspecialchars($barber) . '! Please complete your payment to confirm.';
    }
}
?>

<div class="booking-container" style="padding: 40px; max-width: 600px; margin: auto;">
    <div class="page-header">
        <h1>Book an Appointment</h1>
        <p>Schedule your next grooming session with us.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error" style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
            <?php echo htmlspecialchars($success); ?> <a href="payment.php">Go to Payments</a>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof db !== "undefined") {
                    db.collection("bookings").add({
                        customer_id: "<?php echo $_SESSION['user_id']; ?>",
                        customer_email: "<?php echo $_SESSION['user_email'] ?? ''; ?>",
                        customerName: "<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>",
                        serviceName: "<?php echo htmlspecialchars($service['name'] ?? ''); ?>",
                        barber: "<?php echo htmlspecialchars($barber ?? ''); ?>",
                        date: "<?php echo htmlspecialchars($appointment_date ?? ''); ?>",
                        time: "<?php echo htmlspecialchars($appointment_time ?? ''); ?>",
                        price: <?php echo $service['price'] ?? 0; ?>,
                        status: "pending",
                        paymentStatus: "unpaid",
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        source: "Website"
                    })
                  
                    .then((docRef) => {
                        console.log("Booking successfully synced, ID: ", docRef.id);
                    })
                    .catch((error) => {
                        console.error("Firebase Error Code:", error.code);
                        console.error("Firebase Error Message:", error.message);
                    });
                }
            });
        </script>
    <?php endif; ?>
    
    <div class="booking-form-container">
        <form method="POST" class="booking-form">
            
            <!-- Service Dropdown -->
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="service_id" style="display: block; font-weight: bold; margin-bottom: 5px;">Select Service</label>
                <select name="service_id" id="service_id" required style="width: 100%; padding: 8px;">
                    <option value="">-- Choose a service --</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo ($selected_service == $s['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['name']); ?> - R <?php echo number_format($s['price'], 2); ?> (<?php echo $s['duration']; ?> min)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
           <!-- Barber Dropdown -->
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="barber" style="display: block; font-weight: bold; margin-bottom: 5px;">Select Barber</label>
                <select name="barber" id="barber" required style="width: 100%; padding: 8px;">
                    <option value="">-- Choose your barber --</option>
                    <option value="William">William (Barber One)</option>
                    <option value="Randy">Randy (Barber Two)</option>
                </select>
            </div>

            <!-- Select Date -->
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="appointment_date" style="display: block; font-weight: bold; margin-bottom: 5px;">Select Date</label>
                <input type="date" name="appointment_date" id="appointment_date" min="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 8px;">
            </div>
            
            <!-- Select Time -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="appointment_time" style="display: block; font-weight: bold; margin-bottom: 5px;">Select Time</label>
                <select name="appointment_time" id="appointment_time" required style="width: 100%; padding: 8px;">
                    <option value="">-- Choose a time --</option>
                    <?php foreach ($time_slots as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="book_appointment" class="btn-primary" style="padding: 10px 20px; background: #000; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Book Appointment</button>
        
        </form>
    </div>
</div><br><br>

<?php include 'includes/footer.php'; ?>