<?php
require_once 'db_connect.php';

// Accept only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST method is allowed"]);
    exit();
}

// Read raw JSON sent from Frontend / Android
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Fallback to standard POST form fields
if (!$data) {
    $data = $_POST;
}

// Validate required fields
if (empty($data['customer_id']) || empty($data['staff_id']) || empty($data['booking_date']) || empty($data['booking_time']) || empty($data['service_id'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields: customer_id, staff_id, booking_date, booking_time, service_id"
    ]);
    exit();
}

try {
    $conn->beginTransaction();

    // 1. Fetch service price and duration
    $stmtService = $conn->prepare("SELECT Price, Duration FROM Service WHERE ServiceID = ?");
    $stmtService->execute([$data['service_id']]);
    $service = $stmtService->fetch();

    if (!$service) {
        throw new Exception("Selected service does not exist.");
    }

    $price = $service['Price'];
    $duration = $service['Duration'];

    // 2. Insert into Bookings
    $stmtBooking = $conn->prepare("
        INSERT INTO Bookings (BookingDate, BookingTime, Status, CustomerID, StaffID) 
        VALUES (?, ?, 'Confirmed', ?, ?)
    ");
    $stmtBooking->execute([
        $data['booking_date'],
        $data['booking_time'],
        $data['customer_id'],
        $data['staff_id']
    ]);

    $bookingId = $conn->lastInsertId();

    // 3. Insert into BookingServices with Price and Duration
    $stmtBookingService = $conn->prepare("
        INSERT INTO BookingServices (BookingID, ServiceID, Price, Duration) 
        VALUES (?, ?, ?, ?)
    ");
    $stmtBookingService->execute([$bookingId, $data['service_id'], $price, $duration]);

    $conn->commit();

    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "Booking created successfully",
        "booking_id" => (int)$bookingId,
        "details" => [
            "booking_date" => $data['booking_date'],
            "booking_time" => $data['booking_time'],
            "customer_id"  => (int)$data['customer_id'],
            "staff_id"     => (int)$data['staff_id'],
            "service_id"   => (int)$data['service_id'],
            "price"        => $price,
            "duration"     => $duration
        ]
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to create booking: " . $e->getMessage()
    ]);
}
?>