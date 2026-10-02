<?php
require_once 'db_connect.php';

try {
    $stmt = $conn->prepare("SELECT StaffID, FullName, Role, Email, PhoneNumber FROM Staff WHERE IsActive = 1 ORDER BY StaffID ASC");
    $stmt->execute();
    $staff = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "count" => count($staff),
        "data" => $staff
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to retrieve staff: " . $e->getMessage()
    ]);
}
?>