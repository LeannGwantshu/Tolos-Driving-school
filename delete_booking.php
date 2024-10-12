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
if (!isset($data['bookingId']) || !isset($data['vehicleId']) || !is_numeric($data['bookingId']) || !is_numeric($data['vehicleId'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Invalid booking or vehicle ID."]);
    exit();
}

$bookingId = $data['bookingId'];
$vehicleId = $data['vehicleId'];

// Begin a transaction
$conn->begin_transaction();

try {
    // Delete the booking from the bookings table
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ? AND instructor_id = ?");
    $stmt->bind_param("ii", $bookingId, $userId);
    if (!$stmt->execute()) {
        throw new Exception("Error deleting booking: " . $stmt->error);
    }
    $stmt->close();

    // Update the vehicle to set it as available
    $stmt = $conn->prepare("UPDATE Vehicles SET is_available = 1 WHERE id = ?");
    $stmt->bind_param("i", $vehicleId);
    if (!$stmt->execute()) {
        throw new Exception("Error updating vehicle status: " . $stmt->error);
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
