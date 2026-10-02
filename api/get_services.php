<?php
require_once 'db_connect.php';

try {
    $stmt = $conn->prepare("SELECT ServiceID, ServiceName, Description, Duration, Price FROM Service ORDER BY ServiceID ASC");
    $stmt->execute();
    $services = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "count" => count($services),
        "data" => $services
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to retrieve services: " . $e->getMessage()
    ]);
}
?>