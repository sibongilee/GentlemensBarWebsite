<?php
// booking_appointment.php
include 'includes/header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Pre-select service if passed via GET
$selected_service = isset($_GET['service_id']) ? (int)$_GET['service_id'] : '';

// Time slots available
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
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    
    if (empty($service_id) || empty($appointment_date) || empty($appointment_time)) {
        $error = 'Please select a service, date, and time.';
    } elseif (!isset($services[$service_id])) {
        $error = 'Invalid service selected.';
    } else {
        $service = $services[$service_id];
        
        // Create new booking
        $booking_id = count($_SESSION['bookings']) + 1;
        $new_booking = [
            'id' => $booking_id,
            'service_id' => $service_id,
            'service_name' => $service['name'],
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
        
        // Create pending payment record
        $payment_id = count($_SESSION['payments']) + 1;
        $_SESSION['payments'][] = [
            'id' => $payment_id,
            'booking_id' => $booking_id,
            'amount' => $service['price'],
            'method' => null,
            'status' => 'pending',
            'transaction_id' => null
        ];
        
        // Update booking with payment_id
        foreach ($_SESSION['bookings'] as &$booking) {
            if ($booking['id'] == $booking_id) {
                $booking['payment_id'] = $payment_id;
                break;
            }
        }
        
        $success = 'Appointment booked successfully! Please complete your payment to confirm.';
    }
}
?>
<div class="booking-container">
    <div class="page-header">
        <h1>Book an Appointment</h1>
        <p>Schedule your next grooming session with us.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?> <a href="payment.php">Go to Payments</a></div>
    <?php endif; ?>
    
    <div class="booking-form-container">
        <form method="POST" class="booking-form">
            <div class="form-group">
                <label for="service_id">Select Service</label>
                <select name="service_id" id="service_id" required>
                    <option value="">-- Choose a service --</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>" <?php echo ($selected_service == $service['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($service['name']); ?> - R<?php echo number_format($service['price'], 2); ?> (<?php echo $service['duration']; ?> min)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="appointment_date">Select Date</label>
                <input type="date" name="appointment_date" id="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="appointment_time">Select Time</label>
                <select name="appointment_time" id="appointment_time" required>
                    <option value="">-- Choose a time --</option>
                    <?php foreach ($time_slots as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Add a review step before final booking -->
            <div id="bookingSummary" class="booking-summary" style="display:none;">
                <h3>Review Your Booking</h3>
                <p>Service: <span id="summaryService"></span></p>
                <p>Date: <span id="summaryDate"></span></p>
                <p>Time: <span id="summaryTime"></span></p>
                <p>Total: R <span id="summaryPrice"></span></p>
                <button type="button" onclick="confirmBooking()">Confirm Booking</button>
                <button type="button" onclick="editBooking()">Edit</button>
            </div>
            <button type="submit" name="book_appointment" class="btn-primary">Book Appointment</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>