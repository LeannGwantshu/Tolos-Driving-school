<?php
session_start();
include 'data.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

$userId = $_SESSION['user_id']; // Get the logged-in user's ID

// Get the JSON input from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (!isset($data['carId']) || !is_numeric($data['carId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid car ID."]);
    exit();
}

$carId = $data['carId'];

// Begin a transaction
$conn->begin_transaction();

try {
    // Update the vehicle to mark it as unavailable
    $stmt = $conn->prepare("UPDATE Vehicles SET is_available = 0 WHERE id = ?");
    $stmt->bind_param("i", $carId);
    if (!$stmt->execute()) {
        throw new Exception("Error updating vehicle: " . $stmt->error);
    }
    $stmt->close();

    // Log the booking in the bookings table
    $stmt = $conn->prepare("INSERT INTO bookings (instructor_id, vehicle_id, booking_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $userId, $carId);
    if (!$stmt->execute()) {
        throw new Exception("Error logging booking: " . $stmt->error);
    }
    $stmt->close();

    // Commit the transaction
    $conn->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
